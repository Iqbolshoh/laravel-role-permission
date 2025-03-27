<x-filament-panels::page>
    <div class="flex justify-end mb-4">
        <x-filament::button wire:click="$set('isOpen', true)">+ Add User</x-filament::button>
    </div>

    <x-filament::card>
        {{ $this->table }}
    </x-filament::card>

    <x-filament::modal id="userModal" wire:model="isOpen">
        <x-slot name="title">
            {{ $this->isEditMode ? 'Edit User' : 'Add User' }}
        </x-slot>

        <form wire:submit="save">
            <x-filament::input.wrapper>
                <x-filament::input label="Full Name" wire:model="name" required />
                <x-filament::input label="Email" type="email" wire:model="email" required />
                <x-filament::input label="Password" type="password" wire:model="password" />
                <x-filament::input label="Confirm Password" type="password" wire:model="password_confirmation" />
                <x-filament::select label="Role" wire:model="role">
                    @foreach($this->roles as $role)
                        <option value="{{ $role }}">{{ $role }}</option>
                    @endforeach
                </x-filament::select>
            </x-filament::input.wrapper>

            <x-filament::button type="submit">
                {{ $this->isEditMode ? 'Update' : 'Create' }}
            </x-filament::button>
        </form>
    </x-filament::modal>
</x-filament-panels::page>