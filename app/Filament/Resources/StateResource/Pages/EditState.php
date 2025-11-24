<?php

namespace App\Filament\Resources\StateResource\Pages;

use App\Filament\Resources\StateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditState extends EditRecord
{
    protected static string $resource = StateResource::class;

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
        // This redirects the user back to the ListStates table view.
        return $this->getResource()::getUrl('index');
    }
}
