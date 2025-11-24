<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction; // Necessary import for actions

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;
    protected static ?string $navigationGroup = 'Book Database';
    protected static ?string $navigationLabel = 'Book Master';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 1;

    // Define the list of languages for the dropdown outside the method
    protected static array $languages = [
        'Hindi' => 'Hindi',
        'English' => 'English',
        'Marathi' => 'Marathi',
        'Nepali' => 'Nepali',
        'Odia' => 'Odia',
        'Urdu' => 'Urdu',
        'Kannad' => 'Kannad',
        'Bengali' => 'Bengali',
        'Gujarati' => 'Gujarati',
        'Assamesse' => 'Assamesse',
        'Malyalam' => 'Malyalam',
        'Telugu' => 'Telugu',
        'Tamil' => 'Tamil',
        'Punjabi' => 'Punjabi',
    ];


    // ----------------------------------------------------------------------
    // ## Form Layout (Create/Edit Book)
    // ----------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sku_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'SKU-' . str_pad(Book::count() + 1, 4, '0', STR_PAD_LEFT))
                    ->maxLength(255),

                TextInput::make('name')
                    ->label('Book Name')
                    ->required()
                    ->columnSpanFull(),

                // --- FIX: Replaced TextInput with Select for predefined options ---
                Select::make('language')
                    ->options(self::$languages) // Use the predefined static array
                    ->searchable() 
                    ->required()
                    ->columnSpan(1),
                // ---------------------------------------------------------------
                
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₹')
                    ->step(0.01),

                Toggle::make('can_be_sold')
                    ->label('Can be Sold')
                    ->helperText('Enable this if the item is available for sale.')
                    ->default(true),
            ])->columns(2);
    }

    // ----------------------------------------------------------------------
    // ## Table Layout (Book Master List)
    // ----------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku_id')->label('SKU Id')->searchable()->sortable(),
                TextColumn::make('name')->label('Book Name')->searchable(),
                TextColumn::make('language')->searchable(),

                // Display price with Indian Rupees formatting
                TextColumn::make('price')
                    ->money('INR') 
                    ->label('Price')
                    ->sortable(),

                IconColumn::make('can_be_sold')
                    ->label('Available for Sale')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // FIX: Uses imported classes
                EditAction::make(), 
                DeleteAction::make(), 
            ])
            ->bulkActions([
                // FIX: Uses imported classes
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
