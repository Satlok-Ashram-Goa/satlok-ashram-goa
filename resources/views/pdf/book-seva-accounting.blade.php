<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Book Seva Accounting Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1a1a1a;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .date-range {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .summary-row:last-child {
            margin-bottom: 0;
            font-weight: bold;
            font-size: 16px;
            padding-top: 10px;
            border-top: 2px solid #d1d5db;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Satlok Ashram Goa</h1>
        <p>Book Seva Accounting Report</p>
    </div>

    <div class="date-range">
        Period: {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Date</th>
                <th width="20%">Txn No</th>
                <th width="25%">Donation Type</th>
                <th width="15%" class="text-right">Total Qty</th>
                <th width="25%" class="text-right">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->txn_date)->format('d/m/Y') }}</td>
                    <td>{{ $transaction->txn_id }}</td>
                    <td class="text-center">
                        <span class="badge {{ $transaction->donation_type === 'Counter Sale' ? 'badge-success' : 'badge-info' }}">
                            {{ $transaction->donation_type }}
                        </span>
                    </td>
                    <td class="text-right">{{ number_format($transaction->total_qty) }}</td>
                    <td class="text-right">₹ {{ number_format($transaction->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No transactions found for the selected period</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span>Total Transactions:</span>
            <span>{{ $transactions->count() }}</span>
        </div>
        <div class="summary-row">
            <span>Total Quantity:</span>
            <span>{{ number_format($totalQty) }}</span>
        </div>
        <div class="summary-row">
            <span>Total Amount:</span>
            <span>₹ {{ number_format($totalAmount, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Satlok Ashram Goa - Book Seva Accounting System</p>
    </div>
</body>
</html>
