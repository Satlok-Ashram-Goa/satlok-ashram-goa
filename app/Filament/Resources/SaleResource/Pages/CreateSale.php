<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    // --- The Fix: Redirect to Table View after creation ---
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // --- Your Customizations: Better Button Labels ---
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
}
