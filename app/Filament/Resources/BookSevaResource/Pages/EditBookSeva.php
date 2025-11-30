<?php

namespace App\Filament\Resources\BookSevaResource\Pages;

use App\Filament\Resources\BookSevaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookSeva extends EditRecord
{
    protected static string $resource = BookSevaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Recalculate totals from items when loading the form
        if (isset($data['items']) && is_array($data['items'])) {
            $totalQty = 0;
            $totalAmount = 0;

            foreach ($data['items'] as &$item) {
                // Recalculate amount for each item
                $qty = (int)($item['quantity'] ?? 0);
                $price = (float)($item['price'] ?? 0);
                $item['amount'] = $qty * $price;

                // Add to totals
                $totalQty += $qty;
                $totalAmount += $item['amount'];
            }

            // Update grand totals
            $data['total_qty'] = $totalQty;
            $data['total_amount'] = $totalAmount;
        }

        return $data;
    }
}
