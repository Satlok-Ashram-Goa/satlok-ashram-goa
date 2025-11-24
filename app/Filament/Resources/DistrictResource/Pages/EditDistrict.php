<?php

namespace App\Filament\Resources\DistrictResource\Pages;

use App\Filament\Resources\DistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistrict extends EditRecord
{
    protected static string $resource = DistrictResource::class;

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
        // This redirects the user back to the List Districts table view.
        return $this->getResource()::getUrl('index');
    }
}
