{{-- resources/views/capturista/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Panel de capturista') }}
            </h2>

            <div class="hidden sm:flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-white ring-1 ring-white/15">
                    <span class="h-2 w-2 rounded-full bg-white/70"></span>
                    CAPTURISTA
                </span>
            </div>
        </div>
    </x-slot>

    @php
        $asignacionesDisponibles = $asignacionesDisponibles ?? collect();
        $asignacionesTrabajadas = $asignacionesTrabajadas ?? collect();
        $asignacionesCompartidas = $asignacionesCompartidas ?? collect();

        $asignacionesCount = $asignacionesCount ?? ($asignacionesDisponibles->count() + $asignacionesTrabajadas->count());
        $expedientesCount = $expedientesCount ?? null;

        $tieneAsignaciones = ($asignacionesCount ?? 0) > 0;

        // helpers visuales
        $badge = fn($n) => $n > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700';
    @endphp

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Shell --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl">

                {{-- Top header --}}
                <div class="p-6 sm:p-8 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                        <div class="flex items-start gap-4">
                            <div class="shrink-0">
                                <div class="h-12 w-12 rounded-2xl bg-[#691C32]/10 ring-1 ring-[#691C32]/15 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-[#691C32]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                                    <span class="text-[#691C32]">Bienvenido(a)</span>,
                                    {{ Auth::user()->nombres ?? Auth::user()->name }}
                                </h3>

                                <p class="mt-1 text-sm sm:text-base text-gray-600 leading-relaxed max-w-2xl">
                                    Desde este panel podrás <span class="font-semibold text-gray-800">capturar, consultar y dar seguimiento</span>
                                    a los expedientes técnicos asignados a tu institución.
                                </p>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#9F2241]/10 text-[#9F2241] ring-1 ring-[#9F2241]/15">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#9F2241]"></span>
                                        Captura · Expedientes
                                    </span>

                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 ring-1 ring-gray-200">
                                        <svg class="h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        Panel de trabajo
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Resumen rápido --}}
                        <div class="grid grid-cols-2 gap-3 w-full lg:w-auto">
                            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-500">Asignaciones EPS</div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold {{ $badge($asignacionesCount ?? 0) }}">
                                        {{ $asignacionesCount ?? 0 }}
                                    </span>
                                </div>
                                <div class="mt-2 text-2xl font-extrabold text-[#691C32]">
                                    {{ $asignacionesCount ?? 0 }}
                                </div>
                                <div class="mt-1 text-[11px] text-gray-500">
                                    Eje · Programa · Subprograma
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-500">Expedientes</div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $expedientesCount ?? '—' }}
                                    </span>
                                </div>
                                <div class="mt-2 text-2xl font-extrabold text-[#691C32]">
                                    {{ $expedientesCount ?? '—' }}
                                </div>
                                <div class="mt-1 text-[11px] text-gray-500">
                                    Borrador · En revisión · etc.
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Aviso si no hay asignaciones --}}
                    @if(! $tieneAsignaciones)
                        <div class="mt-6 rounded-2xl border border-yellow-100 bg-yellow-50 p-4 sm:p-5">
                            <div class="flex gap-3">
                                <div class="mt-0.5">
                                    <div class="h-9 w-9 rounded-xl bg-yellow-100 text-yellow-800 flex items-center justify-center">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-yellow-900">
                                        Aún no tienes subprogramas FASP asignados
                                    </div>
                                    <div class="mt-1 text-sm text-yellow-800 leading-relaxed">
                                        Por el momento tu institución no tiene EPS asignados. Solicita al validador que realice la asignación
                                        para poder capturar expedientes.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ACCIONES RÁPIDAS --}}
                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- Mis asignaciones FASP --}}
                        <div class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <div class="h-10 w-10 rounded-2xl bg-[#9F2241]/10 ring-1 ring-[#9F2241]/15 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-[#9F2241]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 6h16M4 10h16M4 14h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M4 18h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-gray-900 text-lg">
                                                Mis asignaciones FASP
                                            </h4>
                                        </div>

                                        <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                                            EPS (Eje/Programa/Subprograma) asignados a tu institución.
                                        </p>
                                    </div>

                                    <span class="inline-flex items-center justify-center h-12 w-12 rounded-2xl text-sm font-extrabold {{ $badge($asignacionesCount ?? 0) }} ring-1 ring-black/5">
                                        {{ $asignacionesCount ?? 0 }}
                                    </span>
                                </div>

                                <div class="mt-5 grid grid-cols-3 gap-2">
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                                        <div class="text-[11px] text-gray-500">Disponibles</div>
                                        <div class="text-sm font-bold text-gray-900">{{ $asignacionesDisponibles->count() }}</div>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                                        <div class="text-[11px] text-gray-500">En trabajo</div>
                                        <div class="text-sm font-bold text-gray-900">{{ $asignacionesTrabajadas->count() }}</div>
                                    </div>
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                                        <div class="text-[11px] text-gray-500">Compartidas</div>
                                        <div class="text-sm font-bold text-gray-900">{{ $asignacionesCompartidas->count() }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 pb-6">
                                <div class="flex items-center justify-between rounded-xl bg-[#691C32]/5 border border-[#691C32]/10 p-3">
                                    <div class="text-xs text-gray-700">
                                        Revisa los EPS y comienza la captura.
                                    </div>
                                    <span class="text-xs font-semibold text-[#691C32]">
                                        Panel →
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Crear expediente --}}
                        <div class="group rounded-2xl border border-gray-200 bg-white shadow-sm transition {{ $tieneAsignaciones ? 'hover:shadow-md' : 'opacity-70' }}">
                            <div class="p-6">
                                <div class="flex items-center gap-2">
                                    <div class="h-10 w-10 rounded-2xl bg-[#691C32]/10 ring-1 ring-[#691C32]/15 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-[#691C32]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-lg">
                                        Crear nuevo expediente
                                    </h4>
                                </div>

                                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                                    Inicia la captura de un nuevo expediente técnico con base en lo asignado a tu institución.
                                </p>

                                <div class="mt-5">
                                    @if($tieneAsignaciones)
                                        <a href="{{ route('expedientes.create') }}"
                                           class="inline-flex w-full items-center justify-center gap-2 px-4 py-2.5 bg-[#691C32] text-white text-sm font-semibold rounded-xl hover:bg-[#4e1324] transition shadow-sm">
                                            Ir a captura de expediente
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="inline-flex w-full items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-semibold rounded-xl ring-1 ring-gray-200 cursor-not-allowed">
                                            Requiere asignación
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-4 text-[11px] text-gray-500">
                                    Tip: primero guarda la 1ra parte para habilitar el resto.
                                </div>
                            </div>
                        </div>

                        {{-- Mis expedientes --}}
                        <div class="group rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition">
                            <div class="p-6">
                                <div class="flex items-center gap-2">
                                    <div class="h-10 w-10 rounded-2xl bg-indigo-50 ring-1 ring-indigo-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-indigo-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14 2v6h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-lg">
                                        Mis expedientes
                                    </h4>
                                </div>

                                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                                    Consulta tus expedientes y continúa su edición.
                                </p>

                                <div class="mt-5">
                                    <a href="{{ route('expedientes.index') }}"
                                       class="inline-flex w-full items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-black transition shadow-sm">
                                        Ver lista de expedientes
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </div>

                                <div class="mt-4 text-[11px] text-gray-500">
                                    Continúa donde lo dejaste.
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- LISTADOS --}}
                    <div class="mt-10">
                        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
                            <div>
                                <h4 class="text-lg font-extrabold text-gray-900">Asignaciones por estado</h4>
                                <p class="text-sm text-gray-600">Organiza tus EPS y avanza más rápido en la captura.</p>
                            </div>

                            <div class="flex items-center gap-2 text-xs">
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-50 text-gray-700 ring-1 ring-gray-200">
                                    Total:
                                    <span class="font-bold text-gray-900">{{ $asignacionesCount ?? 0 }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                            {{-- Disponibles --}}
                            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="h-9 w-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 6v12M6 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-gray-900">Disponibles</div>
                                                <div class="text-xs text-gray-600">Aún sin expediente iniciado</div>
                                            </div>
                                        </div>

                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $asignacionesDisponibles->count() }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    @if($asignacionesDisponibles->isEmpty())
                                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center">
                                            <div class="text-sm font-semibold text-gray-700">— Sin pendientes —</div>
                                            <div class="mt-1 text-xs text-gray-500">Cuando tengas EPS disponibles aparecerán aquí.</div>
                                        </div>
                                    @else
                                        <ul class="space-y-3">
                                            @foreach($asignacionesDisponibles as $a)
                                                <li class="rounded-2xl border border-gray-200 bg-white p-4 hover:shadow-sm transition">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0">
                                                            <div class="text-sm font-bold text-gray-900 truncate">
                                                                Eje {{ $a['eje'] ?? $a->eje }}
                                                                <span class="text-gray-400">·</span>
                                                                Prog {{ $a['programa'] ?? $a->programa }}
                                                                <span class="text-gray-400">·</span>
                                                                Sub {{ $a['subprograma'] ?? $a->subprograma }}
                                                            </div>

                                                            @if(!empty($a['subprograma_nombre'] ?? null))
                                                                <div class="text-xs text-gray-600 mt-1 line-clamp-2">
                                                                    {{ $a['subprograma_nombre'] }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <span class="shrink-0 inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                                            Nuevo
                                                        </span>
                                                    </div>

                                                    <div class="mt-4">
                                                        <a href="{{ route('expedientes.create') }}"
                                                           class="inline-flex items-center justify-center w-full gap-2 px-3 py-2 text-xs font-semibold rounded-xl bg-[#691C32] text-white hover:bg-[#4e1324] transition shadow-sm">
                                                            Iniciar expediente
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>

                            {{-- En trabajo --}}
                            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="h-9 w-9 rounded-xl bg-green-100 text-green-800 flex items-center justify-center">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 19V5a2 2 0 0 1 2-2h9l5 5v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14 3v5h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-gray-900">En trabajo</div>
                                                <div class="text-xs text-gray-600">Ya iniciaste la 1ra parte</div>
                                            </div>
                                        </div>

                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">
                                            {{ $asignacionesTrabajadas->count() }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    @if($asignacionesTrabajadas->isEmpty())
                                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center">
                                            <div class="text-sm font-semibold text-gray-700">— Aún no has iniciado expedientes —</div>
                                            <div class="mt-1 text-xs text-gray-500">Crea un expediente desde “Crear nuevo expediente”.</div>
                                        </div>
                                    @else
                                        <ul class="space-y-3">
                                            @foreach($asignacionesTrabajadas as $a)
                                                <li class="rounded-2xl border border-gray-200 bg-white p-4 hover:shadow-sm transition">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0">
                                                            <div class="text-sm font-bold text-gray-900 truncate">
                                                                Eje {{ $a['eje'] ?? $a->eje }}
                                                                <span class="text-gray-400">·</span>
                                                                Prog {{ $a['programa'] ?? $a->programa }}
                                                                <span class="text-gray-400">·</span>
                                                                Sub {{ $a['subprograma'] ?? $a->subprograma }}
                                                            </div>

                                                            @if(!empty($a['subprograma_nombre'] ?? null))
                                                                <div class="text-xs text-gray-600 mt-1 line-clamp-2">
                                                                    {{ $a['subprograma_nombre'] }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <span class="shrink-0 inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-green-50 text-green-700 border border-green-100">
                                                            En progreso
                                                        </span>
                                                    </div>

                                                    <div class="mt-4 flex flex-wrap gap-2">
                                                        <a href="{{ route('expedientes.index') }}"
                                                           class="inline-flex flex-1 items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition ring-1 ring-gray-200">
                                                            Ver expedientes
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </a>

                                                        @if(!empty($a['expediente_id'] ?? null))
                                                            <a href="{{ route('expedientes.edit', $a['expediente_id']) }}"
                                                               class="inline-flex flex-1 items-center justify-center gap-2 px-3 py-2 text-xs font-semibold rounded-xl bg-[#691C32] text-white hover:bg-[#4e1324] transition shadow-sm">
                                                                Continuar
                                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>

                            {{-- Compartidas --}}
                            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                                <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="h-9 w-9 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-gray-900">Compartidas</div>
                                                <div class="text-xs text-gray-600">Trabajo colaborativo</div>
                                            </div>
                                        </div>

                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                                            {{ $asignacionesCompartidas->count() }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    @if($asignacionesCompartidas->isEmpty())
                                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center">
                                            <div class="text-sm font-semibold text-gray-700">— No tienes asignaciones compartidas —</div>
                                            <div class="mt-1 text-xs text-gray-500">Si un EPS es compartido aparecerá aquí.</div>
                                        </div>
                                    @else
                                        <ul class="space-y-3">
                                            @foreach($asignacionesCompartidas as $a)
                                                <li class="rounded-2xl border border-gray-200 bg-white p-4 hover:shadow-sm transition">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0">
                                                            <div class="text-sm font-bold text-gray-900 truncate">
                                                                Eje {{ $a['eje'] ?? $a->eje }}
                                                                <span class="text-gray-400">·</span>
                                                                Prog {{ $a['programa'] ?? $a->programa }}
                                                                <span class="text-gray-400">·</span>
                                                                Sub {{ $a['subprograma'] ?? $a->subprograma }}
                                                            </div>

                                                            @if(!empty($a['subprograma_nombre'] ?? null))
                                                                <div class="text-xs text-gray-600 mt-1 line-clamp-2">
                                                                    {{ $a['subprograma_nombre'] }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <span class="shrink-0 inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                                                            Compartido
                                                        </span>
                                                    </div>

                                                    @php
                                                        $otras = $a['otras_instituciones'] ?? [];
                                                    @endphp

                                                    @if(count($otras))
                                                        <div class="mt-3 rounded-xl border border-gray-200 bg-gray-50 p-3">
                                                            <div class="text-xs font-semibold text-gray-700">Con:</div>
                                                            <div class="mt-2 flex flex-wrap gap-2">
                                                                @foreach($otras as $inst)
                                                                    <span class="px-2 py-1 rounded-full bg-white border border-gray-200 text-[11px] font-medium text-gray-700">
                                                                        {{ $inst }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="mt-4">
                                                        <a href="{{ route('expedientes.create') }}"
                                                           class="inline-flex items-center justify-center w-full gap-2 px-3 py-2 text-xs font-semibold rounded-xl bg-[#691C32] text-white hover:bg-[#4e1324] transition shadow-sm">
                                                            Crear/Continuar
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Guía rápida --}}
                    <div class="mt-10 rounded-2xl border border-gray-200 bg-gray-50 p-6 sm:p-7">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0">
                                <div class="h-11 w-11 rounded-2xl bg-white ring-1 ring-gray-200 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1">
                                <h4 class="font-extrabold text-gray-900 text-lg">Guía rápida</h4>
                                <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-1.5">
                                    <li>Guarda la <span class="font-semibold text-gray-900">1ra parte</span> (datos generales) y luego continúa la captura técnica.</li>
                                    <li>Los expedientes inician en estatus <span class="font-semibold text-gray-900">Borrador</span>.</li>
                                    <li>Si un expediente es <span class="font-semibold text-gray-900">Rechazado</span>, corrige y reenvía a validación.</li>
                                </ul>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('expedientes.index') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-gray-800 text-sm font-semibold hover:bg-gray-100 transition ring-1 ring-gray-200">
                                        Ir a mis expedientes
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>

                                    @if($tieneAsignaciones)
                                        <a href="{{ route('expedientes.create') }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#691C32] text-white text-sm font-semibold hover:bg-[#4e1324] transition shadow-sm">
                                            Crear expediente
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
