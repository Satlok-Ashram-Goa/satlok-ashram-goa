<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seva Receipt</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 2px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .details-table th {
            background-color: #f2f2f2;
        }
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }
        .transactions-table th, .transactions-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        .transactions-table th {
            background-color: #eee;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Satlok Ashram Goa</h1>
        <p>Seva Receipt</p>
    </div>

    <table class="details-table">
        <tr>
            <th>Pledge ID</th>
            <td>{{ $record->pledge_id }}</td>
            <th>Date</th>
            <td>{{ $record->pledge_date }}</td>
        </tr>
        <tr>
            <th>Bhagat Name</th>
            <td>{{ $record->bhagat->first_name }} {{ $record->bhagat->last_name }}</td>
            <th>Bhagat ID</th>
            <td>{{ $record->bhagat->id }}</td>
        </tr>
        <tr>
            <th>Seva Type</th>
            <td>{{ $record->sevaMaster->name }}</td>
            <th>Status</th>
            <td>{{ $record->status }}</td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td>₹{{ number_format($record->original_amount, 2) }}</td>
            <th>Paid Amount</th>
            <td>₹{{ number_format($record->payments->sum('amount'), 2) }}</td>
        </tr>
    </table>

    <h3>Payment History</h3>
    <table class="transactions-table">
        <thead>
            <tr>
                <th>Txn ID</th>
                <th>Date</th>
                <th>Type</th>
                <th>Location</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->payments as $payment)
            <tr>
                <td>{{ $payment->txn_id }}</td>
                <td>{{ $payment->txn_date }}</td>
                <td>{{ $payment->payment_type }}</td>
                <td>{{ $payment->collection_location }}</td>
                <td>₹{{ number_format($payment->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">Total Paid</th>
                <th>₹{{ number_format($record->payments->sum('amount'), 2) }}</th>
            </tr>
            <tr>
                <th colspan="4" style="text-align: right;">Balance Due</th>
                <th>₹{{ number_format($record->original_amount - $record->payments->sum('amount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Authorized Signatory</p>
    </div>
</body>
</html>
