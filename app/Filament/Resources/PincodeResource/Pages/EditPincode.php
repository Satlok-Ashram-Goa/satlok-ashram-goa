<?php

namespace App\Filament\Resources\PincodeResource\Pages;

use App\Filament\Resources\PincodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPincode extends EditRecord
{
    protected static string $resource = PincodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Override this method to redirect to the 'index' (list) page after saving the record.
     * This ensures the table view is loaded after editing.
     */
    protected function getRedirectUrl(): string
    {
        // This redirects the user back to the List Pincodes table view.
        return $this->getResource()::getUrl('index');
    }
}
