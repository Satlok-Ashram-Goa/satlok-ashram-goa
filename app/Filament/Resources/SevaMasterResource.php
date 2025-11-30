<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SevaMasterResource\Pages;
use App\Filament\Resources\SevaMasterResource\RelationManagers;
use App\Models\SevaMaster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SevaMasterResource extends Resource
{
    protected static ?string $model = SevaMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Paath / Jyoti Seva';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID')
                            ->disabled()
                            ->dehydrated(false) // Don't send to server
                            ->placeholder('Auto-generated'),
                        Forms\Components\TextInput::make('name')
                            ->label('Type of Seva')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status')
                            ->required()
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Type of Seva')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSevaMasters::route('/'),
            'create' => Pages\CreateSevaMaster::route('/create'),
            'edit' => Pages\EditSevaMaster::route('/{record}/edit'),
        ];
    }
}
