<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    /**
     * Set the maximum width of the content area to 'full'.
     * Must be declared 'public' to override the base class method.
     */
    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    /**
     * Set the form container width to 'full'.
     * Must be declared 'public' to override the base class method.
     */
    public function getFormWidth(): string
    {
        return 'full';
    }

    protected function getHeaderActions(): array
    {
        return [
            // No actions allowed on the create page header.
        ];
    }
    
    // FIX 1: Renames the primary 'Create' button to 'Save'
    protected function getCreateButtonLabel(): string
    {
        return 'Save';
    }

    // FIX 2: Renames the 'Create & Create another' button to 'Save & New'
    protected function getCreateAnotherButtonLabel(): string
    {
        return 'Save & New';
    }
}
