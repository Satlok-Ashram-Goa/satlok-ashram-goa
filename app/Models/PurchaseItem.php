<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'book_id', 'quantity', 'unit_price', 'line_total'
    ];

    // An Item belongs to a Purchase Master
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    // An Item belongs to a Book Master
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
