<x-app-layout>
    {{-- Seccion de encabezado. --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Alineación</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">
                <div class="title">Configuración de Alineación</div>
                <div class="muted" style="margin-top:6px;">
                    Se registra una relación entre una meta y los instrumentos estratégicos.
                </div>

                <x-form-errors />

                {{-- Formulario de creacion. --}}
                <form method="POST" action="{{ route('alineaciones.store') }}" class="card" style="margin-top:16px;">
                    @csrf
                    @include('alineaciones.partials.form-fields')

                    <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-green">Guardar</button>
                        <a href="{{ route('alineaciones.index') }}" class="btn">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
