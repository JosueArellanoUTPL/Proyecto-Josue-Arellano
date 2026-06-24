<aside class="sidebar" data-sidebar>
    {{-- Navegacion principal. --}}
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            <span>SIPeIP</span>
        </a>
        <div class="sidebar-role">{{ auth()->user()->roleLabel() }}</div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Principal</div>

        <a href="{{ route('dashboard') }}"
           class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        {{-- Modulos de planificacion. --}}
        @if(auth()->user()->canManagePlanning())
            <div class="sidebar-section">Planificación institucional</div>

            <a href="{{ route('entidades.index') }}"
               class="side-link {{ request()->routeIs('entidades.*') ? 'active' : '' }}">
                Entidades
            </a>

            <a href="{{ route('planes.index') }}"
               class="side-link {{ request()->routeIs('planes.*') ? 'active' : '' }}">
                Planes
            </a>

            <a href="{{ route('metas.index') }}"
               class="side-link {{ request()->routeIs('metas.*') ? 'active' : '' }}">
                Metas
            </a>

            <a href="{{ route('indicadores.index') }}"
               class="side-link {{ request()->routeIs('indicadores.*') ? 'active' : '' }}">
                Indicadores
            </a>

            <a href="{{ route('programas.index') }}"
               class="side-link {{ request()->routeIs('programas.*') ? 'active' : '' }}">
                Programas
            </a>

            <a href="{{ route('proyectos.index') }}"
               class="side-link {{ request()->routeIs('proyectos.*') ? 'active' : '' }}">
                Proyectos
            </a>

            <div class="sidebar-section">Alineación estratégica</div>

            <a href="{{ route('alineaciones.index') }}"
               class="side-link {{ request()->routeIs('alineaciones.*') ? 'active' : '' }}">
                Alineaciones
            </a>

            <a href="{{ route('pdn.index') }}"
               class="side-link {{ request()->routeIs('pdn.*') ? 'active' : '' }}">
                PND
            </a>

            <a href="{{ route('ods.index') }}"
               class="side-link {{ request()->routeIs('ods.*') ? 'active' : '' }}">
                ODS
            </a>

            <a href="{{ route('objetivos-estrategicos.index') }}"
               class="side-link {{ request()->routeIs('objetivos-estrategicos.*') ? 'active' : '' }}">
                Objetivos
            </a>
        @endif

        {{-- Modulos de consulta. --}}
        <div class="sidebar-section">Seguimiento y consulta</div>

        <a href="{{ route('seguimiento.metas') }}"
           class="side-link {{ request()->routeIs('seguimiento.metas', 'seguimiento.meta.show') ? 'active' : '' }}">
            Seguimiento de metas
        </a>

        <a href="{{ route('seguimiento.organizacion') }}"
           class="side-link {{ request()->routeIs('seguimiento.organizacion*', 'seguimiento.programa.show', 'seguimiento.proyecto.show') ? 'active' : '' }}">
            Organizacion
        </a>

        <a href="{{ route('seguimiento.trazabilidad') }}"
           class="side-link {{ request()->routeIs('seguimiento.trazabilidad') ? 'active' : '' }}">
            Trazabilidad
        </a>

        <a href="{{ route('reportes.index') }}"
           class="side-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
            Reportes
        </a>

        {{-- Modulos de administrador. --}}
        @if(auth()->user()->isAdmin())
            <div class="sidebar-section">Seguridad</div>

            <a href="{{ route('usuarios.index') }}"
               class="side-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                Usuarios
            </a>

            <a href="{{ route('auditoria.index') }}"
               class="side-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                Auditoria
            </a>
        @endif
    </nav>

    {{-- Sesion del usuario. --}}
    <div class="sidebar-user">
        <a href="{{ route('profile.edit') }}" class="side-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            {{ Auth::user()->name }}
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="side-link logout-link">
                Cerrar sesion
            </button>
        </form>
    </div>
</aside>
