<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockBalanceResource\Pages;
use App\Models\StockBalance;
use App\Models\Book; 

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

// --- TABLE ACTION IMPORTS ---
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
// ----------------------------------

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StockBalanceResource extends Resource
{
    protected static ?string $model = StockBalance::class;

    protected static ?string $navigationGroup = 'Book Database';
    protected static ?string $navigationLabel = 'Stock Balance Form';
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?int $navigationSort = 3; 

    // ----------------------------------------------------------------------
    // ## Form Layout (Stock Take)
    // ----------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Stock Take Header')
                    ->schema([
                        TextInput::make('txn_id')
                            ->label('Txn Id')
                            ->default(fn () => 'STK-' . str_pad(StockBalance::count() + 1, 4, '0', STR_PAD_LEFT))
                            ->readOnly(),
                        DatePicker::make('closing_balance_month')
                            ->label('Closing Balance Date')
                            ->required()
                            ->default(now()),
                    ])->columns(2),

                Section::make('Actual Stock Count')
                    ->description('Enter the physical count for every book listed below.')
                    ->schema([
                        Repeater::make('adjustment_details')
                            ->label(false) 
                            ->columnSpanFull()
                            ->minItems(1)
                            
                            // FIX: Using the correct supported methods for disabling actions
                            ->addable(false)          
                            ->deletable(false)        
                            ->reorderable(false)    
                            
                            ->defaultItems(0)
                            ->schema([
                                // Field 1: Book Details (Readonly)
                                TextInput::make('book_details')
                                    ->label('Book Master')
                                    ->readOnly()
                                    ->columnSpan(3),

                                // Field 2: Actual Qty (Editable Count)
                                TextInput::make('actual_qty')
                                    ->label('Physical Count (Override)')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1)
                                    ->live()
                                    ->debounce('500ms') 
                                    ->dehydrated(true), 
                            ])->columns(4)
                            // --- FIX: Explicitly cast to (int) before summing ---
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                 // Recalculate Total Qty
                                $items = collect($get('adjustment_details'));
                                $totalQty = $items->sum(fn ($item) => (int) $item['actual_qty']);
                                $set('total_qty', $totalQty);
                            }),
                    ]),
                    
                // Total Qty (Calculated field)
                TextInput::make('total_qty')
                    ->label('Total Adjusted Quantity')
                    ->readOnly()
                    ->numeric()
                    ->dehydrated(true), 
                
                // Hidden field to store the user ID
                TextInput::make('adjusted_by_user_id')
                    ->numeric()
                    ->default(auth()->id()) // Pre-fill with the logged-in user's ID
                    ->required()
                    ->hidden(),

            ]);
            // NOTE: The conflicting afterSave hook is removed, as it's now handled by the Page Class.
    }

    // ----------------------------------------------------------------------
    // ## Table Layout
    // ----------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('txn_id')->label('Txn Id')->searchable()->sortable(),
                TextColumn::make('closing_balance_month')->label('Closing Balance Month')->date('M Y')->sortable(),
                TextColumn::make('total_qty')->label('Total Qty')->sortable(),
                
                // Column to show the Admin User's name
                TextColumn::make('adjustedBy.name')
                    ->label('Adjusted By')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListStockBalances::route('/'),
            'create' => Pages\CreateStockBalance::route('/create'),
            'edit' => Pages\EditStockBalance::route('/{record}/edit'),
        ];
    }
}
