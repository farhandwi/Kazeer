<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Download receipt PDF for a transaction.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, Transaction $transaction)
    {
        // Periksa apakah transaksi memiliki status paid
        if (!in_array($transaction->payment_status, ['SUCCESS', 'PAID', 'SETTLED'])) {
            abort(403, 'Receipt only available for paid transactions');
        }
        
        $items = $transaction->transactionItems()->with('transaction', 'food')->get();
        
        $pdf = Pdf::loadView('pdf.receipt', [
            'transaction' => $transaction,
            'items' => $items
        ]);
        
        return $pdf->download("receipt_{$transaction->code}.pdf");
    }
}