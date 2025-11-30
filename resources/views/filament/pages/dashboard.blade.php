<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit.prevent="submit">
                {{ $this->form }}
            </form>
        </div>
    </div>
</x-filament-panels::page>
