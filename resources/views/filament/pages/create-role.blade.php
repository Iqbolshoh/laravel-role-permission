<x-filament-panels::page>
    <div class="p-6 space-y-6">
        <form wire:submit.prevent="save" 
              class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg dark:shadow-gray-700">
            <!-- Role Name Input -->
            <div class="mb-4">
                <label for="roleName" class="block font-semibold text-gray-900 dark:text-gray-200">Role Name:</label>
                <input id="roleName" type="text" wire:model.defer="roleName" 
                       class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 
                              bg-gray-100 border-gray-300 text-gray-900 
                              dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200" required>
            </div>

            <!-- Permission Groups -->
            <div class="mb-4 space-y-6">
                @foreach($groupedPermissions as $group => $permissions)
                    <div class="p-4 border rounded-md bg-gray-100 border-gray-300 text-gray-900 
                                dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                        <h3 class="font-semibold text-lg">{{ ucfirst($group) }}</h3>
                        <div class="flex flex-wrap gap-4 mt-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" wire:model="permissions" value="{{ $permission['name'] }}"
                                           class="w-5 h-5 rounded border-gray-400 dark:border-gray-600">
                                    <span>{{ ucfirst(explode('.', $permission['name'])[1]) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Save Button -->
            <button type="submit" 
                    class="w-full py-2 px-4 rounded-xl font-semibold transition duration-300 ease-in-out 
                           transform hover:scale-105 active:scale-95 bg-gradient-to-r from-gray-200 to-gray-400 
                           border-2 border-gray-500 text-gray-900 shadow-lg hover:from-gray-300 hover:to-gray-500 
                           hover:border-gray-600 dark:bg-gradient-to-r dark:from-gray-700 dark:to-gray-900 
                           dark:border-gray-600 dark:text-white dark:hover:from-gray-600 dark:hover:to-gray-800">
                Save Role
            </button>
        </form>
    </div>
</x-filament-panels::page>