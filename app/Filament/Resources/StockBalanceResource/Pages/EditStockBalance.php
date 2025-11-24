<?php

    namespace App\Filament\Resources\StockBalanceResource\Pages;

    use App\Filament\Resources\StockBalanceResource;
    use Filament\Actions;
    use Filament\Resources\Pages\EditRecord;

    class EditStockBalance extends EditRecord
    {
        protected static string $resource = StockBalanceResource::class;

        protected function getHeaderActions(): array
        {
            return [
                Actions\DeleteAction::make(),
            ];
        }
        
        /**
         * FIX: Redirects back to the index (list) page after successfully saving.
         */
        protected function getRedirectUrl(): string
        {
            return $this->getResource()::getUrl('index');
        }
    }
