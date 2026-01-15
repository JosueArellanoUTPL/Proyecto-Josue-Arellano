<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Grid 2 columnas: izquierda KPIs (alto completo), derecha 2 cards iguales --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:items-stretch">

                {{-- IZQUIERDA: 1 card grande (misma altura que las 2 de la derecha) --}}
                <div class="bg-white shadow rounded p-6 h-full">
                    <h3 class="text-lg font-semibold mb-4">Resumen del sistema</h3>

                    {{-- KPIs en grid interno --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="border rounded p-4">
                            <div class="text-sm text-gray-500">Planes activos</div>
                            <div class="text-3xl font-bold text-gray-800">{{ $kpis['planes_activos'] ?? 0 }}</div>
                        </div>

                        <div class="border rounded p-4">
                            <div class="text-sm text-gray-500">Metas registradas</div>
                            <div class="text-3xl font-bold text-gray-800">{{ $kpis['metas'] ?? 0 }}</div>
                        </div>

                        <div class="border rounded p-4">
                            <div class="text-sm text-gray-500">Indicadores definidos</div>
                            <div class="text-3xl font-bold text-gray-800">{{ $kpis['indicadores'] ?? 0 }}</div>
                        </div>

                        <div class="border rounded p-4">
                            <div class="text-sm text-gray-500">Alineaciones</div>
                            <div class="text-3xl font-bold text-gray-800">{{ $kpis['alineaciones'] ?? 0 }}</div>
                        </div>

                        <div class="border rounded p-4">
                            <div class="text-sm text-gray-500">Programas</div>
                            <div class="text-3xl font-bold text-gray-800">{{ $kpis['programas'] ?? 0 }}</div>
                        </div>

                        <div class="border rounded p-4">
                            <div class="text-sm text-gray-500">Proyectos</div>
                            <div class="text-3xl font-bold text-gray-800">{{ $kpis['proyectos'] ?? 0 }}</div>
                        </div>
                    </div>

                    {{-- Nota opcional bonita --}}
                    <div class="mt-6 text-sm text-gray-500">
                        Este panel resume el estado general del sistema y su nivel de alineación.
                    </div>
                </div>

                {{-- DERECHA: 2 cards iguales apiladas (cada una ocupa 1/2 del alto) --}}
                <div class="flex flex-col gap-6 h-full">

                    {{-- Card 1: Dona --}}
                    <div class="bg-white shadow rounded p-6 flex-1">
                        <h3 class="text-lg font-semibold mb-4 text-center">
                            Metas alineadas vs no alineadas
                        </h3>

                        {{-- Contenedor con altura controlada --}}
                        <div class="h-64 flex justify-center items-center">
                            <div class="w-56 h-56">
                                <canvas id="metasChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Barras --}}
                    <div class="bg-white shadow rounded p-6 flex-1">
                        <h3 class="text-lg font-semibold mb-4 text-center">
                            Indicadores por Plan
                        </h3>

                        {{-- Contenedor con altura controlada --}}
                        <div class="h-64">
                            <canvas id="indicadoresPorPlanChart"></canvas>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Dona: Metas alineadas vs no alineadas
        new Chart(document.getElementById('metasChart'), {
            type: 'doughnut',
            data: {
                labels: ['Metas alineadas', 'Metas no alineadas'],
                datasets: [{
                    data: [
                        {{ $metasAlineadas ?? 0 }},
                        {{ $metasNoAlineadas ?? 0 }}
                    ],
                    backgroundColor: ['#16a34a', '#dc2626'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Barras: Indicadores por Plan
        const labelsPlanes = @json(($indicadoresPorPlan ?? collect())->map(fn($p) => $p->codigo)->toArray());
        const dataIndicadores = @json(($indicadoresPorPlan ?? collect())->map(fn($p) => (int)$p->indicadores_count)->toArray());

        new Chart(document.getElementById('indicadoresPorPlanChart'), {
            type: 'bar',
            data: {
                labels: labelsPlanes,
                datasets: [{
                    label: 'Indicadores',
                    data: dataIndicadores
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</x-app-layout>
