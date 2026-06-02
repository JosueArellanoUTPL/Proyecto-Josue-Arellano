<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reportes
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="wrap">
                {{-- Pantalla principal de reportes: solo tiene accesos a cada reporte. --}}
                <div class="title">Generacion de reportes</div>
                <div class="muted" style="margin-top:6px;">
                    Reportes basicos para consulta, seguimiento y respaldo academico del sistema.
                </div>

                {{-- Cada tarjeta entra a un reporte diferente. --}}
                <div class="report-grid">
                    <a href="{{ route('reportes.institucional') }}" class="card report-card">
                        <div class="title">Reporte institucional</div>
                        <div class="muted" style="margin-top:6px;">
                            Resumen general de entidades, planes, metas, proyectos y avances.
                        </div>
                    </a>

                    <a href="{{ route('reportes.metas') }}" class="card report-card">
                        <div class="title">Reporte de metas</div>
                        <div class="muted" style="margin-top:6px;">
                            Avance de metas por entidad, plan e indicadores asociados.
                        </div>
                    </a>

                    <a href="{{ route('reportes.proyectos') }}" class="card report-card">
                        <div class="title">Reporte de proyectos</div>
                        <div class="muted" style="margin-top:6px;">
                            Estado actual de proyectos, ultimos avances y evidencias.
                        </div>
                    </a>

                    <a href="{{ route('reportes.trazabilidad') }}" class="card report-card">
                        <div class="title">Reporte de trazabilidad</div>
                        <div class="muted" style="margin-top:6px;">
                            Relacion entre metas, indicadores, ODS, PDN y objetivos estrategicos.
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
