<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookSevaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_seva_id',
        'book_id',
        'quantity',
        'price',
        'amount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function bookSeva(): BelongsTo
    {
        return $this->belongsTo(BookSeva::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
