<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ZillaResource\Pages;
use App\Models\Zilla;
use App\Models\District; // Required for dependency logic
use App\Models\State; // Required for dependency logic
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ZillaResource extends Resource
{
    protected static ?string $model = Zilla::class;
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationIcon = 'heroicon-o-map'; 
    protected static ?int $navigationSort = 4;

    // ----------------------------------------------------------------------
    // ## Form Layout (State-District Dependent Selects)
    // ----------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. STATE SELECT (Filters the District)
                Select::make('state_id')
                    ->label('Select State')
                    ->options(State::all()->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('district_id', null))
                    ->default(function ($record) {
                        // When editing, populate state_id from the district relationship
                        return $record?->district?->state_id;
                    })
                    ->dehydrated(false) // Don't save this field to DB
                    ->required(),
                    
                // 2. DISTRICT SELECT (Dependent on State, saves district_id)
                Select::make('district_id')
                    ->label('District')
                    ->relationship('district', 'name') 
                    ->options(function (Get $get): Collection {
                        $stateId = $get('state_id');
                        if ($stateId) {
                            return District::where('state_id', $stateId)->pluck('name', 'id');
                        }
                        return collect();
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (Get $get) => !$get('state_id')),
                    
                // 3. ZILLA NAME INPUT (Now spans full width)
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(), // <-- FIX: Makes Name span two columns
            ])->columns(2); // Form uses two columns
    }

    // ----------------------------------------------------------------------
    // ## Table Layout
    // ----------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('district.name')
                    ->label('District')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('district.state.name')
                    ->label('State')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Zilla Name')
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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListZillas::route('/'),
            'create' => Pages\CreateZilla::route('/create'),
            'edit' => Pages\EditZilla::route('/{record}/edit'),
        ];
    }
}
