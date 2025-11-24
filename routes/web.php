<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookSevaPdfController; // Move use statement up

Route::get('/', function () {
    return view('welcome');
});

// --- START of Book Seva PDF Download Route ---
// Route to handle the PDF download action
// It links the URL /admin/book-sevas/{record}/pdf to the download method in the controller.
Route::get('/admin/book-sevas/{record}/pdf', [BookSevaPdfController::class, 'download'])
    ->name('book-sevas.pdf')
    ->middleware('auth'); 
// --- END of Book Seva PDF Download Route ---
