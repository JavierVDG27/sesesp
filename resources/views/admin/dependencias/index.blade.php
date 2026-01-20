{{-- resources/views/admin/dependencias/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-white leading-tight">
                    Dependencias
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    Visualiza usuarios agrupados por institución y subdependencia.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.instituciones.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#9F2241] text-white font-semibold shadow-sm hover:bg-[#691C32] transition">
                    <i class="fas fa-building"></i>
                    Gestionar Instituciones
                </a>

                <a href="{{ route('admin.subdependencias.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-[#9F2241] font-semibold border border-[#9F2241]/40 hover:bg-[#9F2241]/10 transition">
                    <i class="fas fa-sitemap"></i>
                    Gestionar Subdependencias
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-green-700">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="text-green-800 text-sm">
                            <p class="font-semibold">Operación realizada</p>
                            <p class="mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-[#9F2241]">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-800">Listado por Dependencias</h1>
                            <p class="text-sm text-gray-500 mt-1">
                                Visualiza usuarios agrupados por institución y subdependencia.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="space-y-8">
                        @forelse($instituciones as $inst)

                            <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-sm bg-white"
                                 x-data="{ openAll: false }"
                            >
                                {{-- Header institución --}}
                                <div class="px-6 py-5 bg-gradient-to-r from-[#691C32] to-[#691C32]/90 text-white">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-white/80 text-xs uppercase tracking-wider">Dependencia</p>
                                            <h2 class="text-xl font-bold break-words">{{ $inst->nombre }}</h2>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center gap-2 text-xs font-semibold bg-white/10 border border-white/15 px-3 py-1 rounded-full">
                                                <i class="fas fa-sitemap"></i>
                                                {{ $inst->subdependencias->count() }} subdependencias
                                            </span>

                                            <span class="inline-flex items-center gap-2 text-xs font-semibold bg-white/10 border border-white/15 px-3 py-1 rounded-full">
                                                <i class="fas fa-users"></i>
                                                {{ ($inst->usuariosSinSubdependencia?->count() ?? 0) + $inst->subdependencias->sum(fn($sd) => $sd->users->count()) }} usuarios
                                            </span>

                                            {{-- Acciones acordeón (solo UI) --}}
                                            <button type="button"
                                                    class="inline-flex items-center gap-2 text-xs font-semibold bg-white/10 border border-white/15 px-3 py-1 rounded-full hover:bg-white/15 transition"
                                                    @click="openAll = !openAll">
                                                <i class="fas" :class="openAll ? 'fa-minus' : 'fa-plus'"></i>
                                                <span x-text="openAll ? 'Contraer todo' : 'Expandir todo'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6 space-y-6">

                                    {{-- SUBDEPENDENCIAS EN ACORDEÓN --}}
                                    @forelse($inst->subdependencias as $sub)
                                        <div class="rounded-2xl border border-gray-200 bg-gray-50/60 overflow-hidden"
                                             x-data="{ open: false }"
                                             x-init="$watch('openAll', v => open = v)"
                                        >
                                            {{-- Botón header --}}
                                            <button type="button"
                                                    class="w-full px-5 py-4 bg-white border-b border-gray-200 text-left hover:bg-gray-50 transition"
                                                    @click="open = !open"
                                            >
                                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                                    <div class="flex items-start gap-3 min-w-0">
                                                        <div class="h-10 w-10 rounded-xl bg-[#9F2241]/10 border border-[#9F2241]/15 text-[#9F2241] flex items-center justify-center">
                                                            <i class="fas fa-diagram-project"></i>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <h3 class="text-lg font-semibold text-gray-800 break-words">
                                                                {{ $sub->nombre }}
                                                            </h3>
                                                            <p class="text-xs text-gray-500 mt-1">Subdependencia</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="inline-flex items-center gap-2 text-xs font-semibold bg-gray-50 border border-gray-200 px-3 py-1 rounded-full">
                                                            <i class="fas fa-users text-gray-500"></i>
                                                            {{ $sub->users->count() }} usuarios
                                                        </span>

                                                        <span class="inline-flex items-center gap-2 text-xs font-semibold bg-white border border-gray-200 px-3 py-1 rounded-full">
                                                            <i class="fas fa-chevron-down text-[11px] transition-transform"
                                                               :class="open ? 'rotate-180' : ''"></i>
                                                            <span x-text="open ? 'Ocultar' : 'Ver detalles'"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </button>

                                            {{-- Contenido acordeón --}}
                                            <div x-show="open" x-cloak class="p-5 space-y-4">

                                                {{-- Form asignar --}}
                                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                                    <p class="text-sm text-gray-600">
                                                        Asigna usuarios sin subdependencia a <span class="font-semibold">{{ $sub->nombre }}</span>.
                                                    </p>

                                                    <form method="POST" action="{{ route('admin.dependencias.asignar') }}"
                                                          class="flex flex-col sm:flex-row sm:items-center gap-2">
                                                        @csrf
                                                        <input type="hidden" name="subdependencia_id" value="{{ $sub->id }}">

                                                        <select name="user_id"
                                                                class="w-full sm:w-64 rounded-xl border-gray-300 text-sm shadow-sm
                                                                       focus:ring-[#9F2241] focus:border-[#9F2241]
                                                                       disabled:bg-gray-100 disabled:text-gray-500"
                                                                @if(($inst->usuariosSinSubdependencia?->count() ?? 0) === 0) disabled @endif>
                                                            <option value="" selected disabled>
                                                                + Asignar usuario…
                                                            </option>

                                                            @foreach(($inst->usuariosSinSubdependencia ?? collect()) as $u)
                                                                <option value="{{ $u->id }}">
                                                                    {{ $u->nombres }} ({{ $u->role?->nombre ?? 'sin asignar' }})
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        <button type="submit"
                                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#9F2241] text-white text-sm font-semibold shadow-sm hover:bg-[#691C32] transition
                                                                       disabled:opacity-50 disabled:cursor-not-allowed"
                                                                @if(($inst->usuariosSinSubdependencia?->count() ?? 0) === 0) disabled @endif>
                                                            <i class="fas fa-user-plus"></i>
                                                            Agregar
                                                        </button>
                                                    </form>
                                                </div>

                                                {{-- Tabla usuarios --}}
                                                <div class="overflow-x-auto bg-white rounded-2xl border border-gray-200">
                                                    <table class="w-full text-left">
                                                        <thead>
                                                            <tr class="bg-[#9F2241]/10 text-gray-800 font-semibold">
                                                                <th class="py-3 px-4 whitespace-nowrap">Usuario</th>
                                                                <th class="py-3 px-4 whitespace-nowrap">Correo</th>
                                                                <th class="py-3 px-4 whitespace-nowrap">Rol</th>
                                                                <th class="py-3 px-4 whitespace-nowrap">Estado</th>
                                                                <th class="py-3 px-4 whitespace-nowrap">Acciones</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody class="divide-y divide-gray-100">
                                                            @forelse($sub->users as $u)
                                                                <tr class="hover:bg-gray-50 transition">
                                                                    <td class="py-3 px-4">
                                                                        <div class="font-semibold text-gray-800 break-words">
                                                                            {{ $u->nombres }} {{ $u->apellido_paterno }} {{ $u->apellido_materno }}
                                                                        </div>
                                                                    </td>

                                                                    <td class="py-3 px-4 text-gray-700 break-words">
                                                                        {{ $u->email }}
                                                                    </td>

                                                                    <td class="py-3 px-4">
                                                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/10">
                                                                            <i class="fas fa-id-badge text-[12px]"></i>
                                                                            {{ ucfirst($u->role?->nombre ?? $u->role?->name ?? 'sin rol') }}
                                                                        </span>
                                                                    </td>

                                                                    <td class="py-3 px-4">
                                                                        @if($u->activo)
                                                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-800 border border-green-200">
                                                                                <i class="fas fa-circle text-[10px]"></i>
                                                                                Activo
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                                                <i class="fas fa-ban"></i>
                                                                                Deshabilitado
                                                                            </span>
                                                                        @endif
                                                                    </td>

                                                                    <td class="py-3 px-4">
                                                                        {{-- Botón + modal local (mismo patrón que users/index) --}}
                                                                        <div x-data="{ openQuitar:false }"
                                                                             @keydown.escape.window="openQuitar=false"
                                                                             class="inline-block">

                                                                            <button type="button"
                                                                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold shadow-sm hover:bg-amber-600 transition"
                                                                                    @click="openQuitar=true">
                                                                                <i class="fas fa-user-minus"></i>
                                                                                Quitar
                                                                            </button>

                                                                            {{-- Modal quitar usuario de la subdependencia --}}
                                                                            <div
                                                                                x-show="openQuitar"
                                                                                x-cloak
                                                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                                                                aria-modal="true"
                                                                                role="dialog"
                                                                            >
                                                                                {{-- Fondo --}}
                                                                                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                                                                                     @click="openQuitar=false"></div>

                                                                                {{-- Panel --}}
                                                                                <div
                                                                                    x-show="openQuitar"
                                                                                    x-transition.opacity
                                                                                    x-transition.scale.95
                                                                                    class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden"
                                                                                >
                                                                                    {{-- Header --}}
                                                                                    <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white">
                                                                                        <div class="flex items-center gap-3">
                                                                                            <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center">
                                                                                                <i class="fas fa-user-minus"></i>
                                                                                            </div>
                                                                                            <div>
                                                                                                <h2 class="text-lg font-semibold">
                                                                                                    Quitar usuario de la subdependencia
                                                                                                </h2>
                                                                                                <p class="text-xs text-white/80 mt-0.5">
                                                                                                    Esta acción no elimina al usuario del sistema, solo lo desasigna de la subdependencia.
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    {{-- Body --}}
                                                                                    <div class="px-6 py-5 space-y-4">
                                                                                        <p class="text-sm text-gray-700">
                                                                                            ¿Estás seguro de que deseas quitar a este usuario de la subdependencia?
                                                                                        </p>

                                                                                        <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-900 space-y-2">
                                                                                            <div class="flex items-start gap-2">
                                                                                                <i class="fas fa-user mt-0.5"></i>
                                                                                                <div>
                                                                                                    <p class="font-semibold">
                                                                                                        {{ $u->nombres }} {{ $u->apellido_paterno }} {{ $u->apellido_materno }}
                                                                                                    </p>
                                                                                                    <p class="text-xs text-amber-900/80">
                                                                                                        Usuario asignado a la subdependencia:
                                                                                                        <span class="font-medium">{{ $sub->nombre }}</span>
                                                                                                    </p>
                                                                                                </div>
                                                                                            </div>
                                                                                            <p class="text-xs text-amber-900/80 flex gap-2">
                                                                                                <i class="fas fa-circle-info mt-0.5"></i>
                                                                                                <span>
                                                                                                    Podrás volver a asignar a este usuario más adelante desde la sección de “Usuarios sin subdependencia”.
                                                                                                </span>
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>

                                                                                    {{-- Footer --}}
                                                                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-end gap-3">
                                                                                        <button type="button"
                                                                                                @click="openQuitar=false"
                                                                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                                                                                            <i class="fas fa-xmark"></i>
                                                                                            Cancelar
                                                                                        </button>

                                                                                        <form method="POST" action="{{ route('admin.dependencias.quitar') }}">
                                                                                            @csrf
                                                                                            <input type="hidden" name="user_id" value="{{ $u->id }}">

                                                                                            <button type="submit"
                                                                                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-amber-600 text-white text-sm font-semibold shadow-sm hover:bg-amber-700 transition">
                                                                                                <i class="fas fa-check"></i>
                                                                                                Sí, quitar usuario
                                                                                            </button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="5" class="py-8 px-4 text-center text-gray-500">
                                                                        No hay usuarios asignados a esta subdependencia.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 text-gray-600">
                                            Esta institución aún no tiene subdependencias registradas.
                                        </div>
                                    @endforelse

                                    {{-- USUARIOS SIN SUBDEPENDENCIA (ACORDEÓN) --}}
                                    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden"
                                         x-data="{ open: true }"
                                         x-init="$watch('openAll', v => open = v)"
                                    >
                                        <button type="button"
                                                class="w-full px-5 py-4 bg-gray-50 border-b border-gray-200 text-left hover:bg-gray-100 transition"
                                                @click="open = !open">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-800">Usuarios sin subdependencia</h3>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Usuarios registrados en la dependencia pero no asignados a una subdependencia.
                                                    </p>
                                                </div>

                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center gap-2 text-xs font-semibold bg-white border border-gray-200 px-3 py-1 rounded-full">
                                                        <i class="fas fa-users text-gray-500"></i>
                                                        {{ ($inst->usuariosSinSubdependencia?->count() ?? 0) }} usuarios
                                                    </span>

                                                    <span class="inline-flex items-center gap-2 text-xs font-semibold bg-white border border-gray-200 px-3 py-1 rounded-full">
                                                        <i class="fas fa-chevron-down text-[11px] transition-transform"
                                                           :class="open ? 'rotate-180' : ''"></i>
                                                        <span x-text="open ? 'Ocultar' : 'Ver detalles'"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </button>

                                        <div x-show="open" x-cloak class="p-5">
                                            <div class="overflow-x-auto bg-white rounded-2xl border border-gray-200">
                                                <table class="w-full text-left">
                                                    <thead>
                                                        <tr class="bg-white text-gray-800 font-semibold">
                                                            <th class="py-3 px-4 whitespace-nowrap">Usuario</th>
                                                            <th class="py-3 px-4 whitespace-nowrap">Correo</th>
                                                            <th class="py-3 px-4 whitespace-nowrap">Rol</th>
                                                            <th class="py-3 px-4 whitespace-nowrap">Estado</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody class="divide-y divide-gray-100">
                                                        @forelse(($inst->usuariosSinSubdependencia ?? collect()) as $u)
                                                            <tr class="hover:bg-gray-50 transition">
                                                                <td class="py-3 px-4 font-semibold text-gray-800 break-words">
                                                                    {{ $u->nombres }} {{ $u->apellido_paterno }} {{ $u->apellido_materno }}
                                                                </td>
                                                                <td class="py-3 px-4 text-gray-700 break-words">
                                                                    {{ $u->email }}
                                                                </td>
                                                                <td class="py-3 px-4">
                                                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/10">
                                                                        <i class="fas fa-id-badge text-[12px]"></i>
                                                                        {{ ucfirst($u->role?->nombre ?? $u->role?->name ?? 'sin rol') }}
                                                                    </span>
                                                                </td>
                                                                <td class="py-3 px-4">
                                                                    @if($u->activo)
                                                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-800 border border-green-200">
                                                                            <i class="fas fa-circle text-[10px]"></i>
                                                                            Activo
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                                            <i class="fas fa-ban"></i>
                                                                            Deshabilitado
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="py-8 px-4 text-center text-gray-500">
                                                                    No hay usuarios pendientes de subdependencia.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        @empty
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-6 text-gray-600">
                                No hay instituciones registradas todavía.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>