<?php

namespace App\Filament\Resources\DistrictResource\Pages;

use App\Filament\Resources\DistrictResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDistrict extends CreateRecord
{
    protected static string $resource = DistrictResource::class;

    /**
     * Override this method to redirect to the 'index' (list) page after creation.
     * This ensures the table view is loaded after creating the new record.
     */
    protected function getRedirectUrl(): string
    {
        // This redirects the user back to the List Districts table view.
        return $this->getResource()::getUrl('index');
    }
}
