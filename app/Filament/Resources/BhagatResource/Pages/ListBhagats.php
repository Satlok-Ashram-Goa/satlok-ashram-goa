<?php

namespace App\Filament\Resources\BhagatResource\Pages;

use App\Filament\Resources\BhagatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBhagats extends ListRecords
{
    protected static string $resource = BhagatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
