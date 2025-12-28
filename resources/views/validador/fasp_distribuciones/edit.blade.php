{{-- resources/views/validador/fasp_distribuciones/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Distribución de presupuesto (Subprograma)
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

            {{-- Encabezado / contexto --}}
            <div class="bg-white shadow rounded-2xl p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32] mb-1">
                            {{ $row->eje }} / {{ $row->programa }} / {{ $row->subprograma }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            Año: <b>{{ $row->year }}</b> · Entidad: <b>{{ $row->entidad }}</b>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="font-semibold">Eje:</span> {{ $row->parent?->parent?->nombre ?? '—' }} ·
                            <span class="font-semibold">Programa:</span> {{ $row->parent?->nombre ?? '—' }} ·
                            <span class="font-semibold">Subprograma:</span> {{ $row->nombre ?? '—' }}
                        </p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($locked)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-900">
                                    🔒 Bloqueado
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    ✏️ Editable
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col items-start md:items-end gap-2">
                        <a href="{{ route('validador.fasp_asignaciones_institucion.index', ['year' => $row->year, 'entidad' => $row->entidad, 'eje' => $row->eje, 'programa' => $row->programa]) }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-800 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                            ← Volver a asignaciones
                        </a>

                        @if(!$locked)
                            <form method="POST" action="{{ route('validador.fasp_distribuciones.lock') }}">
                                @csrf
                                <input type="hidden" name="year" value="{{ $row->year }}">
                                <input type="hidden" name="entidad" value="{{ $row->entidad }}">
                                <input type="hidden" name="eje" value="{{ $row->eje }}">
                                <input type="hidden" name="programa" value="{{ $row->programa }}">
                                <input type="hidden" name="subprograma" value="{{ $row->subprograma }}">
                                <button class="px-4 py-2 rounded-md bg-gray-900 text-white text-xs font-semibold hover:bg-black">
                                    Bloquear distribución
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('validador.fasp_distribuciones.unlock') }}">
                                @csrf
                                <input type="hidden" name="year" value="{{ $row->year }}">
                                <input type="hidden" name="entidad" value="{{ $row->entidad }}">
                                <input type="hidden" name="eje" value="{{ $row->eje }}">
                                <input type="hidden" name="programa" value="{{ $row->programa }}">
                                <input type="hidden" name="subprograma" value="{{ $row->subprograma }}">
                                <button class="px-4 py-2 rounded-md bg-yellow-600 text-white text-xs font-semibold hover:bg-yellow-700">
                                    Desbloquear
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tarjetas de límites vs distribuido --}}
            @php
                $limFF = (float)($lim['fed_federal'] ?? 0);
                $limFM = (float)($lim['fed_municipal'] ?? 0);
                $limEE = (float)($lim['est_estatal'] ?? 0);
                $limEM = (float)($lim['est_municipal'] ?? 0);

                $sumFF = (float)($sum['fed_federal'] ?? 0);
                $sumFM = (float)($sum['fed_municipal'] ?? 0);
                $sumEE = (float)($sum['est_estatal'] ?? 0);
                $sumEM = (float)($sum['est_municipal'] ?? 0);

                $restFF = $limFF - $sumFF;
                $restFM = $limFM - $sumFM;
                $restEE = $limEE - $sumEE;
                $restEM = $limEM - $sumEM;

                $okFF = $sumFF <= $limFF + 0.00001;
                $okFM = $sumFM <= $limFM + 0.00001;
                $okEE = $sumEE <= $limEE + 0.00001;
                $okEM = $sumEM <= $limEM + 0.00001;

                $totLim = $limFF + $limFM + $limEE + $limEM;
                $totSum = $sumFF + $sumFM + $sumEE + $sumEM;
                $totRest = $totLim - $totSum;
                $totOk = $totSum <= $totLim + 0.00001;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-4">
                    <div class="text-xs text-gray-500">fed_federal</div>
                    <div class="mt-1 font-semibold {{ $okFF ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($sumFF,2) }} / ${{ number_format($limFF,2) }}
                    </div>
                    <div class="text-xs mt-1 {{ $restFF >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$restFF),2) }}
                        @if($restFF < -0.00001) · Excede: ${{ number_format(abs($restFF),2) }} @endif
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4">
                    <div class="text-xs text-gray-500">fed_municipal</div>
                    <div class="mt-1 font-semibold {{ $okFM ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($sumFM,2) }} / ${{ number_format($limFM,2) }}
                    </div>
                    <div class="text-xs mt-1 {{ $restFM >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$restFM),2) }}
                        @if($restFM < -0.00001) · Excede: ${{ number_format(abs($restFM),2) }} @endif
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4">
                    <div class="text-xs text-gray-500">est_estatal</div>
                    <div class="mt-1 font-semibold {{ $okEE ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($sumEE,2) }} / ${{ number_format($limEE,2) }}
                    </div>
                    <div class="text-xs mt-1 {{ $restEE >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$restEE),2) }}
                        @if($restEE < -0.00001) · Excede: ${{ number_format(abs($restEE),2) }} @endif
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4">
                    <div class="text-xs text-gray-500">est_municipal</div>
                    <div class="mt-1 font-semibold {{ $okEM ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($sumEM,2) }} / ${{ number_format($limEM,2) }}
                    </div>
                    <div class="text-xs mt-1 {{ $restEM >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$restEM),2) }}
                        @if($restEM < -0.00001) · Excede: ${{ number_format(abs($restEM),2) }} @endif
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-4">
                    <div class="text-xs text-gray-500">Total</div>
                    <div class="mt-1 font-semibold {{ $totOk ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($totSum,2) }} / ${{ number_format($totLim,2) }}
                    </div>
                    <div class="text-xs mt-1 {{ $totRest >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$totRest),2) }}
                        @if($totRest < -0.00001) · Excede: ${{ number_format(abs($totRest),2) }} @endif
                    </div>
                </div>
            </div>

            {{-- Form agregar (solo si no está bloqueado) --}}
            <div class="bg-white shadow rounded-2xl p-6 mb-6">
                <h4 class="text-lg font-semibold text-[#691C32]">Agregar renglón de distribución</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Captura una distribución por fuente. El sistema validará que no exceda el monto del catálogo.
                </p>

                @if($locked)
                    <div class="mt-4 px-4 py-3 rounded-lg bg-gray-100 text-gray-800 border border-gray-200 text-sm">
                        🔒 Esta distribución está bloqueada. Desbloquea para agregar o eliminar renglones.
                    </div>
                @else
                    <form method="POST" action="{{ route('validador.fasp_distribuciones.store') }}"
                          class="mt-4 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                        @csrf
                        <input type="hidden" name="year" value="{{ $row->year }}">
                        <input type="hidden" name="entidad" value="{{ $row->entidad }}">
                        <input type="hidden" name="eje" value="{{ $row->eje }}">
                        <input type="hidden" name="programa" value="{{ $row->programa }}">
                        <input type="hidden" name="subprograma" value="{{ $row->subprograma }}">

                        <div class="md:col-span-1">
                            <label class="block text-xs text-gray-500">Fuente</label>
                            <select name="fuente" class="border rounded-md px-3 py-2 text-sm w-full">
                                <option value="fed_federal">fed_federal</option>
                                <option value="fed_municipal">fed_municipal</option>
                                <option value="est_estatal">est_estatal</option>
                                <option value="est_municipal">est_municipal</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-500">Descripción</label>
                            <input name="descripcion" class="border rounded-md px-3 py-2 text-sm w-full"
                                   placeholder="Concepto / observación">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-500">Institución (opcional)</label>
                            <select name="institucion_id" class="border rounded-md px-3 py-2 text-sm w-full">
                                <option value="">—</option>
                                @foreach($instituciones as $inst)
                                    <option value="{{ $inst->id }}">{{ $inst->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-xs text-gray-500">Monto</label>
                            <input name="monto" type="number" step="0.01" min="0.01"
                                   class="border rounded-md px-3 py-2 text-sm w-full">
                        </div>

                        <div class="md:col-span-6">
                            <button class="w-full md:w-auto px-4 py-2 rounded-md bg-[#691C32] text-white text-sm font-semibold hover:bg-[#4e1324]">
                                Agregar
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Tabla de distribuciones --}}
            <div class="bg-white shadow rounded-2xl p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-[#691C32]">Renglones capturados</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            Puedes eliminar renglones mientras no esté bloqueado.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 border-b text-left">Fuente</th>
                                <th class="px-4 py-3 border-b text-left">Descripción</th>
                                <th class="px-4 py-3 border-b text-left">Institución</th>
                                <th class="px-4 py-3 border-b text-right">Monto</th>
                                <th class="px-4 py-3 border-b text-left">Capturado por</th>
                                <th class="px-4 py-3 border-b text-left">Fecha</th>
                                <th class="px-4 py-3 border-b text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($dists as $d)
                                @php
                                    $fuente = (string)$d->fuente;
                                    $limite = (float)($lim[$fuente] ?? 0);
                                    $sumaFuente = (float)($sum[$fuente] ?? 0);
                                    $excedeFuente = $sumaFuente > ($limite + 0.00001);
                                @endphp

                                <tr class="hover:bg-gray-50 {{ $excedeFuente ? 'bg-red-50' : '' }}">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                            {{ $fuente === 'fed_federal' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $fuente === 'fed_municipal' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                            {{ $fuente === 'est_estatal' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $fuente === 'est_municipal' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        ">
                                            {{ $fuente }}
                                        </span>

                                        @if($excedeFuente)
                                            <div class="text-xs text-red-700 mt-1 font-semibold">
                                                ⚠ Excede el límite de la fuente
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-gray-900">{{ $d->descripcion ?? '—' }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $d->institucion?->nombre ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-semibold">
                                        ${{ number_format((float)$d->monto, 2) }}
                                    </td>

                                    <td class="px-4 py-3 text-xs text-gray-700">
                                        {{ $d->creador?->nombres ?? $d->creador?->name ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3 text-xs text-gray-600">
                                        {{ optional($d->created_at)->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if($locked)
                                            <span class="text-xs text-gray-400">Bloqueado</span>
                                        @else
                                            <form method="POST" action="{{ route('validador.fasp_distribuciones.destroy', $d) }}"
                                                  onsubmit="return confirm('¿Eliminar este renglón?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-3 py-2 rounded-md bg-red-600 text-white text-xs font-semibold hover:bg-red-700">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Aún no hay renglones de distribución para este subprograma.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Nota de bloqueo --}}
                <div class="mt-4 text-xs text-gray-600">
                    <b>Regla:</b> Al bloquear, el sistema valida que las 4 fuentes queden completamente distribuidas (sin “restantes”).
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
