<x-filament-panels::page>
    <div class="p-6 space-y-6">
        
        {{-- Edit Modal --}}
        @if ($isEditing)
            <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-90 w-full h-full">
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg w-full max-w-lg">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">Edit Role</h2>

                    {{-- Role Name Input --}}
                    <input type="text" wire:model="roleName"
                        class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                        required>

                    {{-- Permissions --}}
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        @foreach($groupedPermissions as $group => $permissions)
                            <div class="p-4 border rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200">{{ ucfirst($group) }}</h3>
                                <div class="flex flex-wrap gap-4 mt-3">
                                    @foreach($permissions as $permission)
                                        <label class="flex items-center gap-1 text-gray-700 dark:text-white font-medium">
                                            <input type="checkbox" wire:model="selectedPermissions"
                                                value="{{ $permission['name'] }}">
                                            <span>{{ ucfirst(explode('.', $permission['name'])[1]) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 mt-4">
                        <button wire:click="updateRole" class="btn-primary">Save</button>
                        <button wire:click="$set('isEditing', false)" class="btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Roles Table --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
            <table class="w-full border-collapse border border-gray-300 dark:border-gray-700">
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
                            <td class="px-4 py-3 text-center flex gap-2 justify-center">
                                <button wire:click="editRole({{ $role['id'] }})" class="btn-edit">Edit</button>
                                <button wire:click="deleteRole({{ $role['id'] }})" class="btn-delete">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-filament-panels::page>