<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dashboard';

    public $report_type = null;
    public $start_date = null;
    public $end_date = null;

    public function mount(): void
    {
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
    }

    protected function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\Grid::make(3)
                ->schema([
                    \Filament\Forms\Components\Select::make('report_type')
                        ->label('Report Type')
                        ->options([
                            'bhagat' => 'Bhagat Database',
                            'donation' => 'Donation Seva',
                            'book' => 'Book Seva',
                            'accounts' => 'Accounts',
                        ])
                        ->placeholder('Select Report...'),
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Start date')
                        ->default(now()),
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('End date')
                        ->default(now()),
                ]),
        ];
    }
}
