<?php

namespace App\Filament\Resources\TransactionItemsResource\Pages;

use App\Filament\Resources\TransactionItemsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

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
    
    // Override the table query to correctly filter by transaction_id
    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery();
        
        // Get transaction_id from the request
        $transactionId = request()->query('transaction_id');
        
        // Apply transaction_id filter if present
        if ($transactionId) {
            $query->where('transaction_id', $transactionId);
        }
        
        return $query;
    }
    
    // Ensure pagination maintains the transaction_id parameter
    protected function paginateTableQuery(Builder $query): Paginator|LengthAwarePaginator|CursorPaginator
    {
        $paginator = parent::paginateTableQuery($query);
        
        // Get transaction_id from the request
        $transactionId = request()->query('transaction_id');
        
        if ($transactionId) {
            // Add transaction_id to pagination links
            $paginator->appends(['transaction_id' => $transactionId]);
        }
        
        return $paginator;
    }
}