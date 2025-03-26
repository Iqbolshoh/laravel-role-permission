<x-filament-panels::page>
    <div class="container mx-auto px-4 py-8">
        <!-- Edit Modal -->
        @if ($isEditing)
            <div x-data="{ modalOpen: {{ $isEditing ? 'true' : 'false' }} }" x-show="modalOpen" class="fixed inset-0 z-50 flex items-start justify-center bg-opacity-50 p-4 transition-opacity duration-300 
                            bg-gray-900 dark:bg-black">
                <!-- Modal background click to close -->
                <div @click="modalOpen = false; $wire.cancelEdit()" class="absolute inset-0"></div>

                <!-- Modal window -->
                <div class="relative w-full max-w-md rounded-xl shadow-xl mt-16 max-h-[80vh] overflow-y-auto 
                                bg-white dark:bg-gray-800 transition-all duration-200">
                    <form wire:submit.prevent="updateRole" class="p-6 space-y-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Role</h2>

                        <!-- Role Name Input -->
                        <div class="space-y-2">
                            <label for="editRoleName" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Role Name
                            </label>
                            <input type="text" id="editRoleName" wire:model="roleName"
                                class="w-full px-3 py-2 border rounded-md transition-colors duration-200
                                              bg-white border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:outline-none
                                              dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-blue-400" required>
                        </div>

                        <!-- Permissions Section -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($groupedPermissions as $group => $permissions)
                                    <div class="p-4 rounded-lg transition-colors duration-200
                                                       bg-gray-50 dark:bg-gray-700">
                                        <h3 class="text-lg font-semibold mb-3 
                                                          text-gray-800 dark:text-gray-100">
                                            {{ ucfirst($group) }}
                                        </h3>
                                        <div class="grid grid-cols-2 gap-3">
                                            @foreach($permissions as $permission)
                                                <label class="flex items-center space-x-2 text-sm 
                                                                        text-gray-700 dark:text-gray-200">
                                                    <input type="checkbox" wire:model="selectedPermissions"
                                                        value="{{ $permission['name'] }}"
                                                        class="h-4 w-4 rounded transition-colors duration-200
                                                                              text-blue-600 border-gray-300 focus:ring-blue-500
                                                                              dark:text-blue-400 dark:border-gray-600 dark:focus:ring-blue-400">
                                                    <span>{{ ucfirst(explode('.', $permission['name'])[1]) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Modal Buttons -->
                        <div class="flex gap-4">
                            <button type="submit"
                                class="flex-1 px-4 py-2 rounded-md font-medium transition-all duration-200
                                               bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                               dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800">
                                Update Role
                            </button>
                            <button type="button" @click="modalOpen = false; $wire.cancelEdit()"
                                class="flex-1 px-4 py-2 rounded-md font-medium transition-all duration-200
                                               bg-gray-500 text-white hover:bg-gray-600 focus:ring-2 focus:ring-gray-400 focus:ring-offset-2
                                               dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-500 dark:focus:ring-offset-gray-800">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Roles Table -->
        <div class="w-full rounded-xl shadow-md overflow-hidden transition-colors duration-200
                    bg-white dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead class="transition-colors duration-200
                                bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 font-semibold border-b 
                                     text-gray-900 border-gray-200
                                     dark:text-gray-100 dark:border-gray-600">
                                Role Name
                            </th>
                            <th class="px-6 py-4 font-semibold border-b 
                                     text-gray-900 border-gray-200
                                     dark:text-gray-100 dark:border-gray-600">
                                Permissions
                            </th>
                            <th class="px-6 py-4 font-semibold border-b text-center 
                                     text-gray-900 border-gray-200
                                     dark:text-gray-100 dark:border-gray-600">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 border-b 
                                             text-gray-800 border-gray-200
                                             dark:text-gray-200 dark:border-gray-600">
                                    {{ $role['name'] }}
                                </td>
                                <td class="px-6 py-4 border-b 
                                             text-gray-800 border-gray-200
                                             dark:text-gray-200 dark:border-gray-600">
                                    {{ implode(', ', $role['permissions']) }}
                                </td>
                                <td class="px-6 py-4 border-b 
                                             border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-center gap-3">
                                        <button wire:click="editRole({{ $role['id'] }})"
                                            class="px-3 py-1 rounded-md font-medium transition-all duration-200
                                                           bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                                           dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800">
                                            Edit
                                        </button>
                                        <button wire:click="deleteRole({{ $role['id'] }})"
                                            class="px-3 py-1 rounded-md font-medium transition-all duration-200
                                                           bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2
                                                           dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-400 dark:focus:ring-offset-gray-800">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>