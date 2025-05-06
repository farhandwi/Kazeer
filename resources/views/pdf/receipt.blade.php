<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $transaction->code }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #1a1a1a;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .receipt-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
        }

        .receipt-logo {
            max-width: 160px;
        }

        .receipt-meta {
            text-align: right;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice {
            font-size: 16px;
            font-weight: bold;
        }

        .date {
            font-size: 14px;
            color: #666;
        }

        .receipt-section {
            margin-bottom: 25px;
        }

        .receipt-section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #f3f4f6;
        }

        .receipt-customer-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .receipt-detail-column {
            flex: 1;
        }

        .receipt-detail-item {
            margin-bottom: 10px;
        }

        .receipt-detail-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 3px;
        }

        .receipt-detail-value {
            font-size: 14px;
            font-weight: 500;
        }

        .receipt-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .receipt-items th {
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }

        .receipt-items td {
            padding: 10px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
        }

        .right-align {
            text-align: right;
        }

        .receipt-totals {
            width: 100%;
            font-size: 12px;
        }

        .receipt-total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 5px;
        }

        .receipt-total-label {
            width: 150px;
            color: #666;
            text-align: right;
            padding-right: 10px;
        }

        .receipt-total-value {
            width: 100px;
            text-align: right;
            font-weight: 500;
        }

        .receipt-grand-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
            font-weight: bold;
        }

        .receipt-grand-total .receipt-total-label {
            color: #1a1a1a;
            font-size: 14px;
        }

        .receipt-grand-total .receipt-total-value {
            font-size: 14px;
            color: #0f766e;
        }

        .receipt-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eaeaea;
            padding-top: 20px;
        }

        .receipt-thank-you {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .receipt-barcode {
            text-align: center;
            margin-top: 20px;
        }

        .receipt-barcode img {
            max-width: 150px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-green {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-yellow {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .badge-red {
            background-color: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div>
                <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" class="receipt-logo">
            </div>

            <div class="receipt-meta">
                <div class="receipt-title">RECEIPT</div>
                <div class="invoice">#{{ $transaction->code }}</div>
                <div class="date">{{ $transaction->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>

        <div class="receipt-section">
            <div class="receipt-section-title">Customer Information</div>
            <div class="receipt-customer-details">
                <div class="receipt-detail-column">
                    <div class="receipt-detail-item">
                        <div class="receipt-detail-label">Customer Name</div>
                        <div class="receipt-detail-value">{{ $transaction->name }}</div>
                    </div>

                    <div class="receipt-detail-item">
                        <div class="receipt-detail-label">Phone Number</div>
                        <div class="receipt-detail-value">{{ $transaction->phone }}</div>
                    </div>
                </div>

                <div class="receipt-detail-column">
                    <div class="receipt-detail-item">
                        <div class="receipt-detail-label">Payment Method</div>
                        <div class="receipt-detail-value">{{ $transaction->payment_method }}</div>
                    </div>

                    <div class="receipt-detail-item">
                        <div class="receipt-detail-label">Payment Status</div>
                        <div class="receipt-detail-value">
                            <span
                                class="badge 
                                @if (in_array($transaction->payment_status, ['SUCCESS', 'PAID', 'SETTLED'])) badge-green
                                @elseif($transaction->payment_status == 'PENDING')
                                    badge-yellow
                                @else
                                    badge-red @endif
                            ">
                                {{ $transaction->payment_status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="receipt-section">
            <div class="receipt-section-title">Order Items</div>
            <table class="receipt-items">
                <thead>
                    <tr>
                        <th width="60%">Description</th>
                        <th width="10%">Qty</th>
                        <th width="15%" class="right-align">Price</th>
                        <th width="15%" class="right-align">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->food->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="right-align">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="right-align">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="receipt-section">
            <div class="receipt-totals">
                <div class="receipt-total-row">
                    <div class="receipt-total-label">Subtotal</div>
                    <div class="receipt-total-value">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</div>
                </div>

                <div class="receipt-total-row">
                    <div class="receipt-total-label">PPN (11%)</div>
                    <div class="receipt-total-value">Rp {{ number_format($transaction->ppn, 0, ',', '.') }}</div>
                </div>

                <div class="receipt-total-row receipt-grand-total">
                    <div class="receipt-total-label">Total</div>
                    <div class="receipt-total-value">Rp {{ number_format($transaction->total, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="receipt-footer">
            <div class="receipt-thank-you">Thank you for your purchase!</div>
            <p>If you have any questions about this receipt, please contact our customer service.</p>
            <div class="receipt-barcode">
                {!! DNS1D::getBarcodeHTML($transaction->code, 'C128') !!}
                <div style="margin-top: 5px; font-size: 10px;">{{ $transaction->code }}</div>
            </div>
        </div>
    </div>
</body>

</html>
