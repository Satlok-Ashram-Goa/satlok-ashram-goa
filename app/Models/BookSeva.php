<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BookSevaItem;
use App\Models\District; 
use App\Models\Zilla;    
use App\Models\State;    // Import State

class BookSeva extends Model
{
    use HasFactory;

    protected $fillable = [
        'txn_id',
        'txn_date',
        'user_id',
        'state_id',    // Added
        'district_id', 
        'zilla_id',    
        'total_sevadaar',
        'total_qty',
        'total_amount',
    ];

    protected $casts = [
        'txn_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookSevaItem::class);
    }

    // --- Location Relationships ---

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function zilla(): BelongsTo
    {
        return $this->belongsTo(Zilla::class);
    }
}
