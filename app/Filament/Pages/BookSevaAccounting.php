<?php

namespace App\Filament\Pages;

use App\Models\BookSeva;
use App\Models\Sale;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class BookSevaAccounting extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

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
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->fromDate = $state),
                    
                DatePicker::make('to_date')
                    ->label('To Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->toDate = $state),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Sale::query()->whereRaw('1 = 0')) // Dummy query, we'll use records instead
            ->columns([
                TextColumn::make('txn_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('txn_id')
                    ->label('Txn No')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('donation_type')
                    ->label('Donation Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Counter Sale' => 'success',
                        'Book Seva' => 'info',
                        default => 'gray',
                    }),
                    
                TextColumn::make('total_qty')
                    ->label('Total Qty')
                    ->numeric()
                    ->alignEnd(),
                    
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('INR')
                    ->alignEnd()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('INR')
                            ->label('Total'),
                    ]),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('txn_date', 'desc');
    }
    
    public function getTableRecords(): Collection
    {
        return $this->getTransactionsData();
    }

    protected function getTransactionsData(): Collection
    {
        // Get Counter Sales
        $sales = \DB::table('sales')
            ->when($this->fromDate, fn ($q) => $q->whereDate('txn_date', '>=', $this->fromDate))
            ->when($this->toDate, fn ($q) => $q->whereDate('txn_date', '<=', $this->toDate))
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
            ->when($this->fromDate, fn ($q) => $q->whereDate('txn_date', '>=', $this->fromDate))
            ->when($this->toDate, fn ($q) => $q->whereDate('txn_date', '<=', $this->toDate))
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
