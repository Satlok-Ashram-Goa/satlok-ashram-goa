<?php

namespace App\Filament\Resources\BookSevaResource\Pages;

use App\Filament\Resources\BookSevaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookSeva extends CreateRecord
{
    protected static string $resource = BookSevaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        \Log::info('CreateBookSeva Data:', $data);

        $totalQty = 0;
        $totalAmount = 0;

        // Recalculate everything to be safe
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as &$item) {
                $qty = (int) ($item['quantity'] ?? 0);
                $price = (float) ($item['price'] ?? 0);
                $amount = $qty * $price;
                
                // Update item amount
                $item['amount'] = $amount;
                
                $totalQty += $qty;
                $totalAmount += $amount;
            }
        }

        $data['total_qty'] = $totalQty;
        $data['total_amount'] = $totalAmount;

        return $data;
    }
}
