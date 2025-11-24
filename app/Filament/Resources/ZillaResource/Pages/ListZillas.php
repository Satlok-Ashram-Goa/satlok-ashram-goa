<?php

namespace App\Filament\Resources\ZillaResource\Pages;

use App\Filament\Resources\ZillaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListZillas extends ListRecords
{
    protected static string $resource = ZillaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
