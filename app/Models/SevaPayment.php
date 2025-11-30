<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DaanRecord;
use App\Models\User;

class SevaPayment extends Model
{
    protected $fillable = [
        'txn_id',
        'daan_record_id',
        'txn_date',
        'payment_type',
        'amount',
        'collection_location',
        'created_by',
    ];

    public function daanRecord()
    {
        return $this->belongsTo(DaanRecord::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
