<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SIPeIP Academico') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        {{-- Landing publica: reemplaza la pantalla generica de Laravel. --}}
        <main class="landing-page">
            <header class="landing-nav">
                <a href="{{ url('/') }}" class="landing-brand">
                    <x-application-logo class="landing-logo" />
                    <span>SIPeIP Academico</span>
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

                    <div class="landing-cta">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-green">Entrar al sistema</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-green">Ingresar</a>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="btn">Recuperar clave</a>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Vista previa visual del sistema, no es funcional; solo presenta la idea. --}}
                <div class="landing-preview" aria-label="Vista previa del sistema">
                    <div class="preview-top">
                        <div>
                            <div class="title">Panel Ejecutivo</div>
                            <div class="muted">Resumen institucional</div>
                        </div>
                        <span class="chip">Demo</span>
                    </div>

                    <div class="preview-grid">
                        <div class="mini-stat">
                            <span>Metas</span>
                            <strong>12</strong>
                        </div>
                        <div class="mini-stat">
                            <span>Indicadores</span>
                            <strong>28</strong>
                        </div>
                    </div>

                    <div class="preview-chart">
                        <div style="height:48px"></div>
                        <div style="height:76px"></div>
                        <div style="height:58px"></div>
                        <div style="height:96px"></div>
                        <div style="height:70px"></div>
                    </div>

                    <div class="progress">
                        <div style="width:72%; background:var(--green)"></div>
                    </div>
                    <div class="muted" style="margin-top:8px;">Avance institucional estimado: 72%</div>
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
