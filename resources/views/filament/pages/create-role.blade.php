<x-filament-panels::page>
    <div class="p-6 space-y-6">
        <form wire:submit.prevent="create" class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-900" action="role/create">
            <div class="mb-4">
                <label class="block font-semibold">Role Name:</label>
                <input type="text" wire:model.defer="name"
                    class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                    required>
            </div>

            <div class="mb-4 space-y-6">
                @foreach($groupedPermissions as $group => $permissions)
                    <div class="p-4 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
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

            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold py-2 px-4 rounded-xl 
       border-2 border-blue-700 shadow-2xl hover:shadow-2xl 
       hover:from-blue-600 hover:to-blue-800 hover:border-blue-800 
       dark:bg-gradient-to-r dark:from-gray-900 dark:to-gray-950 dark:border-gray-700 dark:text-white 
       dark:hover:from-gray-800 dark:hover:to-gray-900 dark:hover:border-gray-600 
       transition duration-300 ease-in-out transform hover:scale-105 active:scale-95">
                Create Role
            </button>
        </form>
    </div>
</x-filament-panels::page>