<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\District; 
use App\Models\Pincode; 

class Zilla extends Model
{
    use HasFactory;
    
    protected $fillable = ['district_id', 'name'];

    // A Zilla belongs to one District
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
    
    // A Zilla has many Pincodes
    public function pincodes(): HasMany
    {
        return $this->hasMany(Pincode::class);
    }
}
