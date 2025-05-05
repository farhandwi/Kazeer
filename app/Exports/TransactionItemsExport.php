<?php

namespace App\Exports;

use App\Models\TransactionItems;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class TransactionItemsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    protected $filters;
    protected $rowNumber = 0;
    protected $totalAmount = 0;
    protected $totalQuantity = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Transaction Items';
    }

    public function query()
    {
        $query = TransactionItems::query()
            ->with(['transaction', 'food'])
            ->orderBy('created_at', 'desc');

        if (isset($this->filters['period'])) {
            switch ($this->filters['period']) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'custom':
                    if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
                        $query->whereBetween('created_at', [
                            Carbon::parse($this->filters['start_date'])->startOfDay(),
                            Carbon::parse($this->filters['end_date'])->endOfDay(),
                        ]);
                    }
                    break;
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Transaction ID',
            'Transaction Code',
            'Food Name',
            'Quantity',
            'Price',
            'Subtotal',
            'Transaction Date',
            'Created At',
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;
        $this->totalAmount += $item->subtotal;
        $this->totalQuantity += $item->quantity;

        return [
            $this->rowNumber,
            $item->transaction_id,
            $item->transaction->code ?? 'N/A',
            $item->food->name ?? 'N/A',
            $item->quantity,
            $item->price,
            $item->subtotal,
            $item->transaction ? Carbon::parse($item->transaction->created_at)->format('Y-m-d H:i:s') : 'N/A',
            Carbon::parse($item->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Apply border to all cells
            'A1:I' . ($sheet->getHighestRow() + 3) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                
                // Add summary rows
                $summaryRow = $highestRow + 2;
                $sheet->setCellValue('A' . $summaryRow, 'TOTAL');
                $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);
                $sheet->setCellValue('E' . $summaryRow, $this->totalQuantity);
                $sheet->setCellValue('G' . $summaryRow, $this->totalAmount);
                
                // Style the summary row
                $sheet->getStyle('A' . $summaryRow . ':I' . $summaryRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F2F2'],
                    ],
                ]);
                
                // Format currency columns
                $sheet->getStyle('F2:F' . $summaryRow)
                    ->getNumberFormat()
                    ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');
                
                $sheet->getStyle('G2:G' . $summaryRow)
                    ->getNumberFormat()
                    ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');
                
                // Add export info
                $infoRow = $summaryRow + 2;
                $sheet->setCellValue('A' . $infoRow, 'Exported on: ' . Carbon::now()->format('Y-m-d H:i:s'));
                $sheet->mergeCells('A' . $infoRow . ':I' . $infoRow);
                $sheet->getStyle('A' . $infoRow)->getFont()->setItalic(true);
                
                // Add period info if available
                if (isset($this->filters['period'])) {
                    $periodRow = $infoRow + 1;
                    $periodText = 'Period: ';
                    
                    switch ($this->filters['period']) {
                        case 'today':
                            $periodText .= 'Today (' . Carbon::today()->format('Y-m-d') . ')';
                            break;
                        case 'this_week':
                            $periodText .= 'This Week (' . Carbon::now()->startOfWeek()->format('Y-m-d') . ' to ' . Carbon::now()->endOfWeek()->format('Y-m-d') . ')';
                            break;
                        case 'this_month':
                            $periodText .= 'This Month (' . Carbon::now()->format('F Y') . ')';
                            break;
                        case 'custom':
                            if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
                                $periodText .= 'Custom (' . Carbon::parse($this->filters['start_date'])->format('Y-m-d') . ' to ' . Carbon::parse($this->filters['end_date'])->format('Y-m-d') . ')';
                            }
                            break;
                    }
                    
                    $sheet->setCellValue('A' . $periodRow, $periodText);
                    $sheet->mergeCells('A' . $periodRow . ':I' . $periodRow);
                    $sheet->getStyle('A' . $periodRow)->getFont()->setItalic(true);
                }
                
                // Adjust column widths
                foreach (range('A', 'I') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}