<?php

namespace App\Filament\Resources\BhagatResource\Pages;

use App\Filament\Resources\BhagatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBhagat extends EditRecord
{
    protected static string $resource = BhagatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    // Redirect to list page after editing
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
