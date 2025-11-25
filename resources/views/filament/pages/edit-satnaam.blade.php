<x-filament-panels::page>
    @if(!$canEditSatnaam)
        <x-filament::section>
            <div class="flex items-center gap-3 p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg border border-warning-200 dark:border-warning-700">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                <div>
                    <h3 class="font-semibold text-warning-900 dark:text-warning-100">Attendance Incomplete</h3>
                    <p class="text-sm text-warning-700 dark:text-warning-300">All 4 attendance dates must be completed before you can update the Satnaam Mantra Date.</p>
                </div>
            </div>
        </x-filament::section>
    @endif

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button 
                type="submit" 
                icon="heroicon-o-check"
                :disabled="!$canEditSatnaam"
            >
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
