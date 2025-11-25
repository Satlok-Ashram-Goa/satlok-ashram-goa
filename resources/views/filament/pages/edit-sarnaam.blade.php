<x-filament-panels::page>
    @if(!$canEditSarnaam)
        <x-filament::section>
            <div class="flex items-center gap-3 p-4 bg-danger-50 dark:bg-danger-900/20 rounded-lg border border-danger-200 dark:border-danger-700">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                <div>
                    <h3 class="font-semibold text-danger-900 dark:text-danger-100">Requirements Not Met</h3>
                    <p class="text-sm text-danger-700 dark:text-danger-300">
                        The following must be completed before updating Sarnaam Mantra Date:
                    </p>
                    <ul class="mt-2 text-sm text-danger-700 dark:text-danger-300 list-disc list-inside">
                        @foreach($missingRequirements as $requirement)
                            <li>{{ $requirement }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="flex items-center gap-3 p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg border border-warning-200 dark:border-warning-700">
                <x-heroicon-o-exclamation-circle class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                <div>
                    <h3 class="font-semibold text-warning-900 dark:text-warning-100">Final Step Warning</h3>
                    <p class="text-sm text-warning-700 dark:text-warning-300">
                        Once Sarnaam Mantra Date is set, ALL previous dates (Attendance Dates 1-4 and Satnaam Date) will be permanently locked and cannot be edited.
                    </p>
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
                :disabled="!$canEditSarnaam"
            >
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
