{{-- resources/views/capturista/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Panel de capturista') }}
        </h2>
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

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-8">

                {{-- Header --}}
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32] mb-2">
                            Bienvenido(a), {{ Auth::user()->nombres ?? Auth::user()->name }}
                        </h3>

                        <p class="text-gray-700 leading-relaxed">
                            Desde este panel podrás <span class="font-semibold">capturar, consultar y dar seguimiento</span>
                            a los expedientes técnicos.
                        </p>
                    </div>

                    {{-- Resumen rápido --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <div class="text-xs text-gray-500">Asignaciones EPS</div>
                            <div class="text-xl font-bold text-[#691C32]">{{ $asignacionesCount ?? 0 }}</div>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <div class="text-xs text-gray-500">Expedientes</div>
                            <div class="text-xl font-bold text-[#691C32]">{{ $expedientesCount ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Aviso si no hay asignaciones --}}
                @if(! $tieneAsignaciones)
                    <div class="mt-6 px-4 py-3 rounded-lg bg-yellow-50 text-yellow-800 border border-yellow-100 text-sm">
                        ⚠️ Por el momento tu institución no tiene subprogramas FASP asignados.
                        Solicita al validador que realice la asignación para poder capturar expedientes.
                    </div>
                @endif

                {{-- ACCIONES RÁPIDAS --}}
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Mis asignaciones FASP --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Mis asignaciones FASP</h4>
                                <p class="text-gray-600 text-sm mb-3">
                                    EPS (Eje/Programa/Subprograma) asignados a tu institución.
                                </p>
                            </div>

                            <span class="inline-flex items-center px-6 py-6 rounded-full text-xs font-semibold {{ $badge($asignacionesCount ?? 0) }}">
                                {{ $asignacionesCount ?? 0 }}
                            </span>
                        </div>

                        <div class="text-xs text-gray-600 space-y-1">
                            <div>Disponibles: <span class="font-semibold">{{ $asignacionesDisponibles->count() }}</span></div>
                            <div>En trabajo: <span class="font-semibold">{{ $asignacionesTrabajadas->count() }}</span></div>
                            <div>Compartidas: <span class="font-semibold">{{ $asignacionesCompartidas->count() }}</span></div>
                        </div>
                    </div>

                    {{-- Crear expediente --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow transition {{ $tieneAsignaciones ? 'hover:shadow-md hover:bg-[#9F2241]/20' : 'opacity-60' }}">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Crear nuevo expediente</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Inicia la captura de un nuevo expediente técnico con base en lo asignado a tu institución.
                        </p>

                        @if($tieneAsignaciones)
                            <a href="{{ route('expedientes.create') }}"
                               class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                                Ir a captura de expediente <span class="ml-1">→</span>
                            </a>
                        @else
                            <span class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-600 text-sm font-medium rounded-md cursor-not-allowed">
                                Requiere asignación
                            </span>
                        @endif
                    </div>

                    {{-- Mis expedientes --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Mis expedientes</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Consulta tus expedientes y continúa su edición.
                        </p>
                        <a href="{{ route('expedientes.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                            Ver lista de expedientes <span class="ml-1">→</span>
                        </a>
                    </div>
                </div>

                {{-- LISTADOS (punto 6) --}}
                <div class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Disponibles --}}
                    <div class="border border-gray-200 rounded-2xl p-6 bg-white shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800">Disponibles</h4>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $asignacionesDisponibles->count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">
                            EPS asignados a tu institución que aún no tienen expediente iniciado.
                        </p>

                        @if($asignacionesDisponibles->isEmpty())
                            <div class="text-sm text-gray-500">— Sin pendientes —</div>
                        @else
                            <ul class="space-y-3">
                                @foreach($asignacionesDisponibles as $a)
                                    <li class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="text-sm font-semibold text-gray-800">
                                            Eje {{ $a['eje'] ?? $a->eje }} · Prog {{ $a['programa'] ?? $a->programa }} · Sub {{ $a['subprograma'] ?? $a->subprograma }}
                                        </div>
                                        @if(!empty($a['subprograma_nombre'] ?? null))
                                            <div class="text-xs text-gray-600 mt-1">{{ $a['subprograma_nombre'] }}</div>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('expedientes.create') }}"
                                               class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md bg-[#691C32] text-white hover:bg-[#4e1324] transition">
                                                Iniciar expediente →
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- En trabajo --}}
                    <div class="border border-gray-200 rounded-2xl p-6 bg-white shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800">En trabajo</h4>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">
                                {{ $asignacionesTrabajadas->count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">
                            EPS donde ya guardaste la 1ra parte (continúa edición desde “Mis expedientes”).
                        </p>

                        @if($asignacionesTrabajadas->isEmpty())
                            <div class="text-sm text-gray-500">— Aún no has iniciado expedientes —</div>
                        @else
                            <ul class="space-y-3">
                                @foreach($asignacionesTrabajadas as $a)
                                    <li class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="text-sm font-semibold text-gray-800">
                                            Eje {{ $a['eje'] ?? $a->eje }} · Prog {{ $a['programa'] ?? $a->programa }} · Sub {{ $a['subprograma'] ?? $a->subprograma }}
                                        </div>
                                        @if(!empty($a['subprograma_nombre'] ?? null))
                                            <div class="text-xs text-gray-600 mt-1">{{ $a['subprograma_nombre'] }}</div>
                                        @endif

                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <a href="{{ route('expedientes.index') }}"
                                               class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                                Ver expedientes →
                                            </a>

                                            @if(!empty($a['expediente_id'] ?? null))
                                                <a href="{{ route('expedientes.edit', $a['expediente_id']) }}"
                                                   class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md bg-[#691C32] text-white hover:bg-[#4e1324] transition">
                                                    Continuar →
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Compartidas --}}
                    <div class="border border-gray-200 rounded-2xl p-6 bg-white shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800">Compartidas</h4>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                                {{ $asignacionesCompartidas->count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">
                            EPS asignados a tu institución y a otras (trabajo colaborativo).
                        </p>

                        @if($asignacionesCompartidas->isEmpty())
                            <div class="text-sm text-gray-500">— No tienes asignaciones compartidas —</div>
                        @else
                            <ul class="space-y-3">
                                @foreach($asignacionesCompartidas as $a)
                                    <li class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="text-sm font-semibold text-gray-800">
                                            Eje {{ $a['eje'] ?? $a->eje }} · Prog {{ $a['programa'] ?? $a->programa }} · Sub {{ $a['subprograma'] ?? $a->subprograma }}
                                        </div>

                                        @if(!empty($a['subprograma_nombre'] ?? null))
                                            <div class="text-xs text-gray-600 mt-1">{{ $a['subprograma_nombre'] }}</div>
                                        @endif

                                        @php
                                            $otras = $a['otras_instituciones'] ?? [];
                                        @endphp

                                        @if(count($otras))
                                            <div class="mt-2 text-xs text-gray-600">
                                                Con:
                                                <div class="mt-1 flex flex-wrap gap-2">
                                                    @foreach($otras as $inst)
                                                        <span class="px-2 py-1 rounded-full bg-white border text-gray-700">
                                                            {{ $inst }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            <a href="{{ route('expedientes.create') }}"
                                               class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md bg-[#691C32] text-white hover:bg-[#4e1324] transition">
                                                Crear/Continuar →
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- Guía rápida --}}
                <div class="mt-10 bg-gray-50 border border-gray-200 rounded-2xl p-6">
                    <h4 class="font-semibold text-gray-700 mb-2 text-lg">Guía rápida</h4>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Guarda la <span class="font-semibold">1ra parte</span> (datos generales) y luego continúa la captura técnica.</li>
                        <li>Los expedientes inician en estatus <span class="font-semibold">Borrador</span>.</li>
                        <li>Si un expediente es <span class="font-semibold">Rechazado</span>, corrige y reenvía a validación.</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>