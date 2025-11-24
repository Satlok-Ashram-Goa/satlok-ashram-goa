<?php

namespace App\Filament\Resources\StockBalanceResource\Pages;

use App\Filament\Resources\StockBalanceResource;
use App\Models\Book; 
use App\Models\StockBalance; 
use App\Models\Inventory; // Necessary for the logic after save
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStockBalance extends CreateRecord
{
    protected static string $resource = StockBalanceResource::class;

    /**
     * Override the mount method to inject all book data and the required TXN ID.
     */
    public function mount(): void
    {
        parent::mount();
        
        // --- FIX 1: Manually generate the next sequential TXN ID ---
        $nextTxnId = 'STK-' . str_pad(StockBalance::count() + 1, 4, '0', STR_PAD_LEFT);

        // Fetch all books and prepare the repeater data
        $books = Book::all()->map(function ($book) {
            return [
                // Fields defined in the Repeater schema in the Resource file
                'book_details' => "{$book->name} ({$book->language}) - SKU: {$book->sku_id}",
                'actual_qty' => 0, 
                'book_id' => $book->id, // Store book_id secretly for saving logic
            ];
        })->toArray();

        // --- FIX 2: Fill the form state with the generated ID and the book list ---
        $this->form->fill([
            'txn_id' => $nextTxnId, 
            'adjustment_details' => $books
        ]);
    }
    
    /**
     * Executes after the record is created. Used here to update the master Inventory.
     */
    protected function handleCreatedRecord(StockBalance $record): StockBalance
    {
        // Decode the adjustment details (JSON column data)
        $adjustments = json_decode($record->adjustment_details, true);

        // Iterate through each book's actual count and update the master inventory table
        foreach ($adjustments as $adjustment) {
            $bookId = $adjustment['book_id'] ?? null; 
            $actualQty = (int) $adjustment['actual_qty']; 

            if ($bookId) {
                // Update the Inventory master record for this book.
                Inventory::updateOrCreate(
                    ['book_id' => $bookId],
                    ['current_stock_qty' => $actualQty]
                );
            }
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
    
    protected function getCreateButtonLabel(): string
    {
        return 'Save Stock Count';
    }
}
