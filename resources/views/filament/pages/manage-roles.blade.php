<x-filament-panels::page>
    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="p-3 text-green-700 bg-green-200 dark:bg-green-900 dark:text-green-300 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
            <table class="w-full border-collapse border border-gray-300 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th
                            class="border border-gray-300 dark:border-gray-700 px-4 py-3 text-left text-gray-800 dark:text-gray-200">
                            Role Name</th>
                        <th
                            class="border border-gray-300 dark:border-gray-700 px-4 py-3 text-left text-gray-800 dark:text-gray-200">
                            Permissions</th>
                        <th
                            class="border border-gray-300 dark:border-gray-700 px-4 py-3 text-center text-gray-800 dark:text-gray-200">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->roles as $role)
                        @if($role['name'] !== 'superadmin')
                            <tr id="role-{{ $role['id'] }}" class="border border-gray-300 dark:border-gray-700">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-200">{{ $role['name'] }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-200">{{ implode(', ', $role['permissions']) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="editRole({{ $role['id'] }})"
                                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-200">Edit</button>
                                    <button onclick="deleteRole({{ $role['id'] }})"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">Delete</button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($isEditing)
            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg mt-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">Edit Role</h2>
                <input type="text" wire:model="roleName"
                    class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                    required>

                <!-- "Select All" toggle tugmachasi -->
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-2">Permissions</h3>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @foreach($groupedPermissions as $group => $permissions)
                        <div class="p-4 border rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200">{{ ucfirst($group) }}</h3>
                                <label class="flex items-center space-x-2 text-gray-700 dark:text-white font-medium">
                                    <input type="checkbox"
                                        class="group-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-400 dark:border-gray-600"
                                        data-group="{{ $group }}" onclick="toggleGroupPermissions('{{ $group }}')">
                                    <span style="display: block; margin-left: 5px;"> All</span>
                                </label>
                            </div>
                            <div class="flex flex-wrap gap-4 mt-3">
                                @foreach($permissions as $permission)
                                    <label class="flex items-center space-x-3 text-gray-700 dark:text-white font-medium">
                                        <input type="checkbox"
                                            class="permission-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-400 dark:border-gray-600"
                                            data-group="{{ $group }}" wire:model="selectedPermissions"
                                            value="{{ $permission['name'] }}">
                                        <span
                                            style="display: block; margin-left: 5px;">{{ ucfirst(explode('.', $permission['name'])[1]) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button wire:click="updateRole"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200">
                    Save Changes</button>
                <button wire:click="$set('isEditing', false)"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200 ml-2">Cancel</button>
            </div>
        @endif
    </div>
</x-filament-panels::page>

<script>
    function toggleGroupPermissions(group) {
        let groupChecked = document.querySelector(`.group-checkbox[data-group="${group}"]`).checked;
        let selectedPermissions = @this.get('selectedPermissions') || [];

        document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`).forEach(el => {
            el.checked = groupChecked;
            if (groupChecked) {
                if (!selectedPermissions.includes(el.value)) {
                    selectedPermissions.push(el.value);
                }
            } else {
                selectedPermissions = selectedPermissions.filter(permission => permission !== el.value);
            }
        });

        @this.set('selectedPermissions', selectedPermissions);
    }

    function updateSelectAllCheckbox() {
        let allPermissions = document.querySelectorAll('.permission-checkbox');
        let allChecked = Array.from(allPermissions).every(el => el.checked);
        document.getElementById('select-all').checked = allChecked;
    }

    function deleteRole(roleId) {
        if (!confirm('Are you sure you want to delete this role?')) return;
        fetch(`/roles/${roleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.filament.notifications.notify({ title: 'Success', message: 'Role deleted successfully!', type: 'success' });
                    document.getElementById(`role-${roleId}`).remove();
                } else {
                    window.filament.notifications.notify({ title: 'Error', message: 'Failed to delete role!', type: 'danger' });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.filament.notifications.notify({ title: 'Error', message: 'An error occurred!', type: 'danger' });
            });
    }
</script>