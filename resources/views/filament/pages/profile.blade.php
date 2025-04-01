<x-filament::page>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold mb-4">My Profile</h1>

        <form wire:submit.prevent="save" class="space-y-4">
            <x-filament::section>
                {{ $this->form }}
            </x-filament::section>

            <div class="mt-6 flex space-x-4">
                @if ($this->canEdit())
                    <x-filament::button type="submit" color="primary">
                        Save Changes
                    </x-filament::button>
                @endif

                @if ($this->canDelete())
                    <x-filament::button type="button" color="danger" wire:click="delete"
                        wire:confirm="Are you sure you want to delete your profile? This action cannot be undone.">
                        Delete Profile
                    </x-filament::button>
                @endif
            </div>
        </form>
    </div>
</x-filament::page>