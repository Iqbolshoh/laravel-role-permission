<x-filament-panels::page>
    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="p-3 text-green-700 bg-green-200 dark:bg-green-900 dark:text-green-300 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('filament.pages.create-role') }}"
            class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-white font-semibold">Role Name:</label>
                <input type="text" id="role-name" name="name"
                    class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                    required>
            </div>

            <div class="mb-4 space-y-6">
                @foreach($this->groupedPermissions as $group => $permissions)
                    <div class="p-4 border rounded-md shadow-sm bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200">{{ ucfirst($group) }}</h3>
                            <label class="flex items-center space-x-2 text-gray-700 dark:text-white font-medium">
                                <input type="checkbox"
                                    class="select-all-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-400 dark:border-gray-600"
                                    data-group="{{ $group }}">
                                <span style="display: block; margin-left: 5px;">All</span>
                            </label>
                        </div>

                        <div class="flex flex-wrap gap-4 mt-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-center space-x-3 text-gray-700 dark:text-white font-medium">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="permission-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-400 dark:border-gray-600"
                                        data-group="{{ $group }}">
                                    <span style="display: block; margin-left: 5px;">
                                        {{ ucfirst(explode('.', $permission->name)[1]) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-center">
                <button type="submit"
                    class="w-full bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                    Create Role
                </button>
            </div>
        </form>
    </div>
</x-filament-panels::page>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.select-all-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const group = this.getAttribute('data-group');
                const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        });
    });
</script>