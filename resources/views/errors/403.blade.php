<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Access Denied') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg text-center p-8">
                <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">Oops! Permission Denied</h2>
                <p class="text-gray-600 mb-6">
                    You do not have the required permissions to access this page.
                </p>

                <a href="{{ route('dashboard') }}"
                   class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
