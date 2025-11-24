<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Zilla; // CRITICAL: Import the Zilla model

class Pincode extends Model
{
    use HasFactory;
    
    // Fillable attributes updated to use 'zilla_id'
    protected $fillable = ['zilla_id', 'pincode'];

    // Relationship updated to link Pincode to Zilla
    // A Pincode now belongs to one Zilla
    public function zilla(): BelongsTo
    {
        return $this->belongsTo(Zilla::class);
    }
}
