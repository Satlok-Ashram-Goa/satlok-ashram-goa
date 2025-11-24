<!DOCTYPE html>
<html>
<head>
    <title>Book Seva Report</title>
    <!-- FIX: Specify UTF-8 encoding for Dompdf to correctly handle Unicode characters like the Rupee symbol -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
    <style>
        /* General Body Style */
        /* FIX: Use a font that Dompdf knows supports the Rupee symbol (e.g., DejaVu Sans) */
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #000; }
        .container { width: 100%; margin: 0 auto; }
        
        /* Header Table Styling */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { border: 1px solid #000; padding: 8px; vertical-align: middle; }
        .header-title { font-weight: bold; font-size: 20px; text-align: center; text-transform: uppercase; }
        
        /* Items Table Styling */
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 6px; text-align: center; }
        
        /* Yellow Header Background */
        .items-table th { background-color: #fef3c7; font-weight: bold; } 
        
        /* Text Alignments */
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        
        /* Footer Styling */
        .footer-row td { font-weight: bold; background-color: #f3f4f6; }
        
        /* Logo Area */
        .logo-cell { width: 20%; text-align: center; }
        .logo-placeholder { font-weight: bold; color: #b91c1c; }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Section Table -->
    <table class="header-table">
        <tr>
            <!-- Logo Cell -->
            <td rowspan="4" class="logo-cell">
                <!-- Replace with <img src="{{ public_path('logo.png') }}" width="80"> if you have a file -->
                <div class="logo-placeholder">
                    <h3>SATLOK<br>ASHRAM<br>GOA</h3>
                </div>
            </td>
            <!-- Title Cell -->
            <td colspan="2" class="header-title">BOOK SEVA REPORT</td>
        </tr>
        <tr>
            <td width="30%"><strong>BOOK SEVA RECEIPT NO</strong></td>
            <td>{{ $record->txn_id }}</td>
        </tr>
        <tr>
            <td><strong>DATE</strong></td>
            <td>{{ $record->txn_date->format('d-M-Y') }}</td>
        </tr>
        <tr>
            <td><strong>BOOK SEVADAR</strong></td>
            <td>{{ $record->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <!-- Empty cell under logo -->
            <td class="logo-cell"></td>
            <td><strong>TOTAL SEVADAR</strong></td>
            <td>{{ $record->total_sevadaar }} Bhagat</td>
        </tr>
        <tr>
            <!-- Empty cell under logo -->
            <td class="logo-cell"></td>
            <td><strong>BOOK SEVA LOCATION</strong></td>
            <td>
                {{ $record->zilla->name ?? '' }} {{ $record->district->name ?? '' }} {{ $record->state->name ?? '' }}
            </td>
        </tr>
    </table>

    <!-- Items Section Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">SL</th>
                <th width="40%" class="text-left">BOOK NAME</th>
                <th width="10%">SEVA</th>
                <th width="10%">FREE</th>
                <th width="10%">TOTAL</th>
                <th width="10%">PRICE</th>
                <th width="15%">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->book->name ?? '-' }}</td>
                <!-- Logic: Mapping Qty to Seva column. Free column is 0 for now. -->
                <td>{{ $item->quantity }}</td> 
                <td>0</td>
                <td>{{ $item->quantity }}</td>
                <!-- Using HTML entity -->
                <td>&#8377; {{ number_format($item->price, 2) }}</td>
                <td>&#8377; {{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
            
            <!-- Empty rows to ensure the table looks full (Optional) -->
            @for($i = 0; $i < max(0, 5 - count($record->items)); $i++)
            <tr>
                <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr class="footer-row">
                <td colspan="4" class="text-right" style="padding-right: 15px;">TOTAL A</td>
                <td>{{ $record->total_qty }}</td>
                <td>--</td>
                <td>&#8377; {{ number_format($record->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

</body>
</html>
