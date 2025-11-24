<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'txn_id', 'closing_balance_month', 'total_qty', 'adjustment_details'
    ];
    
    protected $casts = [
        'closing_balance_month' => 'date',
        'adjustment_details' => 'array', // Cast to JSON array for storing book counts
    ];
}
