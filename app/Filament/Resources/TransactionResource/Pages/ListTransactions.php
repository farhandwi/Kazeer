<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionItemsResource;
use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionItemsExport;
use Illuminate\Database\Eloquent\Model;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Grid::make(2)
                        ->schema([
                            Select::make('period')
                                ->label('Period')
                                ->options([
                                    'today' => 'Today',
                                    'this_week' => 'This Week',
                                    'this_month' => 'This Month',
                                    'custom' => 'Custom Range',
                                ])
                                ->default('today')
                                ->reactive()
                                ->required(),
                            DatePicker::make('start_date')
                                ->label('Start Date')
                                ->visible(fn ($get) => $get('period') === 'custom')
                                ->required(fn ($get) => $get('period') === 'custom'),
                            DatePicker::make('end_date')
                                ->label('End Date')
                                ->visible(fn ($get) => $get('period') === 'custom')
                                ->required(fn ($get) => $get('period') === 'custom')
                                ->minDate(fn ($get) => $get('start_date')),
                        ])
                ])
                ->action(function (array $data) {
                    $fileName = 'transaction-items-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
                    
                    // Show notification
                    Notification::make()
                        ->title('Export started')
                        ->body('Your export is being processed...')
                        ->success()
                        ->send();
                    
                    return Excel::download(
                        new TransactionItemsExport($data),
                        $fileName
                    );
                }),
            
            // Only show CreateAction if you want users to be able to create items
            // Actions\CreateAction::make(),
        ];
    }
}
