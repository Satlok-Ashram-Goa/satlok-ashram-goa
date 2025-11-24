<?php

namespace App\Filament\Resources\ZillaResource\Pages;

use App\Filament\Resources\ZillaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateZilla extends CreateRecord
{
    protected static string $resource = ZillaResource::class;

    /**
     * Override this method to redirect to the 'index' (list) page after creation.
     */
    protected function getRedirectUrl(): string
    {
        // This redirects the user back to the List Zillas table view.
        return $this->getResource()::getUrl('index');
    }
}
