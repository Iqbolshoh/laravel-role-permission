<x-filament-panels::page>
    {{-- User info --}}
    <div class="p-6 bg-white rounded-xl shadow-md dark:bg-gray-900">
        <div class="flex items-center gap-2">
            <img class="w-16 h-16 rounded-full border-2 border-primary-500"
                src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&color=FFFFFF&background=09090b"
                alt="{{ $name }}">
            <div class="ml-2">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $email }}</p>
                <div class="mt-1 space-x-2">
                    @forelse ($roles as $role)
                        <span class="inline-block px-3 py-1 text-xs font-semibold text-white bg-primary-600 rounded-full">
                            {{ $role }}
                        </span>
                    @empty
                        <span
                            class="inline-block px-3 py-1 text-xs font-semibold text-gray-500 bg-gray-200 rounded-full dark:bg-gray-700 dark:text-gray-400">
                            No roles assigned
                        </span>
                    @endforelse
                </div>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Joined on: {{ $joined }}</p>
    </div>
</x-filament-panels::page>