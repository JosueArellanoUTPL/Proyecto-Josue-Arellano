<x-app-layout>
    {{-- Seccion de encabezado. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Alineación</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">
                <div class="title">Editar Alineación</div>
                <div class="muted" style="margin-top:6px;">
                    Se actualiza la meta y los instrumentos estratégicos asociados.
                </div>

                <x-form-errors />

                {{-- Formulario de edicion. --}}
                <form method="POST" action="{{ route('alineaciones.update', $alineacion->id) }}" class="card" style="margin-top:16px;">
                    @csrf
                    @method('PUT')
                    @include('alineaciones.partials.form-fields')

                    <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-green">Actualizar</button>
                        <a href="{{ route('alineaciones.index') }}" class="btn">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
