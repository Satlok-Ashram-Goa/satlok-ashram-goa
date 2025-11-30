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

            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
