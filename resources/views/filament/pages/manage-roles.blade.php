<x-filament-panels::page>
    <div class="fi-page-content space-y-6 bg-white dark:bg-gray-900">
        @if ($isEditing)
            {{-- Edit Role Card --}}
            <div
                class="fi-card relative rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-gray-700">
                <form wire:submit.prevent="updateRole" class="fi-card-body p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="fi-card-header text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Role</h2>
                        <button type="button" wire:click="cancelEdit"
                            class="fi-btn-icon flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Role Name --}}
                    <div class="mb-6">
                        <label for="roleName"
                            class="fi-input-label block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role
                            Name</label>
                        <input type="text" id="roleName" wire:model="roleName" style="margin: 10px 0px;" class="fi-input w-full p-3 border border-gray-300 rounded-lg bg-white text-gray-900 
                                dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 
                                focus:border-primary-500 dark:focus:ring-primary-500 dark:focus:border-primary-500 
                                transition duration-200" required>
                    </div>

                    {{-- Permissions --}}
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2" style="gap: 6px">
                            @foreach($groupedPermissions as $group => $permissions)
                                <div
                                    class="fi-permission-group p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    <h3
                                        class="fi-permission-group-title font-semibold text-lg text-gray-800 dark:text-gray-100 mb-3">
                                        {{ ucfirst($group) }}
                                    </h3>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($permissions as $permission)
                                            <label class="fi-checkbox flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                                <input type="checkbox" wire:model="selectedPermissions"
                                                    value="{{ $permission['name'] }}" class="fi-checkbox-input w-5 h-5 border border-gray-300 rounded text-primary-600 
                                                                focus:ring-primary-500 dark:border-gray-500 dark:bg-gray-600 
                                                                dark:checked:bg-primary-500 dark:checked:border-primary-500">
                                                <span
                                                    class="fi-checkbox-label text-sm">{{ ucfirst(explode('.', $permission['name'])[1]) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="fi-form-actions flex gap-4" style="margin: 10px 0px;">
                        <button type="submit" class="fi-btn fi-btn-primary flex-1 bg-primary-600 text-white py-3 px-6 rounded-lg 
                                hover:bg-primary-700 transition duration-300 focus-visible:outline focus-visible:outline-2 
                                focus-visible:outline-offset-2 focus-visible:outline-primary-600 shadow-sm">
                            Update Role
                        </button>
                        <button type="button" wire:click="cancelEdit" class="fi-btn fi-btn-secondary flex-1 bg-white text-gray-900 py-3 px-6 border border-gray-300 rounded-lg 
                                hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 
                                dark:hover:bg-gray-600 transition duration-300 shadow-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Roles Table --}}
            <div
                class="fi-card relative overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-gray-700">
                <div class="fi-card-body p-6">
                    <table class="fi-table w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="fi-table-header px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    Role Name</th>
                                <th scope="col"
                                    class="fi-table-header px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    Permissions</th>
                                <th scope="col"
                                    class="fi-table-header px-6 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-800">
                            @foreach($roles as $role)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-200">
                                    <td class="fi-table-cell whitespace-nowrap px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $role['name'] }}
                                    </td>
                                    <td class="fi-table-cell px-6 py-4 text-gray-900 dark:text-gray-100">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($role['permissions'] as $permission)
                                                <span
                                                    class="fi-badge inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                                    {{ $permission }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="fi-table-cell whitespace-nowrap px-6 py-4 text-center">
                                        <div class="flex justify-center gap-3">
                                            <button wire:click="editRole({{ $role['id'] }})"
                                                class="fi-btn fi-btn-icon p-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button wire:click="deleteRole({{ $role['id'] }})"
                                                class="fi-btn fi-btn-icon p-2 text-danger-600 hover:text-danger-700 dark:text-danger-400 dark:hover:text-danger-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>