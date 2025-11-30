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

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Components\Tab::make('All'),
            'new' => \Filament\Resources\Components\Tab::make('New')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Pending')),
            'in_progress' => \Filament\Resources\Components\Tab::make('In Progress')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'In Progress')),
            'completed' => \Filament\Resources\Components\Tab::make('Completed')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Completed')),
        ];
    }
}
