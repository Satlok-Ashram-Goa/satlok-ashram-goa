<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bhagat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'id_state_id', 'first_name', 'last_name', 'date_of_birth',
        'guardian_type', 'guardian_name', 'mobile_no', 'whatsapp_no',
        'email_id', 'aadhar_card_no', 
        'current_addr_line_1', 'current_addr_line_2', 
        'current_state_id', 'current_district_id', 'current_zilla_id', 'current_pincode',
        'same_as_current', 'perm_addr_line_1', 'perm_addr_line_2',
        'perm_state_id', 'perm_district_id', 'perm_zilla_id', 'perm_pincode',
        'photo_path', 'aadhar_front_path', 'aadhar_rear_path',
        'first_mantra_date', 'satnaam_mantra_date', 'sarnaam_mantra_date',
        'status', 'blacklist_status'
    ];

    // --- Relationships ---
    // ID Generation State (separate from address)
    public function idState(): BelongsTo { return $this->belongsTo(State::class, 'id_state_id'); }
    
    // Current Address Relationships
    public function currentState(): BelongsTo { return $this->belongsTo(State::class, 'current_state_id'); }
    public function currentDistrict(): BelongsTo { return $this->belongsTo(District::class, 'current_district_id'); }
    public function currentZilla(): BelongsTo { return $this->belongsTo(Zilla::class, 'current_zilla_id'); }
    
    // Permanent Address Relationships
    public function permState(): BelongsTo { return $this->belongsTo(State::class, 'perm_state_id'); }
    public function permDistrict(): BelongsTo { return $this->belongsTo(District::class, 'perm_district_id'); }
    public function permZilla(): BelongsTo { return $this->belongsTo(Zilla::class, 'perm_zilla_id'); }
}
