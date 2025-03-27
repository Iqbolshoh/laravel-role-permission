<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}
        {{ $this->submitAction }}
    </form>
</x-filament-panels::page>