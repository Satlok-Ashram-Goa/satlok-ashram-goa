<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // --- The Fix: Redirect to Table View after editing ---
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
                // Recalculate line_total for each item
                $qty = (int)($item['quantity'] ?? 0);
                $price = (float)($item['unit_price'] ?? 0);
                $item['line_total'] = $qty * $price;

                // Add to totals
                $totalQty += $qty;
                $totalAmount += $item['line_total'];
            }

            // Update grand totals
            $data['total_qty'] = $totalQty;
            $data['total_amount'] = $totalAmount;
        }

        return $data;
    }
}
