{{-- resources/views/validador/fasp_asignaciones_institucion/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Asignaciones FASP por institución
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-2xl p-6">

                {{-- Header + filtros --}}
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32]">Catálogo (solo lectura) + Asignación</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Asigna subprogramas a una o más instituciones. Puedes quitar chips individuales.
                        </p>
                    </div>

                    <form method="GET" class="flex flex-wrap gap-2 items-end">
                        <div>
                            <label class="block text-xs text-gray-500">Año</label>
                            <input name="year" value="{{ $year }}" class="border rounded-md px-3 py-2 text-sm w-28">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Entidad</label>
                            <input name="entidad" value="{{ $entidad }}" class="border rounded-md px-3 py-2 text-sm w-28">
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Eje</label>
                            <select name="eje" class="border rounded-md px-3 py-2 text-sm">
                                <option value="">Todos</option>
                                @foreach($ejes as $x)
                                    <option value="{{ $x }}" @selected((string)$eje === (string)$x)>{{ $x }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500">Programa</label>
                            <select name="programa" class="border rounded-md px-3 py-2 text-sm">
                                <option value="">Todos</option>
                                @foreach($programas as $p)
                                    <option value="{{ $p }}" @selected((string)$programa === (string)$p)>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button class="px-4 py-2 rounded-md bg-[#691C32] text-white text-sm font-semibold hover:bg-[#4e1324]">
                            Filtrar
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border-b text-left">Eje</th>
                            <th class="px-4 py-3 border-b text-left">Programa</th>
                            <th class="px-4 py-3 border-b text-left">Subprograma</th>

                            <th class="px-4 py-3 border-b text-right">Subtotal Federal</th>
                            <th class="px-4 py-3 border-b text-right">Subtotal Estatal</th>
                            <th class="px-4 py-3 border-b text-right">Total</th>

                            <th class="px-4 py-3 border-b text-left">Distribución</th>
                            <th class="px-4 py-3 border-b text-left">Instituciones</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                        @forelse($rows as $r)
                            @php
                                $key = "{$r->eje}|{$r->programa}|{$r->subprograma}";

                                // Asignaciones multi-institución
                                $asigs = $map[$key] ?? collect();
                                $assignedIds = $asigs->pluck('institucion_id')->map(fn($v)=>(int)$v)->all();

                                // Subtotales desde catálogo
                                $fed = (float)($r->fed_federal ?? 0) + (float)($r->fed_municipal ?? 0);
                                $est = (float)($r->est_estatal ?? 0) + (float)($r->est_municipal ?? 0);
                                $tot = $fed + $est;

                                // Límites por fuente (catálogo)
                                $limFF = (float)($r->fed_federal ?? 0);
                                $limFM = (float)($r->fed_municipal ?? 0);
                                $limEE = (float)($r->est_estatal ?? 0);
                                $limEM = (float)($r->est_municipal ?? 0);

                                // Sumas distribuidas (resumen) desde controller
                                $sum = $distribSum[$key] ?? collect();
                                $sFF = (float)($sum['fed_federal'] ?? 0);
                                $sFM = (float)($sum['fed_municipal'] ?? 0);
                                $sEE = (float)($sum['est_estatal'] ?? 0);
                                $sEM = (float)($sum['est_municipal'] ?? 0);

                                $locked = isset($locks[$key]);

                                // estados
                                $ok =
                                    $sFF <= $limFF + 0.00001 &&
                                    $sFM <= $limFM + 0.00001 &&
                                    $sEE <= $limEE + 0.00001 &&
                                    $sEM <= $limEM + 0.00001;

                                $completa =
                                    abs($sFF - $limFF) < 0.00001 &&
                                    abs($sFM - $limFM) < 0.00001 &&
                                    abs($sEE - $limEE) < 0.00001 &&
                                    abs($sEM - $limEM) < 0.00001;
                            @endphp

                            <tr class="hover:bg-gray-50">
                                {{-- Eje --}}
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $r->eje }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $r->parent?->parent?->nombre ?? '—' }}
                                    </div>
                                </td>

                                {{-- Programa --}}
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $r->programa }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $r->parent?->nombre ?? '—' }}
                                    </div>
                                </td>

                                {{-- Subprograma --}}
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $r->subprograma }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $r->nombre ?? '—' }}
                                    </div>
                                </td>

                                {{-- Subtotales --}}
                                <td class="px-4 py-3 text-right">${{ number_format($fed, 2) }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($est, 2) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">${{ number_format($tot, 2) }}</td>

                                {{-- Distribución: solo estatus + botón --}}
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($locked)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-200 text-gray-800">
                                                Bloqueado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-yellow-100 text-yellow-800">
                                                Editable
                                            </span>
                                        @endif

                                        @if(!$ok)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800">
                                                Excede
                                            </span>
                                        @elseif($completa)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-800">
                                                Completa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                                Parcial
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('validador.fasp_distribuciones.edit', $r) }}"
                                       class="mt-2 inline-flex items-center px-3 py-2 bg-gray-100 text-gray-800 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                                        Ver distribución →
                                    </a>
                                </td>

                                {{-- Instituciones --}}
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2 items-center">
                                        @forelse($asigs as $a)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                                {{ $a->institucion->nombre ?? ('ID '.$a->institucion_id) }}
                                            </span>

                                            <form method="POST" action="{{ route('validador.fasp_asignaciones_institucion.quitar', $a) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-[11px] px-2 py-1 rounded bg-red-600 text-white font-semibold hover:bg-red-700">
                                                    Quitar
                                                </button>
                                            </form>
                                        @empty
                                            <span class="text-xs text-gray-400">Sin asignaciones</span>
                                        @endforelse
                                    </div>

                                    <form method="POST"
                                          action="{{ route('validador.fasp_asignaciones_institucion.asignar') }}"
                                          class="mt-2 flex gap-2 items-center">
                                        @csrf
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <input type="hidden" name="entidad" value="{{ $entidad }}">
                                        <input type="hidden" name="eje" value="{{ $r->eje }}">
                                        <input type="hidden" name="programa" value="{{ $r->programa }}">
                                        <input type="hidden" name="subprograma" value="{{ $r->subprograma }}">

                                        <select name="institucion_id" class="border rounded-md px-3 py-2 text-sm">
                                            <option value="">+ Agregar institución</option>
                                            @foreach($instituciones as $inst)
                                                @continue(in_array((int)$inst->id, $assignedIds, true))
                                                <option value="{{ $inst->id }}">{{ $inst->nombre }}</option>
                                            @endforeach
                                        </select>

                                        <button class="px-3 py-2 rounded-md bg-[#691C32] text-white text-xs font-semibold hover:bg-[#4e1324]">
                                            +
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    No hay registros del catálogo para estos filtros.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $rows->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>