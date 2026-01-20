<x-app-layout>

    {{-- Encabezado --}}
    <x-slot name="header">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-white leading-tight">
                    Panel del Administrador
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    Accesos rápidos y administración del sistema.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-100"
         x-data="{ sidebarOpen: false }"
         @keydown.escape.window="sidebarOpen=false"
    >
        <div class="flex">

            {{-- Overlay móvil --}}
            <div
                x-show="sidebarOpen"
                x-cloak
                class="fixed inset-0 z-40 bg-black/40 lg:hidden"
                @click="sidebarOpen=false"
            ></div>

            {{-- SIDE MENU --}}
            <aside
                class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-[#691C32] text-white shadow-2xl
                       transform transition-transform duration-200 ease-out
                       lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                {{-- Brand / Header --}}
                <div class="px-6 py-6 border-b border-white/10">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-white/70">Panel</p>
                            <h3 class="text-xl font-bold tracking-wide">Administrador</h3>
                        </div>

                        <button type="button"
                                class="lg:hidden h-10 w-10 rounded-xl border border-white/15 bg-white/10 hover:bg-white/15 transition inline-flex items-center justify-center"
                                @click="sidebarOpen=false">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Mini perfil --}}
                    <div class="mt-5 flex items-center gap-3">
                        <div class="h-11 w-11 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center">
                            <i class="fas fa-user-shield"></i>
                        </div>

                        <div class="min-w-0">
                            <p class="font-semibold leading-tight break-words">
                                {{ Auth::user()->name }}
                            </p>
                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-white/10 border border-white/10 text-white/90">
                                    <i class="fas fa-shield-halved text-[11px]"></i>
                                    Administrador
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navegación --}}
                <nav class="px-4 py-5 space-y-2">

                    @php
                        $itemBase = 'group flex items-center gap-3 px-4 py-3 rounded-2xl transition';
                        $itemIdle = 'hover:bg-white/10';
                        $itemActive = 'bg-white/12 ring-1 ring-white/15';
                        $iconBase = 'h-10 w-10 rounded-xl border border-white/10 flex items-center justify-center transition';
                        $iconIdle = 'bg-white/10 group-hover:bg-white/15';
                        $iconActive = 'bg-white/15';
                        $chev = 'fas fa-chevron-right ml-auto text-white/60 text-xs';
                    @endphp

                    {{-- Usuarios --}}
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ $itemBase }} {{ request()->routeIs('admin.users.*') ? $itemActive : $itemIdle }}">
                        <span class="{{ $iconBase }} {{ request()->routeIs('admin.users.*') ? $iconActive : $iconIdle }}">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold leading-tight">Gestión de Usuarios</p>
                            <p class="text-xs text-white/70">Altas, roles y permisos</p>
                        </div>
                        <i class="{{ $chev }}"></i>
                    </a>

                    {{-- Dependencias --}}
                    <a href="{{ route('admin.dependencias.index') }}"
                       class="{{ $itemBase }} {{ request()->routeIs('admin.dependencias.*') ? $itemActive : $itemIdle }}">
                        <span class="{{ $iconBase }} {{ request()->routeIs('admin.dependencias.*') ? $iconActive : $iconIdle }}">
                            <i class="fas fa-building"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold leading-tight">Dependencias</p>
                            <p class="text-xs text-white/70">Catálogo institucional</p>
                        </div>
                        <i class="{{ $chev }}"></i>
                    </a>

                    {{-- Catálogo FASP --}}
                    <a href="{{ route('admin.fasp.index') }}"
                       class="{{ $itemBase }} {{ request()->routeIs('admin.fasp.*') ? $itemActive : $itemIdle }}">
                        <span class="{{ $iconBase }} {{ request()->routeIs('admin.fasp.*') ? $iconActive : $iconIdle }}">
                            <i class="fas fa-book"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold leading-tight">Catálogo FASP</p>
                            <p class="text-xs text-white/70">Importación y estructura</p>
                        </div>
                        <i class="{{ $chev }}"></i>
                    </a>

                    {{-- Validación Expedientes --}}
                    <a href="{{ route('revision.index') }}"
                       class="{{ $itemBase }} {{ request()->routeIs('revision.*') ? $itemActive : $itemIdle }}">
                        <span class="{{ $iconBase }} {{ request()->routeIs('revision.*') ? $iconActive : $iconIdle }}">
                            <i class="fas fa-file-signature"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold leading-tight">Validación Expedientes</p>
                            <p class="text-xs text-white/70">Revisión de documentos</p>
                        </div>
                        <i class="{{ $chev }}"></i>
                    </a>

                    <div class="my-4 border-t border-white/10"></div>

                    {{-- Perfil --}}
                    <a href="{{ route('profile.edit') }}"
                       class="{{ $itemBase }} {{ request()->routeIs('profile.*') ? $itemActive : $itemIdle }}">
                        <span class="{{ $iconBase }} {{ request()->routeIs('profile.*') ? $iconActive : $iconIdle }}">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold leading-tight">Mi Perfil</p>
                            <p class="text-xs text-white/70">Datos de la cuenta</p>
                        </div>
                        <i class="{{ $chev }}"></i>
                    </a>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="pt-1">
                        @csrf
                        <button type="submit"
                                class="w-full {{ $itemBase }} {{ $itemIdle }} text-left">
                            <span class="{{ $iconBase }} {{ $iconIdle }}">
                                <i class="fas fa-right-from-bracket"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="font-semibold leading-tight">Cerrar Sesión</p>
                                <p class="text-xs text-white/70">Salir del sistema</p>
                            </div>
                        </button>
                    </form>

                </nav>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 lg:ml-0">
                <div class="p-6 lg:p-10 space-y-6">

                    {{-- Barra superior móvil --}}
                    <div class="lg:hidden flex items-center justify-between">
                        <button type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 shadow-sm hover:bg-gray-50 transition"
                                @click="sidebarOpen=true">
                            <i class="fas fa-bars text-[#9F2241]"></i>
                            <span class="font-medium text-gray-700">Menú</span>
                        </button>

                        <span class="text-sm text-gray-500">
                            {{ Auth::user()->name }}
                        </span>
                    </div>

                    {{-- Card principal --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <h1 class="text-2xl md:text-3xl font-bold text-[#691C32]">
                                        Bienvenid@ {{ Auth::user()->name }}
                                    </h1>
                                    <p class="text-gray-600 mt-2">
                                        Desde este panel puedes administrar usuarios, dependencias y configuraciones del sistema.
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.users.index') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#9F2241] text-white shadow-sm hover:bg-[#691C32] transition">
                                        <i class="fas fa-users"></i>
                                        Ir a Usuarios
                                    </a>
                                    <a href="{{ route('revision.index') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                                        <i class="fas fa-file-signature text-[#9F2241]"></i>
                                        Ir a Expedientes
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Tarjetas del Dashboard --}}
                        <div class="px-6 py-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                                {{-- Usuarios --}}
                                <a href="{{ route('admin.users.index') }}"
                                   class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                                    <div class="h-1 w-full bg-gradient-to-r from-[#9F2241] via-[#9F2241]/60 to-transparent"></div>
                                    <div class="p-5">
                                        <div class="flex items-start gap-3">
                                            <div class="h-11 w-11 rounded-2xl bg-[#9F2241]/10 border border-[#9F2241]/15 text-[#9F2241] flex items-center justify-center">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-gray-800 text-lg leading-tight">Usuarios</h4>
                                                <p class="text-gray-600 text-sm mt-1">Gestiona todos los usuarios registrados.</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#9F2241]">
                                            <span>Administrar</span>
                                            <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                                        </div>
                                    </div>
                                </a>

                                {{-- Dependencias --}}
                                <a href="{{ route('admin.dependencias.index') }}"
                                   class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                                    <div class="h-1 w-full bg-gradient-to-r from-[#9F2241] via-[#9F2241]/60 to-transparent"></div>
                                    <div class="p-5">
                                        <div class="flex items-start gap-3">
                                            <div class="h-11 w-11 rounded-2xl bg-[#9F2241]/10 border border-[#9F2241]/15 text-[#9F2241] flex items-center justify-center">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-gray-800 text-lg leading-tight">Dependencias</h4>
                                                <p class="text-gray-600 text-sm mt-1">Administra dependencias y asignaciones.</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#9F2241]">
                                            <span>Administrar</span>
                                            <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                                        </div>
                                    </div>
                                </a>

                                {{-- Catálogo FASP --}}
                                <a href="{{ route('admin.fasp.index') }}"
                                   class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                                    <div class="h-1 w-full bg-gradient-to-r from-[#9F2241] via-[#9F2241]/60 to-transparent"></div>
                                    <div class="p-5">
                                        <div class="flex items-start gap-3">
                                            <div class="h-11 w-11 rounded-2xl bg-[#9F2241]/10 border border-[#9F2241]/15 text-[#9F2241] flex items-center justify-center">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-gray-800 text-lg leading-tight">Catálogo FASP</h4>
                                                <p class="text-gray-600 text-sm mt-1">Importa, consulta y valida la estructura por Eje/Programa.</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#9F2241]">
                                            <span>Administrar</span>
                                            <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                                        </div>
                                    </div>
                                </a>

                                {{-- Validación Expedientes --}}
                                <a href="{{ route('revision.index') }}"
                                   class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                                    <div class="h-1 w-full bg-gradient-to-r from-[#9F2241] via-[#9F2241]/60 to-transparent"></div>
                                    <div class="p-5">
                                        <div class="flex items-start gap-3">
                                            <div class="h-11 w-11 rounded-2xl bg-[#9F2241]/10 border border-[#9F2241]/15 text-[#9F2241] flex items-center justify-center">
                                                <i class="fas fa-file-signature"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-gray-800 text-lg leading-tight">Expedientes</h4>
                                                <p class="text-gray-600 text-sm mt-1">Valida expedientes técnicos.</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#9F2241]">
                                            <span>Administrar</span>
                                            <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                                        </div>
                                    </div>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>
            </main>

        </div>
    </div>

</x-app-layout>
