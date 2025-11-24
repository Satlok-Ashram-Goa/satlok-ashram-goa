<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    // --- MERGED: Grouping the resource in the sidebar ---
    protected static ?string $navigationGroup = 'System Settings';
    // -----------------------------------------------------
    
    // Set the navigation label in the sidebar
    protected static ?string $navigationLabel = 'User Database'; 
protected static ?int $navigationSort = 1;
    // ----------------------------------------------------------------------
    // ## Form Layout (Create & Edit)
    // ----------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('mobile_no')
                    ->tel()
                    ->nullable()
                    ->maxLength(20),
                
                // CRUCIAL: Password Field Setup for Hashing
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // Encrypt the password
                    ->dehydrated(fn ($state) => filled($state)) // Only save if the field is filled
                    ->required(fn (string $context): bool => $context === 'create') // Required only on 'Create'
                    ->hint('Required on creation. Leave blank to keep current password.'),

                // Read-Only Last Login Field
                TextInput::make('last_login_at')
                    ->label('Last Login')
                    ->disabled()
                    ->default('N/A')
                    ->visibleOn('edit'),
            ])->columns(2);
    }

    // ----------------------------------------------------------------------
    // ## Table Layout (List View) - BUG-FIXED!
    // ----------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mobile_no')
                    ->searchable(),
                
                // Last Login Column (Uses placeholder for NULL values)
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime() 
                    ->placeholder('Never logged in')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Registered On')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Define any filters here
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ----------------------------------------------------------------------
    // ## Page Routes
    // ----------------------------------------------------------------------
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
