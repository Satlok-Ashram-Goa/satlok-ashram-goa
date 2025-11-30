<?php

namespace App\Filament\Resources\DaanRecordResource\Pages;

use App\Filament\Resources\DaanRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaanRecords extends ListRecords
{
    protected static string $resource = DaanRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
