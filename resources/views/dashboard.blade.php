<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Welcome Message -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            Welcome back, {{ Auth::user()->name }}! 👋
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Here's what you need to focus on today.
                        </p>
                    </div>

                    <!-- Task Manager Component -->
                    <livewire:task-manager />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>