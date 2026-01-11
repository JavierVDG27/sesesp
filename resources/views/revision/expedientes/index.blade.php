<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Bandeja global de expedientes
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alerts --}}
            @if (session('success'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 flex items-start gap-2">
                    <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-green-100 text-green-700 text-xs">✓</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 flex items-start gap-2">
                    <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-100 text-red-700 text-xs">!</span>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @php
                $tab = $tab ?? 'validacion';
                $counts = $counts ?? [
                    'validacion' => 0,
                    'pendiente_firma' => 0,
                    'firmados' => 0,
                    'rechazados' => 0,
                ];

                $tabBtn = function($key) use ($tab) {
                    return $tab === $key
                        ? 'bg-[#691C32] text-white shadow-sm'
                        : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50';
                };

                $tabCount = function($key) use ($tab) {
                    return $tab === $key
                        ? 'bg-white/20 text-white'
                        : 'bg-gray-100 text-gray-700';
                };
            @endphp

            <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">

                {{-- Header Card --}}
                <div class="px-6 py-5 bg-gradient-to-r from-[#691C32] to-[#7a1f36]">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-white">Bandeja de revisión</h3>
                            <p class="text-sm text-white/80 mt-1">
                                Consulta expedientes y emite tu decisión (Admin/Validador).
                            </p>
                        </div>

                        {{-- Buscador --}}
                        <form method="GET" action="{{ route('revision.index') }}" class="w-full lg:w-auto">
                            <input type="hidden" name="tab" value="{{ $tab ?? 'validacion' }}">

                            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                                <div class="relative w-full sm:w-[420px]">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/70">
                                        🔎
                                    </span>
                                    <input type="text" name="q" value="{{ $q ?? '' }}"
                                           placeholder="Buscar: folio, proyecto, capturista, correo, institución..."
                                           class="w-full rounded-xl border border-white/20 bg-white/10 pl-10 pr-3 py-2.5 text-sm text-white placeholder:text-white/60
                                                  focus:border-white/40 focus:ring-white/30">
                                </div>

                                <button class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white text-[#691C32] text-sm font-bold hover:bg-white/90 transition">
                                    Buscar
                                </button>

                                @if(!empty($q))
                                    <a href="{{ route('revision.index', ['tab' => $tab]) }}"
                                       class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-white/30 bg-white/10 text-sm font-semibold text-white hover:bg-white/15 transition">
                                        Limpiar
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tabs + meta --}}
                <div class="px-6 py-4 border-b bg-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('revision.index', ['tab' => 'validacion', 'q' => $q]) }}"
                               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ $tabBtn('validacion') }}">
                                En validación
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $tabCount('validacion') }}">
                                    {{ $counts['validacion'] ?? 0 }}
                                </span>
                            </a>

                            <a href="{{ route('revision.index', ['tab' => 'pendiente_firma', 'q' => $q]) }}"
                               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ $tabBtn('pendiente_firma') }}">
                                Pendiente a firmar
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $tabCount('pendiente_firma') }}">
                                    {{ $counts['pendiente_firma'] ?? 0 }}
                                </span>
                            </a>

                            <a href="{{ route('revision.index', ['tab' => 'firmados', 'q' => $q]) }}"
                               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ $tabBtn('firmados') }}">
                                Firmados
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $tabCount('firmados') }}">
                                    {{ $counts['firmados'] ?? 0 }}
                                </span>
                            </a>

                            <a href="{{ route('revision.index', ['tab' => 'rechazados', 'q' => $q]) }}"
                               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ $tabBtn('rechazados') }}">
                                Rechazados
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $tabCount('rechazados') }}">
                                    {{ $counts['rechazados'] ?? 0 }}
                                </span>
                            </a>
                        </div>

                        <div class="text-xs text-gray-500">
                            @if(!empty($q))
                                Mostrando resultados para: <span class="font-semibold text-gray-700">“{{ $q }}”</span>
                            @else
                                Filtra por pestaña o usa el buscador para localizar un expediente.
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="p-6">
                    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-700 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Folio</th>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Proyecto</th>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Capturista</th>
                                    <th class="px-4 py-3 border-b text-left font-semibold hidden lg:table-cell">Institución</th>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Estatus</th>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Últ. movimiento</th>
                                    <th class="px-4 py-3 border-b text-center font-semibold">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($expedientes as $exp)
                                    @php
                                        $estatus = strtolower((string)($exp->estatus ?? ''));

                                        $badge = match($estatus) {
                                            'en validacion', 'en_validacion' => ['bg-yellow-100 text-yellow-800 border-yellow-200', 'En validación'],
                                            'pendiente_firma' => ['bg-indigo-100 text-indigo-800 border-indigo-200', 'Pendiente firma'],
                                            'firmado' => ['bg-emerald-100 text-emerald-800 border-emerald-200', 'Firmado'],
                                            'rechazado' => ['bg-red-100 text-red-800 border-red-200', 'Rechazado'],
                                            default => ['bg-gray-100 text-gray-800 border-gray-200', ucfirst($estatus)],
                                        };

                                        $u = $exp->usuario;
                                        $capturistaNombre = $u
                                            ? trim(($u->nombres ?? '').' '.($u->apellido_paterno ?? '').' '.($u->apellido_materno ?? ''))
                                            : 'N/A';

                                        $inst = $u?->institucion
                                            ? trim(($u->institucion->siglas ? $u->institucion->siglas.' - ' : '').($u->institucion->nombre ?? ''))
                                            : '—';

                                        $lastDecision = $exp->historiales
                                            ? (
                                                $exp->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_FIRMADO)
                                                ?? $exp->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_PENDIENTE_FIRMA)
                                                ?? $exp->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_RECHAZADO)
                                            )
                                            : null;

                                        $by = $lastDecision?->usuario;
                                        $byName = $by
                                            ? trim(($by->nombres ?? '').' '.($by->apellido_paterno ?? '').' '.($by->apellido_materno ?? ''))
                                            : null;
                                    @endphp

                                    <tr class="hover:bg-gray-50">
                                        {{-- Folio --}}
                                        <td class="px-4 py-3 align-top">
                                            <div class="flex flex-col gap-0.5">
                                                <span class="font-bold text-gray-900">{{ $exp->folio }}</span>
                                                <span class="text-xs text-gray-500">Año: {{ $exp->anio_ejercicio }}</span>
                                            </div>
                                        </td>

                                        {{-- Proyecto --}}
                                        <td class="px-4 py-3 align-top">
                                            <div class="font-semibold text-gray-900 leading-snug">
                                                {{ $exp->nombre_proyecto }}
                                            </div>
                                        </td>

                                        {{-- Capturista --}}
                                        <td class="px-4 py-3 align-top">
                                            <div class="text-gray-900 font-medium">
                                                {{ $capturistaNombre }}
                                            </div>
                                            @if($u?->email)
                                                <div class="text-xs text-gray-500">{{ $u->email }}</div>
                                            @endif
                                        </td>

                                        {{-- Institución --}}
                                        <td class="px-4 py-3 align-top hidden lg:table-cell">
                                            <div class="text-gray-800">
                                                {{ $inst }}
                                            </div>
                                        </td>

                                        {{-- Estatus --}}
                                        <td class="px-4 py-3 align-top">
                                            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge[0] }}">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current opacity-60"></span>
                                                {{ $badge[1] }}
                                            </span>
                                        </td>

                                        {{-- Últ. movimiento --}}
                                        <td class="px-4 py-3 align-top text-xs text-gray-600">
                                            @if($lastDecision)
                                                <div class="text-gray-700">
                                                    <span class="font-semibold">
                                                        @if($lastDecision->estado_nuevo === \App\Models\Expediente::ESTADO_RECHAZADO)
                                                            Rechazó:
                                                        @elseif($lastDecision->estado_nuevo === \App\Models\Expediente::ESTADO_PENDIENTE_FIRMA)
                                                            Validó:
                                                        @elseif($lastDecision->estado_nuevo === \App\Models\Expediente::ESTADO_FIRMADO)
                                                            Firmó/Cargó:
                                                        @else
                                                            Movió:
                                                        @endif
                                                    </span>
                                                    {{ $byName ?? 'N/A' }}
                                                </div>

                                                @if($by?->email)
                                                    <div class="text-gray-500">{{ $by->email }}</div>
                                                @endif

                                                <div class="text-gray-500">
                                                    {{ optional($lastDecision->created_at)->format('d/m/Y H:i') }}
                                                </div>
                                            @else
                                                <div class="text-gray-500">
                                                    {{ optional($exp->updated_at)->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="px-4 py-3 align-top text-center">
                                            <a href="{{ route('revision.show', $exp) }}"
                                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#691C32] text-white text-xs font-bold shadow-sm hover:bg-[#4e1324] transition">
                                                <span>Revisar</span>
                                                <span aria-hidden="true">→</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-12 text-center">
                                            <div class="mx-auto max-w-md">
                                                <div class="mx-auto mb-3 h-12 w-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-500">
                                                    📄
                                                </div>
                                                <div class="font-semibold text-gray-800">No hay expedientes para mostrar</div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    Prueba cambiando de pestaña o usa el buscador para encontrar un folio.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $expedientes->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
