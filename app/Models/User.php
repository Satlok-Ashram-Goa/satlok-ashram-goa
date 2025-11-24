<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser; // <--- NEW: For Filament access control
use Filament\Panel;                       // <--- NEW: For Filament access control

class User extends Authenticatable implements FilamentUser // <--- NEW: Implement FilamentUser interface
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name', // <--- NEW: Added from migration
        'last_name',  // <--- NEW: Added from migration
        'email',
        'mobile_no',  // <--- NEW: Added from migration
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // --- NEW: Filament Access Control Method ---
    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // For simplicity and immediate access, we are allowing any user in the DB to access the panel.
        // In a real application, you might add logic like:
        // return str_ends_with($this->email, '@satlok.com');
        return true; 
    }
    // ------------------------------------------
}
