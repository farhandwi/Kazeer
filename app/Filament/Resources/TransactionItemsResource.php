<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionItemsResource\Pages;
use App\Filament\Resources\TransactionItemsResource\RelationManagers;
use App\Models\TransactionItems;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;

class TransactionItemsResource extends Resource
{
    protected static ?string $model = TransactionItems::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static bool $shouldRegisterNavigation = false;

    public static function getRecordTitle(?Model $record): string|null|Htmlable
    {
        return $record->food?->name ?? 'Transaction Item';
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('transaction_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('foods_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Filter by transaction_id from URL parameter if present
        $transactionId = request()->query('transaction_id');
        
        return $table
            ->modifyQueryUsing(function (Builder $query) use ($transactionId) {
                if ($transactionId) {
                    $query->where('transaction_id', $transactionId);
                }
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('transaction.code')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('transaction.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('food.name')
                    ->label('Food Name')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Adding filter for transaction ID if needed as an additional filter
                SelectFilter::make('transaction_id')
                    ->label('Transaction')
                    ->relationship('transaction', 'code')
                    ->searchable()
                    ->preload()
                    ->visible(fn() => !request()->has('transaction_id')),
                    
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until ' . Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),

                SelectFilter::make('quick_filter')
                    ->label('Quick Filters')
                    ->options([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'last_week' => 'Last Week',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $filter = $data['value'] ?? null;
                        
                        if (!$filter) {
                            return $query;
                        }

                        return match ($filter) {
                            'today' => $query->whereDate('created_at', Carbon::today()),
                            'yesterday' => $query->whereDate('created_at', Carbon::yesterday()),
                            'this_week' => $query->whereBetween('created_at', [
                                Carbon::now()->startOfWeek(),
                                Carbon::now()->endOfWeek(),
                            ]),
                            'last_week' => $query->whereBetween('created_at', [
                                Carbon::now()->subWeek()->startOfWeek(),
                                Carbon::now()->subWeek()->endOfWeek(),
                            ]),
                            'this_month' => $query->whereMonth('created_at', Carbon::now()->month)
                                                 ->whereYear('created_at', Carbon::now()->year),
                            'last_month' => $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                                                 ->whereYear('created_at', Carbon::now()->subMonth()->year),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                // Tambahkan action untuk melihat receipt di sini jika diperlukan
                Tables\Actions\Action::make('view_receipt')
                    ->label('View Receipt')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (TransactionItems $record): string => TransactionResource::getUrl('receipt', ['record' => $record->transaction_id]))
                    ->visible(fn (TransactionItems $record): bool => in_array($record->transaction->payment_status ?? '', ['SUCCESS', 'PAID', 'SETTLED'])),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListTransactionItems::route('/'),
        ];
    }
}