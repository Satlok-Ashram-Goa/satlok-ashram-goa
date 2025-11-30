<?php

namespace App\Filament\Pages;

use App\Models\BookSeva;
use App\Models\Sale;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class BookSevaAccounting extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static string $view = 'filament.pages.book-seva-accounting';
    
    protected static ?string $navigationGroup = 'Accounts & Finance';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Book Seva Accounting';
    
    protected static ?string $title = 'Book Seva Accounting';

    public ?array $data = [];
    public ?string $fromDate = null;
    public ?string $toDate = null;

    public function mount(): void
    {
        // Set default dates to current month
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->endOfMonth()->format('Y-m-d');
        
        $this->form->fill([
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('from_date')
                    ->label('From Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d') // Ensure state is stored as Y-m-d
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state) {
                        $this->fromDate = $state;
                    }),
                    
                DatePicker::make('to_date')
                    ->label('To Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d') // Ensure state is stored as Y-m-d
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state) {
                        $this->toDate = $state;
                    }),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function getTransactions()
    {
        return $this->getTransactionsData();
    }

    #[\Livewire\Attributes\Computed]
    public function transactions()
    {
        return $this->getTransactionsData();
    }

    public function getTotalAmount()
    {
        return $this->transactions()->sum('total_amount');
    }

    public function getTotalQty()
    {
        return $this->transactions()->sum('total_qty');
    }

    protected function getTransactionsData(): Collection
    {
        // Get Counter Sales
        $sales = \DB::table('sales')
            ->when($this->fromDate, fn ($q) => $q->where('txn_date', '>=', $this->fromDate))
            ->when($this->toDate, fn ($q) => $q->where('txn_date', '<=', $this->toDate))
            ->select([
                'id',
                'txn_date',
                'txn_id',
                \DB::raw("'Counter Sale' as donation_type"),
                'total_qty',
                'total_amount',
            ])
            ->get();

        // Get Book Sevas
        $bookSevas = \DB::table('book_sevas')
            ->when($this->fromDate, fn ($q) => $q->where('txn_date', '>=', $this->fromDate))
            ->when($this->toDate, fn ($q) => $q->where('txn_date', '<=', $this->toDate))
            ->select([
                'id',
                'txn_date',
                'txn_id',
                \DB::raw("'Book Seva' as donation_type"),
                'total_qty',
                'total_amount',
            ])
            ->get();

        // Merge and sort
        return $sales->merge($bookSevas)->sortByDesc('txn_date')->values();
    }

    public function downloadPdf(): \Symfony\Component\HttpFoundation\Response
    {
        $transactions = $this->getTransactionsData();
            
        $totalAmount = $transactions->sum('total_amount');
        $totalQty = $transactions->sum('total_qty');

        $pdf = Pdf::loadView('pdf.book-seva-accounting', [
            'transactions' => $transactions,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'totalAmount' => $totalAmount,
            'totalQty' => $totalQty,
        ]);

        $filename = 'book-seva-accounting-' . now()->format('Y-m-d') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
