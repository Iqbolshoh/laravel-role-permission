<x-filament-panels::page>
    <div class="p-6 space-y-6">
        {{-- User Creation Form --}}
        <form wire:submit.prevent="create" class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-900">

            {{-- Name --}}
            <div class="mb-4">
                <label class="block font-semibold">Full Name:</label>
                <input type="text" wire:model.defer="name"
                    class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                    required>
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block font-semibold">Email Address:</label>
                <input type="email" wire:model.defer="email"
                    class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                    required>
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block font-semibold">Password:</label>
                <input type="password" wire:model.defer="password"
                    class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
                    required>
            </div>

            {{-- Role Selection --}}
            <div class="mb-4">
                <label class="block font-semibold">Select Role:</label>
                <select wire:model.defer="role" 
                    class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">
                    <option value="" selected disabled>Select a Role</option>
                    @foreach(\Spatie\Permission\Models\Role::pluck('name') as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-4">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold py-2 px-4 rounded-xl border-2 border-blue-700 shadow-2xl hover:shadow-2xl hover:from-blue-600 hover:to-blue-800 hover:border-blue-800 dark:bg-gradient-to-r dark:from-gray-900 dark:to-gray-950 dark:border-gray-700 dark:text-white dark:hover:from-gray-800 dark:hover:to-gray-900 dark:hover:border-gray-600 transition duration-300 ease-in-out transform hover:scale-105 active:scale-95">
                    Create User
                </button>

                <button type="button" wire:click="reset('name', 'email', 'password', 'role')"
                    class="w-full bg-gray-500 text-white font-semibold py-2 px-4 rounded-xl border-2 border-gray-700 shadow-lg hover:bg-gray-600 transition duration-300 ease-in-out transform hover:scale-105 active:scale-95">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</x-filament-panels::page>