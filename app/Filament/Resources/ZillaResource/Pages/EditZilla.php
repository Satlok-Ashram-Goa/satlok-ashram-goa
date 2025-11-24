<?php

namespace App\Filament\Resources\ZillaResource\Pages;

use App\Filament\Resources\ZillaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditZilla extends EditRecord
{
    protected static string $resource = ZillaResource::class;

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
        // This redirects the user back to the List Zillas table view.
        return $this->getResource()::getUrl('index');
    }
}
