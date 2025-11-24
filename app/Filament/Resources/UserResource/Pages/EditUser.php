<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    // --- NEW METHOD ADDED HERE ---
    /**
     * Redirects to the index (List) page after saving the record.
     */
    protected function getRedirectUrl(): string
    {
        // This tells Filament to route back to the 'index' page of the UserResource
        return $this->getResource()::getUrl('index');
    }
    // ----------------------------
}
