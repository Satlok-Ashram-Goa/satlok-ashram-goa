<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'txn_id', 'txn_date', 'supplier_name', 'invoice_no', 'vehicle_no', 
        'total_qty', 'total_amount', 'invoice_copy_path'
    ];
    
    // ... rest of $casts array

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
    
    // --- ELOQUENT EVENT FIX ---
    protected static function booted()
    {
        static::saving(function (Purchase $purchase) {
            // Check if items relationship exists (it does when using Repeater)
            if ($purchase->relationLoaded('items')) {
                // Calculate totals from the related PurchaseItem models
                $purchase->total_qty = $purchase->items->sum('quantity');
                $purchase->total_amount = $purchase->items->sum('line_total');
            }
        });
    }
    // --------------------------
}
