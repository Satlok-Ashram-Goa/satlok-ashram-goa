<?php

namespace App\Filament\Resources\PincodeResource\Pages;

use App\Filament\Resources\PincodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePincode extends CreateRecord
{
    protected static string $resource = PincodeResource::class;

    /**
     * Override this method to redirect to the 'index' (list) page after creation.
     * This ensures the table view is loaded after creating the new record.
     */
    protected function getRedirectUrl(): string
    {
        // This redirects the user back to the List Pincodes table view.
        return $this->getResource()::getUrl('index');
    }
}
