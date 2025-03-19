<x-filament-panels::page>
    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="p-3 text-green-700 bg-green-200 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 text-red-700 bg-red-200 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-900">
            <div class="mb-4">
                <label class="block font-semibold">Role Name:</label>
                <input type="text" wire:model.defer="name"
                    class="w-full p-2 border rounded-md focus:ring-blue-300 dark:bg-gray-800 dark:text-white" required>
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4 space-y-6">
                @foreach($groupedPermissions as $group => $permissions)
                    <div class="p-4 border rounded-md bg-gray-50 dark:bg-gray-800">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200">
                            {{ ucfirst($group) }}
                        </h3>
                        <div class="flex flex-wrap gap-4 mt-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center gap-2 text-gray-700 dark:text-white">
                                    <input type="checkbox" wire:model="selectedPermissions"
                                        value="{{ $permission['name'] ?? '' }}" class="w-5 h-5 rounded">
                                    <span>{{ ucfirst(explode('.', $permission['name'] ?? '')[1] ?? '') }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @error('selectedPermissions')
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '{{ $message }}',
                        });
                    });
                </script>
            @enderror

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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const submitButton = document.querySelector('button[type="submit"]');
        let isSubmitting = false;

        Livewire.on('roleCreated', () => {
            filament.notifications.notify({
                title: 'Success',
                message: 'Role created successfully!',
                type: 'success'
            });

            submitButton.disabled = false;
            submitButton.innerHTML = 'Create Role';
        });

        submitButton.addEventListener('click', (e) => {
            if (isSubmitting) return e.preventDefault();
            isSubmitting = true;
            submitButton.disabled = true;
            submitButton.innerHTML = 'Creating...';
        });
    });
</script>