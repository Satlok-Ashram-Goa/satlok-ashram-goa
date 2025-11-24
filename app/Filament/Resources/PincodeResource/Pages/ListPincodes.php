<?php

namespace App\Filament\Resources\PincodeResource\Pages;

use App\Filament\Resources\PincodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPincodes extends ListRecords
{
    protected static string $resource = PincodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
