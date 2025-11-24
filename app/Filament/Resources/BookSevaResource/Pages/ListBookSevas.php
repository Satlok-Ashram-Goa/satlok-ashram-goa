<?php

namespace App\Filament\Resources\BookSevaResource\Pages;

use App\Filament\Resources\BookSevaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookSevas extends ListRecords
{
    protected static string $resource = BookSevaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
