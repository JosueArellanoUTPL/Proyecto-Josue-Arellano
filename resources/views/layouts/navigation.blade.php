<aside class="sidebar" data-sidebar>
    {{-- Menu lateral fijo: aqui quedan visibles los modulos principales. --}}
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

        {{-- Estos accesos son de consulta y seguimiento para los roles permitidos. --}}
        <div class="sidebar-section">Seguimiento</div>

        <a href="{{ route('seguimiento.metas') }}"
           class="side-link {{ request()->routeIs('seguimiento.metas', 'seguimiento.meta.show') ? 'active' : '' }}">
            Metas
        </a>

        <a href="{{ route('seguimiento.organizacion') }}"
           class="side-link {{ request()->routeIs('seguimiento.organizacion*') ? 'active' : '' }}">
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

        {{-- Responsable de planificacion y admin ven estos modulos CRUD. --}}
        @if(auth()->user()->canManagePlanning())
            <div class="sidebar-section">Planificacion</div>

            <a href="{{ route('entidades.index') }}"
               class="side-link {{ request()->routeIs('entidades.*') ? 'active' : '' }}">
                Entidades
            </a>

            <a href="{{ route('programas.index') }}"
               class="side-link {{ request()->routeIs('programas.*') ? 'active' : '' }}">
                Programas
            </a>

            <a href="{{ route('proyectos.index') }}"
               class="side-link {{ request()->routeIs('proyectos.*') ? 'active' : '' }}">
                Proyectos
            </a>

            <a href="{{ route('plans.index') }}"
               class="side-link {{ request()->routeIs('plans.*') ? 'active' : '' }}">
                Planes
            </a>

            <a href="{{ route('metas.index') }}"
               class="side-link {{ request()->routeIs('metas.*') ? 'active' : '' }}">
                Metas CRUD
            </a>

            <a href="{{ route('indicadores.index') }}"
               class="side-link {{ request()->routeIs('indicadores.*') ? 'active' : '' }}">
                Indicadores
            </a>

            <a href="{{ route('alineaciones.index') }}"
               class="side-link {{ request()->routeIs('alineaciones.*') ? 'active' : '' }}">
                Alineaciones
            </a>

            <div class="sidebar-section">Catalogos</div>

            <a href="{{ route('pdn.index') }}"
               class="side-link {{ request()->routeIs('pdn.*') ? 'active' : '' }}">
                PND / PDN
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

        {{-- Solo admin ve seguridad. --}}
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

    {{-- Usuario actual y cierre de sesion. --}}
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
