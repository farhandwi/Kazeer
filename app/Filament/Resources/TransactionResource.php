<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionItemsResource\Pages\CreateTransactionItems;
use App\Filament\Resources\TransactionItemsResource\Pages\EditTransactionItems;
use App\Filament\Resources\TransactionItemsResource\Pages\ListTransactionItems;

use App\Models\TransactionItems;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getRecordTitle(?Model $record): string|null|Htmlable
    {
        return $record->name;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('external_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('checkout_link')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('barcodes_id')
                    ->label('QR Code')
                    ->image() // Hanya menerima file gambar
                    ->directory('qr_code') // Direktori penyimpanan
                    ->disk('public') // Disk penyimpanan
                    ->default(function ($record) {
                        return $record->barcodes->image ?? null;
                    }),
                Forms\Components\TextInput::make('payment_method')
                    ->required(),
                Forms\Components\TextInput::make('payment_status')
                    ->required(),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ppn')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Transaction Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('barcodes.image')
                    ->label('Barcode'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->colors([
                        'success' => fn ($state): bool => in_array($state, ['SUCCESS', 'PAID', 'SETTLED']),
                        'warning' => fn ($state): bool => $state === 'PENDING',
                        'danger' => fn ($state): bool => in_array($state, ['FAILED', 'EXPIRED']),
                    ]),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('ppn')
                    ->label('PPN')
                    ->numeric()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->numeric()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                // Ganti action untuk invoice/struk
                Action::make('view_receipt')
                    ->icon('heroicon-o-document-text')
                    ->label('Receipt')
                    ->color('success')
                    // Hanya tampilkan untuk transaksi dengan status PAID/SUCCESS/SETTLED
                    ->visible(fn (Transaction $record): bool => in_array($record->payment_status, ['SUCCESS', 'PAID', 'SETTLED']))
                    ->url(fn (Transaction $record): string => static::getUrl('receipt', ['record' => $record->id])),
                
                // Perbaiki action "See transaction" agar hanya menampilkan items dari transaksi ini
                Action::make('see_transaction')
                    ->icon('heroicon-o-eye')
                    ->label('Transaction Items')
                    ->color('primary')
                    ->url(fn (Transaction $record): string => route('filament.admin.resources.transaction-items.index', ['transaction_id' => $record->id])),
            ])
            ->bulkActions([]);
    }

    // Tambahkan method untuk infolist yang akan digunakan pada halaman receipt
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Receipt')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Invoice Number')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large),
                        
                        TextEntry::make('created_at')
                            ->label('Date')
                            ->date('d M Y H:i'),
                        
                        TextEntry::make('name')
                            ->label('Customer Name'),
                        
                        TextEntry::make('phone')
                            ->label('Phone Number'),
                        
                        TextEntry::make('payment_method')
                            ->label('Payment Method'),
                        
                        TextEntry::make('payment_status')
                            ->label('Payment Status')
                            ->badge(),
                    ])
                    ->columns(2),
                
                Section::make('Items')
                    ->schema([
                        RepeatableEntry::make('transactionItems')
                            ->schema([
                                TextEntry::make('food.name')
                                    ->label('Item'),
                                
                                TextEntry::make('quantity')
                                    ->label('Qty'),
                                
                                TextEntry::make('price')
                                    ->label('Price')
                                    ->money('IDR'),
                                
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('IDR'),
                            ])
                            ->columns(4),
                    ]),
                
                Section::make('Total')
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('IDR'),
                        
                        TextEntry::make('ppn')
                            ->label('PPN (Tax)')
                            ->money('IDR'),
                        
                        TextEntry::make('total')
                            ->label('Total')
                            ->money('IDR')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntry\TextEntrySize::Large),
                    ])
                    ->columns(1),
                
                Section::make('QR Code')
                    ->schema([
                        ImageEntry::make('barcodes.image')
                            ->label('Payment QR'),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'receipt' => Pages\ViewReceipt::route('/{record}/receipt'),
        ];
    }
}