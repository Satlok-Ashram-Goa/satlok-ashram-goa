<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Search Form Section --}}
        <x-filament::section>
            <x-slot name="heading">
                Search Bhagat
            </x-slot>
            
            <x-slot name="description">
                Search by Form Number or Mobile Number to find Bhagat records.
            </x-slot>

            <form wire:submit="search" class="space-y-6">
                {{ $this->form }}
                
                <div class="flex gap-3">
                    <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                        Search
                    </x-filament::button>
                    
                    <x-filament::button type="button" color="gray" wire:click="clear">
                        Clear
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Results Table Section --}}
        @if($hasSearched)
            <x-filament::section>
                <x-slot name="heading">
                    Search Results
                </x-slot>

                {{ $this->table }}
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
