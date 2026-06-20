<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Proyecto</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6">
                {{-- Mensajes de validacion. --}}
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('proyectos.store') }}">
                    {{-- Formulario de datos. --}}
                    @csrf

                    {{-- Campos del proyecto. --}}
                    @include('proyectos.partials.form-fields')

                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded transition">
                            Guardar
                        </button>
                        <a href="{{ route('proyectos.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
