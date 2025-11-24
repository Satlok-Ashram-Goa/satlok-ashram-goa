<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Models\District;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// --- FIX: Missing Imports for Table Actions (Causing the Error) ---
use Filament\Tables\Actions\EditAction; 
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
// -----------------------------------------------------------------

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationIcon = 'heroicon-o-map'; 
protected static ?int $navigationSort = 3;

    // ----------------------------------------------------------------------
    // ## Form Layout (Fixed State Select)
    // ----------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // CORRECTED: Using Select component and relationship() to display State names
                Select::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name') // Links to the state() function in District Model
                    ->searchable()
                    ->required(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    // ----------------------------------------------------------------------
    // ## Table Layout (List View) - NOW USING CORRECT IMPORTS
    // ----------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // CORRECTED: Display related State Name
                TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('name')
                    ->label('District Name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // FIX: These now correctly refer to the imported classes (EditAction, DeleteAction)
                EditAction::make(), 
                DeleteAction::make(), 
            ])
            ->bulkActions([
                // FIX: These now correctly refer to the imported classes
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('state.name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
