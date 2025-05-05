<?php

namespace App\Filament\Resources\TransactionItemsResource\Pages;

use App\Filament\Resources\TransactionItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Transaction;

class ListTransactionItems extends ListRecords
{
    protected static string $resource = TransactionItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Transactions')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.admin.resources.transactions.index')),
        ];
    }
    
    // Override getTableHeading to show transaction details in the page header
    protected function getTableHeading(): string|null
    {
        $transactionId = request()->query('transaction_id');
        
        if ($transactionId) {
            $transaction = Transaction::find($transactionId);
            
            if ($transaction) {
                return "Transaction Items - Invoice #{$transaction->code} - {$transaction->name}";
            }
        }
        
        return "Transaction Items";
    }
}