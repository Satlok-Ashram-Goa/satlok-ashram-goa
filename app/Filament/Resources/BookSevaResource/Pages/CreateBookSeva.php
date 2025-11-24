<?php

namespace App\Filament\Resources\BookSevaResource\Pages;

use App\Filament\Resources\BookSevaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookSeva extends CreateRecord
{
    protected static string $resource = BookSevaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
