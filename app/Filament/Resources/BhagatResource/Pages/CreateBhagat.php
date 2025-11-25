<?php

namespace App\Filament\Resources\BhagatResource\Pages;

use App\Filament\Resources\BhagatResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBhagat extends CreateRecord
{
    protected static string $resource = BhagatResource::class;

    protected function getCreateButtonLabel(): string
    {
        return 'Save';
    }

    protected function getCreateAnotherButtonLabel(): string
    {
        return 'Save & New';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
    
    // Redirect to list page after creating
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
