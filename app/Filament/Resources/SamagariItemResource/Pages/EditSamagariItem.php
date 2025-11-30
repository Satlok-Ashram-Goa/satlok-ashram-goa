<?php

namespace App\Filament\Resources\SamagariItemResource\Pages;

use App\Filament\Resources\SamagariItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSamagariItem extends EditRecord
{
    protected static string $resource = SamagariItemResource::class;

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
