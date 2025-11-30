<?php

namespace App\Models;

use App\Models\Bhagat;
use App\Models\SevaMaster;
use App\Models\SevaPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DaanRecord extends Model
{
    protected $fillable = [
        'pledge_id',
        'pledge_date',
        'bhagat_id',
        'seva_master_id',
        'original_amount',
        'status',
        'created_by',
    ];

    public function bhagat()
    {
        return $this->belongsTo(Bhagat::class);
    }

    public function sevaMaster()
    {
        return $this->belongsTo(SevaMaster::class);
    }

    public function payments()
    {
        return $this->hasMany(SevaPayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
