<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Bandeja global de expedientes</h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-lg rounded-2xl p-6">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32]">Bandeja de revisión</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Consulta expedientes y emite tu decisión (Admin/Validador).
                        </p>
                    </div>

                    {{-- Buscador --}}
                    <form method="GET" action="{{ route('revision.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="{{ $tab ?? 'validacion' }}">
                        <input type="text" name="q" value="{{ $q ?? '' }}"
                               placeholder="Buscar: folio, proyecto, capturista, correo, institución..."
                               class="w-72 max-w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                        <button class="inline-flex items-center px-4 py-2 rounded-md bg-[#691C32] text-white text-sm font-semibold hover:bg-[#4e1324]">
                            Buscar
                        </button>

                        @if(!empty($q))
                            <a href="{{ route('revision.index', ['tab' => $tab]) }}"
                               class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Limpiar
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Tabs --}}
                @php
                    $tab = $tab ?? 'validacion';
                    $counts = $counts ?? ['validacion'=>0,'validados'=>0,'rechazados'=>0];

                    $tabBtn = function($key) use ($tab) {
                        return $tab === $key
                            ? 'bg-[#691C32] text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200';
                    };
                @endphp

                <div class="flex flex-wrap gap-2 mb-5">
                    <a href="{{ route('revision.index', ['tab' => 'validacion', 'q' => $q]) }}"
                       class="inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold {{ $tabBtn('validacion') }}">
                        En validación
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-white/20">
                            {{ $counts['validacion'] ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('revision.index', ['tab' => 'validados', 'q' => $q]) }}"
                       class="inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold {{ $tabBtn('validados') }}">
                        Validados
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-white/20">
                            {{ $counts['validados'] ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('revision.index', ['tab' => 'rechazados', 'q' => $q]) }}"
                       class="inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold {{ $tabBtn('rechazados') }}">
                        Rechazados
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-white/20">
                            {{ $counts['rechazados'] ?? 0 }}
                        </span>
                    </a>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto border border-gray-200 rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 border-b text-left">Folio</th>
                                <th class="px-4 py-3 border-b text-left">Proyecto</th>
                                <th class="px-4 py-3 border-b text-left">Capturista</th>
                                <th class="px-4 py-3 border-b text-left hidden md:table-cell">Institución</th>
                                <th class="px-4 py-3 border-b text-left">Estatus</th>
                                <th class="px-4 py-3 border-b text-left">Últ. movimiento</th>
                                <th class="px-4 py-3 border-b text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($expedientes as $exp)
                                @php
                                    $estatus = strtolower($exp->estatus ?? '');

                                    $badge = match($estatus) {
                                        'en validacion', 'en_validacion' => ['bg-yellow-100 text-yellow-800', 'En validación'],
                                        'aprobado' => ['bg-green-100 text-green-800', 'Aprobado'],
                                        'rechazado' => ['bg-red-100 text-red-800', 'Rechazado'],
                                        default => ['bg-gray-100 text-gray-800', ucfirst($estatus)],
                                    };

                                    $u = $exp->usuario;
                                    $capturistaNombre = $u
                                        ? trim(($u->nombres ?? '').' '.($u->apellido_paterno ?? '').' '.($u->apellido_materno ?? ''))
                                        : 'N/A';

                                    $inst = $u?->institucion
                                        ? trim(($u->institucion->siglas ? $u->institucion->siglas.' - ' : '').($u->institucion->nombre ?? ''))
                                        : '—';

                                    // Quien lo aprobó/rechazó (último movimiento relevante)
                                    $lastDecision = $exp->historiales
                                        ? ($exp->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_APROBADO)
                                            ?? $exp->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_RECHAZADO))
                                        : null;

                                    $by = $lastDecision?->usuario;
                                    $byName = $by
                                        ? trim(($by->nombres ?? '').' '.($by->apellido_paterno ?? '').' '.($by->apellido_materno ?? ''))
                                        : null;
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="font-semibold text-gray-900">{{ $exp->folio }}</span>
                                        <div class="text-xs text-gray-500">Año: {{ $exp->anio_ejercicio }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $exp->nombre_proyecto }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-gray-900">{{ $capturistaNombre }}</div>
                                        @if($u?->email)
                                            <div class="text-xs text-gray-500">{{ $u->email }}</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 hidden md:table-cell">
                                        <div class="text-gray-800">{{ $inst }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badge[0] }}">
                                            {{ $badge[1] }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-xs text-gray-600">
                                        @if($estatus === 'rechazado' && $lastDecision)
                                            <div><span class="font-semibold">Rechazó:</span> {{ $byName ?? 'N/A' }}</div>
                                            @if($by?->email)
                                                <div class="text-gray-500">{{ $by->email }}</div>
                                            @endif
                                            <div class="text-gray-500">{{ optional($lastDecision->created_at)->format('d/m/Y H:i') }}</div>
                                        @elseif($estatus === 'aprobado' && $lastDecision)
                                            <div><span class="font-semibold">Aprobó:</span> {{ $byName ?? 'N/A' }}</div>
                                            @if($by?->email)
                                                <div class="text-gray-500">{{ $by->email }}</div>
                                            @endif
                                            <div class="text-gray-500">{{ optional($lastDecision->created_at)->format('d/m/Y H:i') }}</div>
                                        @else
                                            <div class="text-gray-500">{{ optional($exp->updated_at)->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('revision.show', $exp) }}"
                                           class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-xs font-semibold rounded-md hover:bg-[#4e1324] transition">
                                            Revisar →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        No hay expedientes para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $expedientes->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>