<?php

namespace App\Filament\Resources\SamagariItemResource\Pages;

use App\Filament\Resources\SamagariItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSamagariItems extends ListRecords
{
    protected static string $resource = SamagariItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
