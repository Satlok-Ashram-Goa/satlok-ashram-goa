<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SevaMaster extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'is_active',
    ];
}
