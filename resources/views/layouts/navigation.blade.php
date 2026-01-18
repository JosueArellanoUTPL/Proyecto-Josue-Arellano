<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>

                <!-- Desktop Menu -->
                <div class="hidden sm:flex sm:items-center sm:ms-10 sm:gap-2">
                    <!-- Dashboard -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    @auth
                        <!-- Seguimiento (Admin y Técnico) -->
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button type="button"
                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-md
                                           text-sm font-medium text-gray-700
                                           hover:bg-gray-100 hover:text-gray-900
                                           transition">
                                    Seguimiento
                                    <svg class="h-4 w-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293L10 12l4.707-4.707" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('seguimiento.metas')">
                                    Seguimiento de Metas
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('seguimiento.organizacion')">
                                    Organización (Entidades)
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>

                        @if(auth()->user()->role === 'admin')
                            <!-- Administración (solo Admin) -->
                            <x-dropdown align="left" width="56">
                                <x-slot name="trigger">
                                    <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-md
                                               text-sm font-medium text-gray-700
                                               hover:bg-gray-100 hover:text-gray-900
                                               transition">
                                        Administración
                                        <svg class="h-4 w-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293L10 12l4.707-4.707" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Bloque: Catálogos estratégicos -->
                                    <div class="px-4 py-2 text-xs text-gray-500">Planificación</div>
                                    <x-dropdown-link :href="route('pdn.index')">PND / PDN</x-dropdown-link>
                                    <x-dropdown-link :href="route('ods.index')">ODS</x-dropdown-link>
                                    <x-dropdown-link :href="route('objetivos-estrategicos.index')">Objetivos Estratégicos</x-dropdown-link>

                                    <div class="border-t my-2"></div>

                                    <!-- Bloque: Planificación institucional -->
                                    <div class="px-4 py-2 text-xs text-gray-500">Plan y Seguimiento</div>
                                    <x-dropdown-link :href="route('plans.index')">Planes</x-dropdown-link>
                                    <x-dropdown-link :href="route('metas.index')">Metas</x-dropdown-link>
                                    <x-dropdown-link :href="route('indicadores.index')">Indicadores</x-dropdown-link>
                                    <x-dropdown-link :href="route('alineaciones.index')">Alineaciones</x-dropdown-link>

                                    <div class="border-t my-2"></div>

                                    <!-- Bloque: Ejecución -->
                                    <div class="px-4 py-2 text-xs text-gray-500">Ejecución</div>
                                    <x-dropdown-link :href="route('programas.index')">Programas</x-dropdown-link>
                                    <x-dropdown-link :href="route('proyectos.index')">Proyectos</x-dropdown-link>
                                    <x-dropdown-link :href="route('entidades.index')">Entidades</x-dropdown-link>

                                    <div class="border-t my-2"></div>

                                    <!-- Bloque: Seguridad -->
                                    <div class="px-4 py-2 text-xs text-gray-500">Seguridad</div>
                                    <x-dropdown-link :href="route('usuarios.index')">Usuarios</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button"
                            class="inline-flex items-center gap-1 px-3 py-2 rounded-md
                                   text-sm font-medium text-gray-700
                                   hover:bg-gray-100 hover:text-gray-900
                                   transition">
                            {{ Auth::user()->name }}
                            <svg class="h-4 w-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293L10 12l4.707-4.707" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
