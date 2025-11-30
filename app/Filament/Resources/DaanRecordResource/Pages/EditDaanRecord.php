<?php

namespace App\Filament\Resources\DaanRecordResource\Pages;

use App\Filament\Resources\DaanRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaanRecord extends EditRecord
{
    protected static string $resource = DaanRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('download_receipt')
                ->label('Download Receipt')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function ($record) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.daan-record-receipt', ['record' => $record]);
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "Seva-Receipt-{$record->pledge_id}.pdf"
                    );
                }),
        ];
    }
}
