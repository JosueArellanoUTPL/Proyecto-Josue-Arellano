<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Actividad Operativa (POA)</h2></x-slot>
    <div class="py-6"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow rounded p-6">
        <x-form-errors />
        <form method="POST" action="{{ route('actividades-operativas.update', $actividadOperativa) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('actividades-operativas.partials.form-fields')
            <div class="flex gap-3 mt-6"><button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black font-semibold rounded transition">Actualizar</button><a href="{{ route('actividades-operativas.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-black font-semibold rounded transition">Volver</a></div>
        </form>
    </div></div></div>
</x-app-layout>
