<x-filament-panels::page>
    <div class="p-6 space-y-6">

        {{-- Edit Modal --}}
        @if ($isEditMode)
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-xl w-full max-w-lg">
                    <form wire:submit.prevent="update">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">Edit User</h2>

                        <div class="mb-4">
                            <label class="block font-semibold text-gray-900 dark:text-gray-200">Full Name:</label>
                            <input type="text" wire:model.defer="name"
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-200"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-semibold text-gray-900 dark:text-gray-200">Email Address:</label>
                            <input type="email" wire:model.defer="email"
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-200"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block font-semibold text-gray-900 dark:text-gray-200">Password:</label>
                            <input type="password" wire:model.defer="password"
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                        </div>

                        <div class="mb-4">
                            <label class="block font-semibold text-gray-900 dark:text-gray-200">Confirm Password:</label>
                            <input type="password" wire:model.defer="password_confirmation"
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                        </div>

                        <div class="mb-4">
                            <label class="block font-semibold text-gray-900 dark:text-gray-200">Select Role:</label>
                            <select wire:model.defer="role"
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-200">
                                <option value="" disabled>Select a Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit"
                                class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 dark:hover:bg-blue-500 transition duration-300">
                                Update User
                            </button>

                            <button type="button" wire:click="resetForm"
                                class="w-full bg-gray-500 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-gray-600 dark:hover:bg-gray-400 transition duration-300">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- User List --}}
        <div class="mt-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200">Users List</h2>
            <table class="w-full mt-4 border-collapse border border-gray-300 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="border p-2 text-left text-gray-800 dark:text-gray-200">Name</th>
                        <th class="border p-2 text-left text-gray-800 dark:text-gray-200">Email</th>
                        <th class="border p-2 text-left text-gray-800 dark:text-gray-200">Role</th>
                        <th class="border p-2 text-center text-gray-800 dark:text-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-200">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-200">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-200">
                                {{ $user->roles->pluck('name')->first() }}
                            </td>
                            <td class="px-4 py-3 text-center flex gap-2 justify-center">
                                <button wire:click="editUser({{ $user->id }})"
                                    class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-400 dark:hover:bg-yellow-600">Edit</button>
                                <button wire:click="deleteUser({{ $user->id }})"
                                    class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-400 dark:hover:bg-red-600">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>