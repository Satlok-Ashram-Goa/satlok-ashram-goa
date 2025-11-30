<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SamagariItemResource\Pages;
use App\Filament\Resources\SamagariItemResource\RelationManagers;
use App\Models\SamagariItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SamagariItemResource extends Resource
{
    protected static ?string $model = SamagariItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Samagari Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('sku_id')
                            ->label('SKU ID')
                            ->default(function () {
                                $nextId = SamagariItem::max('id') + 1;
                                return 'SA-GOA-' . (1000 + $nextId);
                            })
                            ->readOnly()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('Item Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->label('Sale Price')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Item Image')
                            ->image()
                            ->directory('samagari-items')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Allow for sale')
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
                Tables\Columns\TextColumn::make('sku_id')
                    ->label('SKU ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Item Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
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
            'index' => Pages\ListSamagariItems::route('/'),
            'create' => Pages\CreateSamagariItem::route('/create'),
            'edit' => Pages\EditSamagariItem::route('/{record}/edit'),
        ];
    }
}
