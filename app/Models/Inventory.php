<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Book; // Ensure the Book model is imported

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id', 
        'current_stock_qty'
    ];
    
    protected $casts = [
        'current_stock_qty' => 'integer',
    ];

    /**
     * An Inventory record belongs to one Book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
