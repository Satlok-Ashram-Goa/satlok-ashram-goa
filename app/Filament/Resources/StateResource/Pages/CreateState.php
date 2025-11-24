<?php

namespace App\Filament\Resources\StateResource\Pages;

use App\Filament\Resources\StateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateState extends CreateRecord
{
    protected static string $resource = StateResource::class;

    /**
     * Override this method to redirect to the 'index' (list) page after creation.
     * This forces the ListStates page (i.e., /admin/states) to load and display the new record.
     */
    protected function getRedirectUrl(): string
    {
        // This is the line that implements the redirect to the List page
        return $this->getResource()::getUrl('index');
    }
}
