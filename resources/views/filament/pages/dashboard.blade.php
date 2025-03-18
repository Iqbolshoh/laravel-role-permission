<x-filament-panels::page>
    <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-900">
        <div class="flex items-center space-x-4">
            <img class="w-16 h-16 rounded-full border-2 border-primary-500"
                src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random" alt="{{ $name }}">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $email }}</p>
                <span
                    class="inline-block px-3 py-1 text-xs font-semibold text-white bg-primary-600 rounded-full">{{ $role }}</span>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Qo‘shilgan sana: {{ $joined }}</p>
    </div>
</x-filament-panels::page>