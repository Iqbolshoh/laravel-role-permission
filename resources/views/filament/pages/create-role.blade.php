<x-filament-panels::page>
    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="p-3 text-green-700 bg-green-200 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-900">
            <div class="mb-4">
                <label class="block font-semibold">Role Name:</label>
                <input type="text" wire:model.defer="name"
                    class="w-full p-2 border rounded-md focus:ring-blue-300 dark:bg-gray-800 dark:text-white" required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4 space-y-6">
                @foreach($this->groupedPermissions as $group => $permissions)
                    <div class="p-4 border rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200">{{ ucfirst($group) }}</h3>
                            <label class="flex items-center gap-1">
                                <input type="checkbox"
                                    class="select-all-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-400 dark:border-gray-600"
                                    data-group="{{ $group }}">
                                <span>All</span>
                            </label>
                        </div>
                        <div class="flex flex-wrap gap-4 mt-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center gap-1 text-gray-700 dark:text-white font-medium">
                                    <input type="checkbox" wire:model="selectedPermissions"
                                        value="{{ $permission['name'] ?? '' }}"
                                        class="permission-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-400 dark:border-gray-600"
                                        data-group="{{ $group }}">
                                    <span>
                                        {{ ucfirst(explode('.', $permission['name'] ?? '')[1] ?? '') }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-black font-semibold py-2 px-4 rounded-lg 
           border border-blue-600 shadow-lg hover:shadow-xl 
           hover:from-blue-600 hover:to-blue-700 hover:text-white hover:border-blue-700 
           dark:bg-gradient-to-r dark:from-gray-800 dark:to-gray-900 dark:border-gray-600 dark:text-white 
           dark:hover:from-gray-700 dark:hover:to-gray-800 dark:hover:border-gray-500 
           transition duration-300 ease-in-out transform hover:scale-105">
                Create Role
            </button>
        </form>

    </div>
</x-filament-panels::page>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const updateSelectedPermissions = () => {
            let selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked'))
                .map(cb => cb.value);

            Livewire.emit('updateAllSelectedPermissions', selectedPermissions);
        };

        document.querySelectorAll('.select-all-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const group = this.dataset.group;
                const isChecked = this.checked;
                let selectedPermissions = @this.get('selectedPermissions') || [];

                document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`)
                    .forEach(cb => {
                        cb.checked = isChecked;
                        if (isChecked) {
                            if (!selectedPermissions.includes(cb.value)) {
                                selectedPermissions.push(cb.value);
                            }
                        } else {
                            selectedPermissions = selectedPermissions.filter(permission => permission !== cb.value);
                        }
                    });

                @this.set('selectedPermissions', selectedPermissions);
            });
        });

        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedPermissions);
        });

        Livewire.on('permissionsUpdated', selectedPermissions => {
            document.querySelectorAll('.permission-checkbox').forEach(cb => {
                cb.checked = selectedPermissions.includes(cb.value);
            });

            document.querySelectorAll('.select-all-checkbox').forEach(groupCheckbox => {
                const group = groupCheckbox.dataset.group;
                const groupPermissions = Array.from(document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`));
                groupCheckbox.checked = groupPermissions.every(cb => cb.checked);
            });
        });
    });

    document.addEventListener('livewire:load', () => {
        Livewire.on('roleCreated', () => {
            window.filament.notifications.notify({
                title: 'Success',
                message: 'Role created successfully!',
                type: 'success'
            });
        });
    });
</script>