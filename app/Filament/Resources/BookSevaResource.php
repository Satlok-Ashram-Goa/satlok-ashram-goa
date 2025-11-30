<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookSevaResource\Pages;
use App\Models\BookSeva;
use App\Models\Book;
use App\Models\District;
use App\Models\Zilla;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
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
use Filament\Tables\Actions\Action; // Import Action
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;

class BookSevaResource extends Resource
{
    protected static ?string $model = BookSeva::class;

    protected static ?string $navigationGroup = 'Book Database';
    protected static ?string $navigationLabel = 'Book Seva';
    protected static ?string $navigationIcon = 'heroicon-o-users'; 
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- SECTION 1: HEADER ---
                Section::make('Seva Details')
                    ->schema([
                        TextInput::make('txn_id')
                            ->label('Txn ID')
                            ->default(fn () => 'SEVA-' . str_pad(BookSeva::count() + 1, 6, '0', STR_PAD_LEFT))
                            ->readOnly(),

                        DatePicker::make('txn_date')
                            ->label('Date')
                            ->required()
                            ->default(now()),
                        
                        TextInput::make('total_sevadaar')
                            ->label('Total Sevadaar (Count)')
                            ->numeric()
                            ->default(0),

                        // Row 2 (Locations)
                        Select::make('state_id')
                            ->label('State')
                            ->relationship('state', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('district_id', null);
                                $set('zilla_id', null);
                            }),

                        Select::make('district_id')
                            ->label('District')
                            ->options(fn (Get $get) => District::where('state_id', $get('state_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->disabled(fn (Get $get) => ! $get('state_id'))
                            ->afterStateUpdated(function (Set $set) {
                                $set('zilla_id', null);
                            }),

                        Select::make('zilla_id')
                            ->label('Zilla')
                            ->options(fn (Get $get) => Zilla::where('district_id', $get('district_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get) => ! $get('district_id')),

                        Hidden::make('user_id')
                            ->default(fn () => Auth::id())
                            ->required(),
                    ])->columns(3),

                // --- SECTION 2: ITEMS ---
                Section::make('Book Distribution')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('book_id')
                                    ->label('Book')
                                    ->options(Book::where('can_be_sold', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        self::updateLineItemLogic($state, $set, $get);
                                    })
                                    ->columnSpan(4),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live()
                                    ->debounce('500ms')
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $amount = (int)$state * (float)$get('price');
                                        $set('amount', $amount);
                                    })
                                    ->columnSpan(2),

                                TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $amount = (int)$get('quantity') * (float)$state;
                                        $set('amount', $amount);
                                    })
                                    ->columnSpan(2),

                                TextInput::make('amount')
                                    ->label('Total')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateGrandTotals($get, $set);
                                    })
                                    ->columnSpan(2),
                            ])
                            ->columns(10)
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateGrandTotals($get, $set))
                            ->deleteAction(
                                fn ($action) => $action->after(fn(Get $get, Set $set) => self::calculateGrandTotals($get, $set))
                            ),
                    ]),

                // --- SECTION 3: FOOTER ---
                Section::make('Summary')
                    ->schema([
                        TextInput::make('total_qty')
                            ->label('Total Books')
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

    public static function updateLineItemLogic($bookId, Set $set, Get $get)
    {
        if (!$bookId) return;
        $book = Book::find($bookId);
        if (!$book) return;
        $qty = (int) $get('quantity') ?: 1;
        $price = $book->price;
        $set('price', $price);
        $set('amount', $qty * $price);
    }

    public static function calculateGrandTotals(Get $get, Set $set)
    {
        $items = collect($get('items'));
        $set('total_qty', $items->sum(fn ($item) => (int) ($item['quantity'] ?? 0)));
        $set('total_amount', $items->sum(fn ($item) => (float) ($item['amount'] ?? 0)));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('txn_id')->label('Txn ID')->searchable()->sortable(),
                TextColumn::make('txn_date')->date()->sortable(),
                
                // REMOVED: State
                TextColumn::make('district.name')->label('District')->sortable()->toggleable(),
                TextColumn::make('zilla.name')->label('Zilla')->sortable()->toggleable(),
                
                TextColumn::make('user.name')->label('Created By')->sortable(),
                TextColumn::make('total_sevadaar')->label('Sevadaars'),
                TextColumn::make('total_qty')->label('Total Books'),
                TextColumn::make('total_amount')->money('INR'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // --- PDF DOWNLOAD ACTION ---
                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (BookSeva $record) => route('book-sevas.pdf', $record))
                    ->openUrlInNewTab(),

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
            'index' => Pages\ListBookSevas::route('/'),
            'create' => Pages\CreateBookSeva::route('/create'),
            'edit' => Pages\EditBookSeva::route('/{record}/edit'),
        ];
    }
}
