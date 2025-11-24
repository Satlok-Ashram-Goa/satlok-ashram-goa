<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Imports for Inventory Relationship
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Inventory; // Ensure the Inventory model is imported

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku_id',
        'name',
        'language',
        'price',
        'can_be_sold',
    ];
    
    protected $casts = [
        'can_be_sold' => 'boolean', // Cast the database integer to a PHP boolean
    ];

    /**
     * Get the Inventory record associated with this Book.
     * This links to the authoritative stock count.
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
