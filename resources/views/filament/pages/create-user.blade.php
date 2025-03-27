<x-filament-panels::page>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Create New User</h2>
    </x-slot>

    <x-filament-panels::form wire:submit="create">
        {{ $this->form }}
        <x-filament::button type="submit">Create User</x-filament::button>
    </x-filament-panels::form>
</x-filament-panels::page>
