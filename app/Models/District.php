<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\State; // Ensure State is imported
use App\Models\Zilla; // Ensure Zilla is imported

class District extends Model
{
    use HasFactory;

    protected $fillable = ['state_id', 'name'];

    // 1. Relationship to State (Parent)
    // A District belongs to one State
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    // 2. Relationship to Zilla (Child)
    // A District now has many Zillas (the new administrative sub-unit)
    public function zillas(): HasMany
    {
        return $this->hasMany(Zilla::class);
    }

    /*
    * The old 'pincodes()' relationship is REMOVED because Pincodes now belong to Zillas.
    * DO NOT include the old pincodes() function in your file.
    */
}
