<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="text-red-500 mb-12 bg-red-200 p-6 rounded-sm">{{ $errors->first() }}</div>
            @endif
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Welcome to your dashboard!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
