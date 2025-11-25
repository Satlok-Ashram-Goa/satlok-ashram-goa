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
        'profile_picture_path', // <--- NEW: Profile picture storage
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
            'last_login_at' => 'datetime',
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
    
    // --- NEW: Filament Profile Display Methods ---
    /**
     * Get the user's full name for display in Filament (top right)
     */
    public function getFilamentName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->email;
    }
    
    /**
     * Get the user's profile picture URL for Filament avatar
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_picture_path 
            ? asset('storage/' . $this->profile_picture_path)
            : null;
    }
    // ------------------------------------------
}
