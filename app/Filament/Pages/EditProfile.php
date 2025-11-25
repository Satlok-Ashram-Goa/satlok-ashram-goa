<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EditProfile extends BaseEditProfile
{
    protected static string $view = 'filament-panels::pages.auth.edit-profile';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Information')
                    ->schema([
                        FileUpload::make('profile_picture_path')
                            ->label('Profile Picture')
                            ->image()
                            ->avatar()
                            ->directory('profile-pictures')
                            ->disk('public')
                            ->imageEditor()
                            ->circleCropper()
                            ->nullable()
                            ->maxSize(2048)
                            ->deleteUploadedFileUsing(function ($file) {
                                // Delete old profile picture when new one is uploaded
                                if ($file && Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }
                            })
                            ->columnSpanFull()
                            ->alignCenter(),
                        
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('last_name')
                            ->label('Last Name')  
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Change Password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->requiredWith('password')
                            ->currentPassword()
                            ->dehydrated(false),
                        
                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed()
                            ->nullable(),
                        
                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->dehydrated(false)
                            ->nullable(),
                    ])
                    ->columns(1),
            ]);
    }
    
    // Override to make the form wider
    public function hasInlineLabels(): bool
    {
        return false;
    }
    
    // Redirect to dashboard after saving profile
    protected function getRedirectUrl(): string
    {
        return url('/admin');
    }
}

