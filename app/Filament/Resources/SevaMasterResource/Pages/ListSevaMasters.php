<?php

namespace App\Filament\Resources\SevaMasterResource\Pages;

use App\Filament\Resources\SevaMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSevaMasters extends ListRecords
{
    protected static string $resource = SevaMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
