<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;

class ViewReceipt extends ViewRecord
{
    protected static string $resource = TransactionResource::class;
    
    // Tambahkan CSS kustom untuk tampilan receipt yang lebih modern
    protected function getHeaderActions(): array
    {
        return [                
            Action::make('download')
                ->label('Download Receipt')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => route('receipt.download', ['transaction' => $this->record->id]))
                ->openUrlInNewTab(),
                
            Action::make('back')
                ->label('Back to Transactions')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => TransactionResource::getUrl('index')),
        ];
    }
    
    public function getTitle(): string
    {
        return "Receipt #{$this->record->code}";
    }
    
    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
    
    public function getBreadcrumbs(): array
    {
        return [];
    }
    
    // Mengubah access level menjadi public sesuai dengan kelas parent
    public function getFooter(): ?View
    {
        return view('filament.receipt-styles');
    }
}