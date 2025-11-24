<?php

namespace App\Filament\Resources\BookSevaResource\Pages;

use App\Filament\Resources\BookSevaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookSeva extends EditRecord
{
    protected static string $resource = BookSevaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
