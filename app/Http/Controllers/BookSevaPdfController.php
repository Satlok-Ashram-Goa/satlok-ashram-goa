<?php

namespace App\Http\Controllers;

use App\Models\BookSeva;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BookSevaPdfController extends Controller
{
    public function download(BookSeva $record)
    {
        // 1. Load all necessary data to avoid "N+1" query performance issues
        // We need the items, the book details, the user who created it, and location data
        $record->load(['items.book', 'user', 'district', 'zilla', 'state']);

        // 2. Load the HTML view (which we will create in Step 3) and pass the record data to it
        $pdf = Pdf::loadView('pdf.book-seva', compact('record'));
        
        // 3. Stream the PDF to the browser (opens in a new tab)
        // You can change ->stream() to ->download() if you want it to save immediately
        return $pdf->stream('BookSeva-' . $record->txn_id . '.pdf');
    }
}
