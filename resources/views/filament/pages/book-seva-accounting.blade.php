<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Date Filter Section --}}
        <x-filament::section>
            <x-slot name="heading">
                Date Range Filter
            </x-slot>
            
            <x-slot name="description">
                Select date range to view transactions
            </x-slot>
            
            <x-slot name="headerEnd">
                <x-filament::button
                    wire:click="downloadPdf"
                    icon="heroicon-o-arrow-down-tray"
                    color="success"
                >
                    Download PDF
                </x-filament::button>
            </x-slot>

            <form class="space-y-6">
                {{ $this->form }}
            </form>
        </x-filament::section>

        {{-- Transactions Table Section --}}
        <x-filament::section>
            <x-slot name="heading">
                Transactions
            </x-slot>
            
            <x-slot name="description">
                Combined transactions from Counter Sale and Book Seva
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-200">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-200">Txn No</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-200">Donation Type</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-200">Total Qty</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-200">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->getTransactions() as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($transaction->txn_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $transaction->txn_id }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                                        {{ $transaction->donation_type === 'Counter Sale' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                        {{ $transaction->donation_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    {{ number_format($transaction->total_qty) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap font-medium">
                                    ₹ {{ number_format($transaction->total_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <x-heroicon-o-magnifying-glass class="w-12 h-12 text-gray-400" />
                                        <p class="font-medium">No transactions found</p>
                                        <p class="text-sm">Try adjusting your date range</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($this->getTransactions()->count() > 0)
                        <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right">Total:</td>
                                <td class="px-4 py-3 text-right">{{ number_format($this->getTotalQty()) }}</td>
                                <td class="px-4 py-3 text-right text-lg">₹ {{ number_format($this->getTotalAmount(), 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
