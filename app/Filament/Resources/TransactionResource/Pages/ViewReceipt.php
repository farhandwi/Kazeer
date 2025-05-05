<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewReceipt extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print Receipt')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => '#')
                ->openUrlInNewTab()
                ->extraAttributes([
                    'onclick' => 'window.print(); return false;',
                ]),
                
            Action::make('back')
                ->label('Back to Transactions')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => TransactionResource::getUrl('index')),
        ];
    }
}