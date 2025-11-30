<?php

namespace App\Filament\Resources\DaanRecordResource\Pages;

use App\Filament\Resources\DaanRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDaanRecord extends CreateRecord
{
    protected static string $resource = DaanRecordResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
