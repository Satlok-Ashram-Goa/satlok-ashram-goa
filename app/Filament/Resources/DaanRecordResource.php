<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaanRecordResource\Pages;
use App\Filament\Resources\DaanRecordResource\RelationManagers;
use App\Models\DaanRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DaanRecordResource extends Resource
{
    protected static ?string $model = DaanRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Paath / Jyoti Seva';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pledge Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('pledge_id')
                                    ->label('Pledge ID')
                                    ->default(function () {
                                        $nextId = \App\Models\DaanRecord::max('id') + 1;
                                        return 'P-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                                    })
                                    ->readOnly()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('pledge_date')
                                    ->label('Pledge Date')
                                    ->required()
                                    ->default(now()),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('bhagat_id')
                                    ->label('Bhagat ID')
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search) => \App\Models\Bhagat::where('user_id', 'like', "%{$search}%")
                                        ->orWhere('first_name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%")
                                        ->orWhere('mobile_no', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn ($bhagat) => [$bhagat->id => "{$bhagat->user_id} - {$bhagat->first_name} {$bhagat->last_name}"]))
                                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\Bhagat::find($value)?->user_id . ' - ' . \App\Models\Bhagat::find($value)?->first_name . ' ' . \App\Models\Bhagat::find($value)?->last_name)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('bhagat_name', \App\Models\Bhagat::find($state)?->first_name . ' ' . \App\Models\Bhagat::find($state)?->last_name)),
                                Forms\Components\TextInput::make('bhagat_name')
                                    ->label('Bhagat Name')
                                    ->disabled()
                                    ->dehydrated(false), // Display only
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('seva_master_id')
                                    ->label('Type of Seva')
                                    ->relationship('sevaMaster', 'name')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('original_amount', \App\Models\SevaMaster::find($state)?->amount)),
                                Forms\Components\TextInput::make('original_amount')
                                    ->label('Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₹')
                                    ->readOnly(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pledge_id')
                    ->label('Pledge ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pledge_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bhagat.user_id')
                    ->label('Bhagat ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bhagat.first_name')
                    ->label('Bhagat Name')
                    ->formatStateUsing(fn ($record) => $record->bhagat->first_name . ' ' . $record->bhagat->last_name)
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('sevaMaster.name')
                    ->label('Type of Seva')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'gray',
                        'In Progress' => 'warning',
                        'Completed' => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDaanRecords::route('/'),
            'create' => Pages\CreateDaanRecord::route('/create'),
            'edit' => Pages\EditDaanRecord::route('/{record}/edit'),
        ];
    }
}
