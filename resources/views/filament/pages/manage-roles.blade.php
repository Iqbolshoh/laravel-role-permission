<x-filament-panels::page>
    <div class="p-6 space-y-6">
        @if ($isEditing)
            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg overflow-x-auto">
                <form wire:submit.prevent="updateRole">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Edit Role</h2>

                    <!-- Role Name -->
                    <div class="mb-4">
                        <label for="roleName" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Role
                            Name</label>
                        <input type="text" id="roleName" wire:model="roleName"
                            class="w-full p-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:ring focus:ring-blue-300"
                            required>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-4">
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            @foreach($groupedPermissions as $group => $permissions)
                                <div class="p-4 border rounded-md bg-gray-700 border-gray-600">
                                    <h3 class="font-semibold text-lg text-gray-200">{{ ucfirst($group) }}</h3>
                                    <div class="flex flex-wrap gap-4 mt-3">
                                        @foreach($permissions as $permission)
                                            <label class="flex items-center gap-1 text-gray-300">
                                                <input type="checkbox" wire:model="selectedPermissions"
                                                    value="{{ $permission['name'] }}"
                                                    class="text-gray-600 border-gray-500 bg-gray-700">
                                                <span>{{ ucfirst(explode('.', $permission['name'])[1]) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition transform hover:scale-105 active:scale-95">
                            Update Role
                        </button>
                        <button type="button" wire:click="cancelEdit"
                            class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition transform hover:scale-105 active:scale-95">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Roles Table -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg overflow-x-auto">
                <table class="w-full border border-gray-300 dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="border px-4 py-3 text-left text-gray-800 dark:text-gray-200">Role Name</th>
                            <th class="border px-4 py-3 text-left text-gray-800 dark:text-gray-200">Permissions</th>
                            <th class="border px-4 py-3 text-center text-gray-800 dark:text-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr class="border">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-200">{{ $role['name'] }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-200">{{ implode(', ', $role['permissions']) }}
                                </td>
                                <td class="px-4 py-3 flex justify-center gap-2">
                                    <button wire:click="editRole({{ $role['id'] }})"
                                        class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 transition transform hover:scale-105 active:scale-95">
                                        Edit
                                    </button>
                                    <button wire:click="deleteRole({{ $role['id'] }})"
                                        class="bg-red-600 text-white py-1 px-3 rounded-lg hover:bg-red-700 transition transform hover:scale-105 active:scale-95">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-filament-panels::page>