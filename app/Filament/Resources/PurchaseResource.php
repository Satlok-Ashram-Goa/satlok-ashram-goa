<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Purchase;
use App\Models\Book; 
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction; 
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;
    protected static ?string $navigationGroup = 'Book Database';
    protected static ?string $navigationLabel = 'Purchase Record';
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?int $navigationSort = 2;


    // ----------------------------------------------------------------------
    // ## Form Layout (Invoice Creation with Repeater)
    // ----------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- INVOICE HEADER DETAILS ---
                Section::make('Invoice Details')
                    ->schema([
                        TextInput::make('txn_id')
                            ->label('Txn Id')
                            ->default(fn () => 'TXN-' . str_pad(Purchase::count() + 1, 5, '0', STR_PAD_LEFT))
                            ->readOnly(),
                        TextInput::make('supplier_name')->required(),
                        TextInput::make('invoice_no')->required()->unique(ignoreRecord: true),
                        DatePicker::make('txn_date')->label('Invoice Date')->required(),
                        TextInput::make('vehicle_no')->nullable(),
                    ])->columns(3),

                // --- INVOICE LINE ITEMS (REPEATER) ---
                Section::make('Book Line Items')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('book_id')
                                    ->label('Book Name')
                                    ->options(Book::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable(),

                                TextInput::make('unit_price')
                                    ->label('Price (per unit)')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (Set $set, $state, Get $get) => 
                                        $set('line_total', $state * $get('quantity'))
                                    ),

                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(fn (Set $set, $state, Get $get) => 
                                        $set('line_total', $state * $get('unit_price'))
                                    ),
                                    
                                TextInput::make('line_total')
                                    ->label('Amount')
                                    ->readOnly()
                                    ->numeric(),

                            ])->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('Add Book Row')
                            ->live()
                            ->deleteAction(
                                fn (\Filament\Forms\Components\Actions\Action $action) => $action->icon('heroicon-m-trash'),
                            ),
                    ]),
                    
                // --- FOOTER AND TOTALS (FINAL CORRECTED BLOCK) ---
                Section::make('Invoice Totals & Documents')
                    ->schema([
                        // Total Quantity (LOCKED AND CALCULATED)
                        TextInput::make('total_qty')
                            ->label('Total Quantity')
                            ->default(0)
                            ->readOnly()
                            ->disabled() // NEW: Prevents interaction
                            ->dehydrated(false) // NEW: Prevents saving to model
                            ->numeric()
                            ->columnSpan(1),
                            
                        // Total Amount (LOCKED AND CALCULATED)
                        TextInput::make('total_amount')
                            ->label('Total Amount (₹)')
                            ->default(0.00)
                            ->readOnly()
                            ->disabled() // NEW: Prevents interaction
                            ->dehydrated(false) // NEW: Prevents saving to model
                            ->numeric()
                            ->columnSpan(1),
                            
                        // Upload Field
                        FileUpload::make('invoice_copy_path')
                            ->label('Upload Invoice Copy')
                            ->directory('purchase-invoices')
                            ->disk('public')
                            ->maxSize(5120) // Max 5MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->columnSpan(2),

                    ])->columns(4)
                    // ADDED LIVE and AFTERSTATEUPDATED to ensure totals update instantly
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) { 
                        // This runs when any item inside the repeater changes
                        $items = collect($get('items'));
                        $totalQty = $items->sum('quantity');
                        $totalAmount = $items->sum('line_total');

                        $set('total_qty', $totalQty);
                        $set('total_amount', number_format($totalAmount, 2, '.', ''));
                    })
                    ->afterStateHydrated(function (Get $get, Set $set) { 
                        // This runs on initial load/edit
                        $items = collect($get('items'));
                        $totalQty = $items->sum('quantity');
                        $totalAmount = $items->sum('line_total');

                        $set('total_qty', $totalQty);
                        $set('total_amount', number_format($totalAmount, 2, '.', ''));
                    })
                    ->saveRelationshipsWhenHidden()
            ]);
    }

    // ----------------------------------------------------------------------
    // ## Table Layout (Purchase Record List)
    // ----------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('txn_id')->label('Txn Id')->searchable()->sortable(),
                TextColumn::make('txn_date')->label('Txn Date')->date()->sortable(),
                TextColumn::make('supplier_name')->searchable(),
                TextColumn::make('invoice_no')->label('Invoice No')->searchable(),
                TextColumn::make('total_qty')->label('Total Qty')->sortable(),
                TextColumn::make('total_amount')->label('Total Amount')->money('INR')->sortable(),
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

    // ----------------------------------------------------------------------
    // ## FIX: Remove Repeater from Relations
    // ----------------------------------------------------------------------
    public static function getRelations(): array
    {
        return [
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
