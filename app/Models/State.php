<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'code'];

    // A State has many Districts
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }
}
