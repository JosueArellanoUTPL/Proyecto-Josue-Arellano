<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Sistema de Planificación') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        {{-- Portada publica. --}}
        <main class="landing-page">
            <header class="landing-nav">
                <a href="{{ url('/') }}" class="landing-brand">
                    <x-application-logo class="landing-logo" />
                    <span>Sistema de Planificación</span>
                </a>

                <div class="landing-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-green">Ir al dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn">Iniciar sesion</a>
                    @endauth
                </div>
            </header>

            <section class="landing-hero">
                <div class="landing-copy">
                    <div class="pill">Sistema de planificacion y gestion institucional</div>
                    <h1>Gestion estrategica y seguimiento.</h1>
                    <p>
                        Plataforma para organizar planes, metas, indicadores, avances,
                        trazabilidad, auditoria y reportes en un solo entorno de consulta.
                    </p>

                </div>

                {{-- Grafico decorativo. --}}
                <div class="landing-preview" aria-hidden="true">
                    <div class="preview-chart">
                        <div style="height:34%"></div>
                        <div style="height:58%"></div>
                        <div style="height:45%"></div>
                        <div style="height:76%"></div>
                        <div style="height:62%"></div>
                        <div style="height:88%"></div>
                        <div style="height:70%"></div>
                    </div>
                </div>
            </section>

            <section class="landing-modules">
                <div class="card">
                    <div class="title">Planificacion</div>
                    <div class="muted">Entidades, planes, metas, indicadores y alineaciones.</div>
                </div>

                <div class="card">
                    <div class="title">Seguimiento</div>
                    <div class="muted">Avances de indicadores, proyectos y evidencias.</div>
                </div>

                <div class="card">
                    <div class="title">Reportes y auditoria</div>
                    <div class="muted">Consulta, impresion, trazabilidad y registro de acciones.</div>
                </div>
            </section>
        </main>
    </body>
</html>
