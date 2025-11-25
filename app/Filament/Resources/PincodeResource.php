<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PincodeResource\Pages;
use App\Models\Pincode;
use App\Models\District; 
use App\Models\State; 
use App\Models\Zilla; 

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PincodeResource extends Resource
{
    protected static ?string $model = Pincode::class;
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?int $navigationSort = 5;

    // ----------------------------------------------------------------------
    // ## Form Layout (3-Level Dependent Selects & Input Fix)
    // ----------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- LEVEL 1: STATE SELECT (Filter) ---
                Select::make('state_id')
                    ->label('Select State [DEPLOYMENT TEST - v1]')
                    ->options(State::all()->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('district_id', null);
                        $set('zilla_id', null);
                    })
                    ->afterStateHydrated(function (Set $set, $state, ?Model $record) {
                        // When editing, populate state_id from zilla -> district -> state
                        if (!$record) return;
                        
                        // Explicitly load relationships if needed
                        if (!$record->relationLoaded('zilla')) {
                            $record->load('zilla.district');
                        }
                        
                        if ($record->zilla?->district) {
                            $set('state_id', $record->zilla->district->state_id);
                        }
                    })
                    ->required()
                    ->dehydrated(false), // Virtual field, not saved to DB
                
                // --- LEVEL 2: DISTRICT SELECT (Filter) ---
                Select::make('district_id')
                    ->label('Select District')
                    ->options(function (Get $get): Collection {
                        $stateId = $get('state_id');
                        if ($stateId) {
                            return District::where('state_id', $stateId)->pluck('name', 'id');
                        }
                        return collect();
                    })
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('zilla_id', null))
                    ->afterStateHydrated(function (Set $set, $state, ?Model $record) {
                        // When editing, populate district_id from zilla -> district
                        if (!$record) return;
                        
                        // Explicitly load relationship if needed
                        if (!$record->relationLoaded('zilla')) {
                            $record->load('zilla');
                        }
                        
                        if ($record->zilla) {
                            $set('district_id', $record->zilla->district_id);
                        }
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (Get $get) => !$get('state_id'))
                    ->dehydrated(false), // Virtual field, not saved to DB

                // --- LEVEL 3: ZILLA SELECT (Saves zilla_id) ---
                Select::make('zilla_id')
                    ->label('Select Zilla')
                    // ->relationship('zilla', 'name') // REMOVED: Conflicts with options()
                    ->options(function (Get $get): Collection {
                        $districtId = $get('district_id');
                        if ($districtId) {
                            return Zilla::where('district_id', $districtId)->pluck('name', 'id');
                        }
                        return collect();
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (Get $get) => !$get('district_id')),
                    
                // --- FINAL INPUT: PINCODE (UI Input Restriction Added) ---
                TextInput::make('pincode')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->length(6) 
                    ->rules(['digits:6']) // Server-side validation
                    ->extraInputAttributes(['inputmode' => 'numeric', 'pattern' => '[0-9]*']) // UI Input Restriction
                    ->columnSpanFull(),
            ])->columns(3);
    }

    // ----------------------------------------------------------------------
    // ## Table Layout (List View)
    // ----------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Display Zilla Name
                TextColumn::make('zilla.name')
                    ->label('Zilla')
                    ->sortable()
                    ->searchable(),
                    
                // Display Parent District Name (via Zilla relationship)
                TextColumn::make('zilla.district.name')
                    ->label('District')
                    ->sortable()
                    ->searchable(),

                // Display Grandparent State Name (via Zilla -> District relationship)
                TextColumn::make('zilla.district.state.name')
                    ->label('State')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('pincode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('zilla.name', 'asc');
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
            'index' => Pages\ListPincodes::route('/'),
            'create' => Pages\CreatePincode::route('/create'),
            'edit' => Pages\EditPincode::route('/{record}/edit'),
        ];
    }
}
