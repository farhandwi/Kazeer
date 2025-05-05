<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodsResource\Pages;
use App\Filament\Resources\FoodsResource\RelationManagers;
use App\Models\Foods;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Collection;

class FoodsResource extends Resource
{
    protected static ?string $model = Foods::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('foods')
                    ->required()
                    ->columnSpanFull(),
                
                // Toggle untuk mengaktifkan makanan
                Forms\Components\Toggle::make('is_active')
                    ->label('Is Active')
                    ->helperText('Active foods will be shown to customers')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true)
                    ->columnSpanFull(),
                
                // Toggle untuk status ketersediaan stok
                Forms\Components\Toggle::make('is_available')
                    ->label('Stock Available')
                    ->helperText('Toggle off if the food is out of stock')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->columnSpanFull()
                    ->prefix('Rp')
                    ->reactive(),
                
                // Toggle untuk mengaktifkan detail promo
                Forms\Components\Toggle::make('show_promo_details')
                    ->label('Is Promo')
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(false)
                    ->columnSpanFull()
                    ->reactive()
                    // Set nilai default toggle berdasarkan keberadaan promo_start_at dan promo_end_at
                    ->dehydrated(false) // Tidak perlu disimpan ke database
                    ->afterStateHydrated(function ($set, $state, $record) {
                        // Jika record ada (edit mode) dan memiliki tanggal promo
                        if ($record && $record->promo_start_at && $record->promo_end_at) {
                            $set('show_promo_details', true);
                        }
                    }),
                
                // Section Promo yang selalu muncul
                Section::make('Promo Details')
                    ->schema([
                        Forms\Components\Select::make('percent')
                            ->label('Discount Percentage')
                            ->options([
                                10 => '10%',
                                25 => '25%',
                                35 => '35%',
                                50 => '50%',
                            ])
                            ->columnSpanFull()
                            ->reactive()
                            ->disabled(fn($get) => !$get('show_promo_details'))
                            ->afterStateUpdated(function ($set, $get, $state) {
                                if ($get('show_promo_details') && $get('price') && $state) {
                                    $discount = ($get('price') * (int)$state) / 100;
                                    $set('price_afterdiscount', $get('price') - $discount);
                                } else {
                                    $set('price_afterdiscount', $get('price'));
                                }
                            })
                            ->afterStateHydrated(function ($set, $state, $record) {
                                // Re-calculate price after discount when form is loaded
                                if ($record && $record->price && $state) {
                                    $discount = ($record->price * (int)$state) / 100;
                                    $set('price_afterdiscount', $record->price - $discount);
                                }
                            }),

                        Forms\Components\TextInput::make('price_afterdiscount')
                            ->label('Price After Discount')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->columnSpanFull()
                            ->disabled(fn($get) => !$get('show_promo_details')),
                        
                        Forms\Components\DateTimePicker::make('promo_start_at')
                            ->label('Promo Start Date and Time')
                            ->columnSpanFull()
                            ->disabled(fn($get) => !$get('show_promo_details')),
                        
                        Forms\Components\DateTimePicker::make('promo_end_at')
                            ->label('Promo End Date and Time')
                            ->columnSpanFull()
                            ->after('promo_start_at')
                            ->disabled(fn($get) => !$get('show_promo_details')),
                    ])
                    ->hidden(fn($get) => !$get('show_promo_details'))
                    ->mutateDehydratedStateUsing(function ($state, $get) {
                        // Reset nilai promo saat toggle dimatikan
                        if (!$get('show_promo_details')) {
                            return [
                                'percent' => null,
                                'price_afterdiscount' => null,
                                'promo_start_at' => null,
                                'promo_end_at' => null,
                            ];
                        }
                        
                        return $state;
                    }),
                
                Forms\Components\Select::make('categories_id')
                    ->label('Category')
                    ->options(function() {
                        $categoriesWithCount = Foods::getProductCountByCategory();
                        return $categoriesWithCount->pluck('name', 'id')
                            ->map(function($name, $id) use ($categoriesWithCount) {
                                $count = $categoriesWithCount->where('id', $id)->first()->product_count;
                                return "{$name} ({$count} items)";
                            });
                    })
                    ->required()
                    ->columnSpanFull()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_afterdiscount')
                    ->money('IDR')
                    ->sortable(),
                
                // Toggle untuk status ketersediaan stok
                Tables\Columns\ToggleColumn::make('is_available')
                    ->label('Stock')
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignCenter()
                    ->sortable(),
                
                // Toggle untuk status aktif/nonaktif
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignCenter()
                    ->sortable(),
                
                // Status badge untuk ketersediaan stok
                Tables\Columns\BadgeColumn::make('stock_status')
                    ->label('Stock Status')
                    ->getStateUsing(fn (Foods $record): string => $record->is_available ? 'Available' : 'Out of Stock')
                    ->colors([
                        'success' => fn ($state): bool => $state === 'Available',
                        'danger' => fn ($state): bool => $state === 'Out of Stock',
                    ]),
                
                // Kolom promo yang bergantung pada tanggal
                Tables\Columns\BadgeColumn::make('promo_status')
                    ->label('Promo Status')
                    ->getStateUsing(function (Foods $record) {
                        // Jika tidak ada tanggal promo, berarti tidak ada promo
                        if (!$record->promo_start_at || !$record->promo_end_at) {
                            return 'No Promo';
                        }
                        
                        $now = now();
                        // Cek apakah tanggal promo valid
                        if ($record->promo_start_at <= $now && $record->promo_end_at >= $now) {
                            return 'Active';
                        }
                        
                        if ($record->promo_start_at > $now) {
                            return 'Upcoming';
                        }
                        
                        return 'Expired';
                    })
                    ->colors([
                        'danger' => 'No Promo',
                        'warning' => 'Upcoming',
                        'success' => 'Active',
                        'secondary' => 'Expired',
                    ]),
                
                Tables\Columns\TextColumn::make('percent')
                    ->label('Discount %')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter berdasarkan kategori
                SelectFilter::make('categories_id')
                    ->label('Category')
                    ->options(function() {
                        $categoriesWithCount = Foods::getProductCountByCategory();
                        return $categoriesWithCount->pluck('name', 'id')
                            ->map(function($name, $id) use ($categoriesWithCount) {
                                $count = $categoriesWithCount->where('id', $id)->first()->product_count;
                                return "{$name} ({$count} items)";
                            });
                    })
                    ->searchable(),
                
                // Filter untuk status
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'all' => 'All',
                    ])
                    ->default('all')
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value'] || $data['value'] === 'all') {
                            return $query;
                        }
                        
                        return $query->where('is_active', $data['value'] === 'active');
                    }),
                
                // Filter untuk ketersediaan stok
                SelectFilter::make('availability')
                    ->label('Stock')
                    ->options([
                        'available' => 'Available',
                        'outofstock' => 'Out of Stock',
                        'all' => 'All',
                    ])
                    ->default('all')
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value'] || $data['value'] === 'all') {
                            return $query;
                        }
                        
                        return $query->where('is_available', $data['value'] === 'available');
                    }),
                
                // Filter untuk promo aktif
                SelectFilter::make('promo_status')
                    ->label('Promo Status')
                    ->options([
                        'active' => 'Active Promo',
                        'upcoming' => 'Upcoming Promo',
                        'expired' => 'Expired Promo',
                        'no_promo' => 'No Promo',
                        'all' => 'All',
                    ])
                    ->default('all')
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value'] || $data['value'] === 'all') {
                            return $query;
                        }
                        
                        $now = now();
                        return match($data['value']) {
                            'active' => $query->where('promo_start_at', '<=', $now)
                                ->where('promo_end_at', '>=', $now),
                            'upcoming' => $query->where('promo_start_at', '>', $now),
                            'expired' => $query->where('promo_end_at', '<', $now),
                            'no_promo' => $query->whereNull('promo_start_at')
                                ->orWhereNull('promo_end_at'),
                            default => $query,
                        };
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Bulk action untuk mengaktifkan/menonaktifkan item
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Set Active')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Set Inactive')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false])),
                    // Bulk action untuk mengatur ketersediaan stok
                    Tables\Actions\BulkAction::make('setAvailable')
                        ->label('Set Stock Available')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_available' => true])),
                    Tables\Actions\BulkAction::make('setUnavailable')
                        ->label('Set Out of Stock')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['is_available' => false])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFoods::route('/'),
            'create' => Pages\CreateFoods::route('/create'),
            'edit' => Pages\EditFoods::route('/{record}/edit'),
        ];
    }
}