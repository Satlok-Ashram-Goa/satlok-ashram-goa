<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use App\Models\Book;
use App\Models\Bhagat;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

use Illuminate\Support\Collection;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationGroup = 'Book Database';
    protected static ?string $navigationLabel = 'Counter Sale';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- SECTION 1: TRANSACTION HEADER ---
                Section::make('Transaction Details')
                    ->schema([
                        TextInput::make('txn_id')
                            ->label('Txn ID')
                            ->default(fn () => 'SALE-' . str_pad(Sale::count() + 1, 6, '0', STR_PAD_LEFT))
                            ->readOnly(),

                        DatePicker::make('txn_date')
                            ->label('Date')
                            ->required()
                            ->default(now()),

                        // Searchable Bhagat Dropdown
                        Select::make('bhagat_id')
                            ->label('Select Bhagat')
                            ->options(Bhagat::all()->mapWithKeys(function ($bhagat) {
                                return [$bhagat->id => "{$bhagat->first_name} {$bhagat->last_name} ({$bhagat->user_id})"];
                            }))
                            ->searchable()
                            ->required(),
                    ])->columns(3),

                // --- SECTION 2: BOOK LINE ITEMS ---
                Section::make('Book Selection')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                // 1. BOOK SELECTION
                                Select::make('book_id')
                                    ->label('Book')
                                    ->options(Book::where('can_be_sold', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        // Update price whenever book changes
                                        self::updateLineItemLogic($state, $get('purpose'), $set, $get);
                                    })
                                    ->columnSpan(3),

                                // 2. PURPOSE SELECTION
                                Select::make('purpose')
                                    ->options([
                                        'Seva' => 'Seva (Paid)',
                                        'SMS' => 'SMS (Limit 1, Free)',
                                        'Free' => 'Free (No Limit, Free)',
                                    ])
                                    ->required()
                                    ->default('Seva')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        // Update logic whenever purpose changes
                                        self::updateLineItemLogic($get('book_id'), $state, $set, $get);
                                    })
                                    ->columnSpan(2),

                                // 3. QUANTITY
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live()
                                    ->debounce('500ms')
                                    // Rule: Disable Qty if Purpose is SMS (forced to 1)
                                    ->disabled(fn (Get $get) => $get('purpose') === 'SMS')
                                    // FIX: dehydrated() ensures the value (1) is sent even if disabled
                                    ->dehydrated() 
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $set('line_total', (int)$state * (float)$get('unit_price'));
                                    })
                                    ->columnSpan(1),

                                // 4. PRICE (Read Only - Derived from Master)
                                TextInput::make('unit_price')
                                    ->label('Price')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $lineTotal = (int)$get('quantity') * (float)$state;
                                        $set('line_total', $lineTotal);
                                    })
                                    ->columnSpan(1),

                                // 5. LINE TOTAL
                                TextInput::make('line_total')
                                    ->label('Total')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateGrandTotals($get, $set);
                                    })
                                    ->columnSpan(1),

                            ])->columns(8)
                            ->live()
                            // Recalculate Grand Totals whenever repeater changes
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateGrandTotals($get, $set);
                            })
                            ->deleteAction(
                                fn (\Filament\Forms\Components\Actions\Action $action) => $action->after(fn(Get $get, Set $set) => self::calculateGrandTotals($get, $set))
                            ),
                    ]),

                // --- FOOTER: GRAND TOTALS ---
                Section::make('Summary')
                    ->schema([
                        TextInput::make('total_qty')
                            ->label('Total Qty')
                            ->readOnly()
                            ->numeric()
                            ->dehydrated()
                            ->default(0),

                        TextInput::make('total_amount')
                            ->label('Grand Total (₹)')
                            ->readOnly()
                            ->numeric()
                            ->prefix('₹')
                            ->dehydrated()
                            ->default(0.00),
                    ])->columns(2)
            ]);
    }

    /**
     * Central Logic for updating line item values based on rules.
     */
    public static function updateLineItemLogic($bookId, $purpose, Set $set, Get $get)
    {
        if (!$bookId) return;

        $book = Book::find($bookId);
        if (!$book) return;

        $price = 0;
        $qty = (int) $get('quantity') ?: 1;

        // Rule 1: SMS (Qty 1, Price 0)
        if ($purpose === 'SMS') {
            $qty = 1; 
            $price = 0; 
        } 
        // Rule 2: Free (Any Qty, Price 0)
        elseif ($purpose === 'Free') {
            $price = 0; 
        } 
        // Rule 3: Seva (Any Qty, Master Price)
        else {
            $price = $book->price; 
        }

        $set('unit_price', $price);
        $set('quantity', $qty);
        $set('line_total', $qty * $price);
    }

    /**
     * Calculate totals for the footer.
     */
    public static function calculateGrandTotals(Get $get, Set $set)
    {
        $items = collect($get('items'));

        // FIX: We use a closure (fn) to explicitly cast values.
        // If 'quantity' is empty string "", (int) converts it to 0.
        $set('total_qty', $items->sum(fn ($item) => (int) ($item['quantity'] ?? 0)));
        
        // Same for line_total, casting to float
        $set('total_amount', $items->sum(fn ($item) => (float) ($item['line_total'] ?? 0)));
    }

    // --- TABLE DEFINITION ---

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('txn_id')->label('Txn ID')->searchable()->sortable(),
                TextColumn::make('txn_date')->date()->sortable(),
                TextColumn::make('bhagat.first_name')
                    ->label('Bhagat Name')
                    ->formatStateUsing(fn (Sale $record) => "{$record->bhagat->first_name} {$record->bhagat->last_name} ({$record->bhagat->user_id})")
                    ->searchable(['first_name', 'last_name', 'user_id']),

                TextColumn::make('total_qty')->label('Qty'),
                TextColumn::make('total_amount')->money('INR')->label('Amount'),
            ])
            ->defaultSort('created_at', 'desc')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
