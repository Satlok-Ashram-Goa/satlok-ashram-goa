<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'txn_id', 'txn_date', 'bhagat_id', 'total_qty', 'total_amount'
    ];

    protected $casts = [
        'txn_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function bhagat(): BelongsTo
    {
        return $this->belongsTo(Bhagat::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
