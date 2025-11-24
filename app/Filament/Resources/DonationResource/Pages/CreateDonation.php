<?php

namespace App\Filament\Resources\DonationResource\Pages;

use App\Filament\Resources\DonationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonation extends CreateRecord
{
    protected static string $resource = DonationResource::class;

    protected function getCreateButtonLabel(): string
    {
        return 'Save Receipt';
    }

    protected function getCreateAnotherButtonLabel(): string
    {
        return 'Save & Create New';
    }
    
    protected function getHeaderActions(): array
    {
        return [];
    }
}
