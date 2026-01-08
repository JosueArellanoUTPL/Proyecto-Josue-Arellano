<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            {{-- Tarjetas de acceso --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('entidades.index') }}"
                   class="block p-6 bg-white shadow rounded text-center hover:bg-gray-100">
                    <h3 class="text-lg font-semibold">Entidades</h3>
                    <p class="text-sm text-gray-600">Gestión de entidades</p>
                </a>

                <a href="{{ route('programas.index') }}"
                   class="block p-6 bg-white shadow rounded text-center hover:bg-gray-100">
                    <h3 class="text-lg font-semibold">Programas</h3>
                    <p class="text-sm text-gray-600">Gestión de programas</p>
                </a>

                <a href="{{ route('proyectos.index') }}"
                   class="block p-6 bg-white shadow rounded text-center hover:bg-gray-100">
                    <h3 class="text-lg font-semibold">Proyectos</h3>
                    <p class="text-sm text-gray-600">Gestión de proyectos</p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
