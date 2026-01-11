{{-- resources/views/validador/fasp_distribuciones/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Distribución de presupuesto (Subprograma)
                </h2>
                <p class="mt-1 text-white/70 text-sm">
                    Captura de renglones por fuente · bloqueo de distribución
                </p>
            </div>

            <div class="hidden sm:flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-white text-xs border border-white/15">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Validador
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm shadow-sm">
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 mt-0.5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>{{ session('success') }}</div>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm shadow-sm">
                    <div class="flex items-start gap-2">
                        <svg class="h-5 w-5 mt-0.5 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            {{-- Encabezado / contexto --}}
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-start gap-3">
                                <div class="h-11 w-11 rounded-2xl bg-[#691C32]/10 border border-[#691C32]/15 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-[#691C32]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8c-1.657 0-3 .895-3 2v9h6v-9c0-1.105-1.343-2-3-2zM7 10V7a5 5 0 0110 0v3" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <h3 class="text-2xl sm:text-3xl font-bold text-[#691C32] leading-tight break-words">
                                        {{ $row->eje }} / {{ $row->programa }} / {{ $row->subprograma }}
                                    </h3>

                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                            Año: <span class="ml-1 font-bold">{{ $row->year }}</span>
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                            Entidad: <span class="ml-1 font-bold">{{ $row->entidad }}</span>
                                        </span>

                                        @if($locked)
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                <span class="h-2 w-2 rounded-full bg-gray-500"></span>
                                                Bloqueado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-100">
                                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                                Editable
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-3 text-sm text-gray-600 leading-relaxed">
                                        <span class="font-semibold">Eje:</span> {{ $row->parent?->parent?->nombre ?? '—' }} ·
                                        <span class="font-semibold">Programa:</span> {{ $row->parent?->nombre ?? '—' }} ·
                                        <span class="font-semibold">Subprograma:</span> {{ $row->nombre ?? '—' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row lg:flex-col items-stretch sm:items-center lg:items-end gap-2">
                            <a href="{{ route('validador.fasp_asignaciones_institucion.index', ['year' => $row->year, 'entidad' => $row->entidad, 'eje' => $row->eje, 'programa' => $row->programa]) }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white text-gray-700 text-sm font-semibold border border-gray-200 shadow-sm hover:bg-gray-50 active:scale-[.99] transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Volver a asignaciones
                            </a>

                            @if(!$locked)
                                <form method="POST" action="{{ route('validador.fasp_distribuciones.lock') }}" class="w-full">
                                    @csrf
                                    <input type="hidden" name="year" value="{{ $row->year }}">
                                    <input type="hidden" name="entidad" value="{{ $row->entidad }}">
                                    <input type="hidden" name="eje" value="{{ $row->eje }}">
                                    <input type="hidden" name="programa" value="{{ $row->programa }}">
                                    <input type="hidden" name="subprograma" value="{{ $row->subprograma }}">
                                    <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold shadow-sm hover:bg-black active:scale-[.99] transition">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />
                                        </svg>
                                        Bloquear distribución
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('validador.fasp_distribuciones.unlock') }}" class="w-full">
                                    @csrf
                                    <input type="hidden" name="year" value="{{ $row->year }}">
                                    <input type="hidden" name="entidad" value="{{ $row->entidad }}">
                                    <input type="hidden" name="eje" value="{{ $row->eje }}">
                                    <input type="hidden" name="programa" value="{{ $row->programa }}">
                                    <input type="hidden" name="subprograma" value="{{ $row->subprograma }}">
                                    <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 text-white text-sm font-semibold shadow-sm hover:bg-amber-700 active:scale-[.99] transition">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11V7a3 3 0 00-6 0v4m-3 0h12a2 2 0 012 2v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7a2 2 0 012-2z" />
                                        </svg>
                                        Desbloquear
                                    </button>
                                </form>
                            @endif
                        </div>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                {{-- fed_federal --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs font-semibold text-gray-500">fed_federal</div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $okFF ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                            {{ $okFF ? 'OK' : 'Excede' }}
                        </span>
                    </div>
                    <div class="mt-2 font-bold {{ $okFF ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($sumFF,2) }}
                        <span class="text-xs font-semibold text-gray-500">/ ${{ number_format($limFF,2) }}</span>
                    </div>
                    <div class="text-xs mt-2 {{ $restFF >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$restFF),2) }}
                        @if($restFF < -0.00001) · Excede: ${{ number_format(abs($restFF),2) }} @endif
                    </div>
                </div>

                {{-- fed_municipal --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs font-semibold text-gray-500">fed_municipal</div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $okFM ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                            {{ $okFM ? 'OK' : 'Excede' }}
                        </span>
                    </div>
                    <div class="mt-2 font-bold {{ $okFM ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($sumFM,2) }}
                        <span class="text-xs font-semibold text-gray-500">/ ${{ number_format($limFM,2) }}</span>
                    </div>
                    <div class="text-xs mt-2 {{ $restFM >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$restFM),2) }}
                        @if($restFM < -0.00001) · Excede: ${{ number_format(abs($restFM),2) }} @endif
                    </div>
                </div>

                {{-- est_estatal --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs font-semibold text-gray-500">est_estatal</div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $okEE ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                            {{ $okEE ? 'OK' : 'Excede' }}
                        </span>
                    </div>
                    <div class="mt-2 font-bold {{ $okEE ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($sumEE,2) }}
                        <span class="text-xs font-semibold text-gray-500">/ ${{ number_format($limEE,2) }}</span>
                    </div>
                    <div class="text-xs mt-2 {{ $restEE >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$restEE),2) }}
                        @if($restEE < -0.00001) · Excede: ${{ number_format(abs($restEE),2) }} @endif
                    </div>
                </div>

                {{-- est_municipal --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs font-semibold text-gray-500">est_municipal</div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $okEM ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                            {{ $okEM ? 'OK' : 'Excede' }}
                        </span>
                    </div>
                    <div class="mt-2 font-bold {{ $okEM ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($sumEM,2) }}
                        <span class="text-xs font-semibold text-gray-500">/ ${{ number_format($limEM,2) }}</span>
                    </div>
                    <div class="text-xs mt-2 {{ $restEM >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$restEM),2) }}
                        @if($restEM < -0.00001) · Excede: ${{ number_format(abs($restEM),2) }} @endif
                    </div>
                </div>

                {{-- Total --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs font-semibold text-gray-500">Total</div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $totOk ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                            {{ $totOk ? 'OK' : 'Excede' }}
                        </span>
                    </div>
                    <div class="mt-2 font-extrabold {{ $totOk ? 'text-gray-900' : 'text-red-700' }}">
                        ${{ number_format($totSum,2) }}
                        <span class="text-xs font-semibold text-gray-500">/ ${{ number_format($totLim,2) }}</span>
                    </div>
                    <div class="text-xs mt-2 {{ $totRest >= -0.00001 ? 'text-gray-600' : 'text-red-700 font-semibold' }}">
                        Restante: ${{ number_format(max(0,$totRest),2) }}
                        @if($totRest < -0.00001) · Excede: ${{ number_format(abs($totRest),2) }} @endif
                    </div>
                </div>
            </div>

            {{-- Form agregar (solo si no está bloqueado) --}}
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-lg font-semibold text-[#691C32]">Agregar renglón de distribución</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Captura una distribución por fuente. El sistema validará que no exceda el monto del catálogo.
                            </p>
                        </div>

                        @if($locked)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                🔒 Bloqueado
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-100">
                                ✏️ Editable
                            </span>
                        @endif
                    </div>

                    @if($locked)
                        <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 mt-0.5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                                </svg>
                                <div>Esta distribución está bloqueada. Desbloquea para agregar o eliminar renglones.</div>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('validador.fasp_distribuciones.store') }}"
                              class="mt-5 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                            @csrf
                            <input type="hidden" name="year" value="{{ $row->year }}">
                            <input type="hidden" name="entidad" value="{{ $row->entidad }}">
                            <input type="hidden" name="eje" value="{{ $row->eje }}">
                            <input type="hidden" name="programa" value="{{ $row->programa }}">
                            <input type="hidden" name="subprograma" value="{{ $row->subprograma }}">

                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Fuente</label>
                                <select name="fuente"
                                        class="border-gray-200 rounded-xl px-3 py-2 text-sm w-full bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]">
                                    <option value="fed_federal">fed_federal</option>
                                    <option value="fed_municipal">fed_municipal</option>
                                    <option value="est_estatal">est_estatal</option>
                                    <option value="est_municipal">est_municipal</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción</label>
                                <input name="descripcion"
                                       class="border-gray-200 rounded-xl px-3 py-2 text-sm w-full bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]"
                                       placeholder="Concepto / observación">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Institución (opcional)</label>
                                <select name="institucion_id"
                                        class="border-gray-200 rounded-xl px-3 py-2 text-sm w-full bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]">
                                    <option value="">—</option>
                                    @foreach($instituciones as $inst)
                                        <option value="{{ $inst->id }}">{{ $inst->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Monto</label>
                                <input name="monto" type="number" step="0.01" min="0.01"
                                       class="border-gray-200 rounded-xl px-3 py-2 text-sm w-full bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]">
                            </div>

                            <div class="md:col-span-6">
                                <button class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#691C32] text-white text-sm font-semibold shadow-sm hover:bg-[#5a182b] active:scale-[.99] transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Agregar
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Tabla de distribuciones --}}
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-[#691C32]">Renglones capturados</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Puedes eliminar renglones mientras no esté bloqueado.
                            </p>
                        </div>

                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                            Tabla
                        </span>
                    </div>

                    <div class="rounded-2xl border border-gray-200 overflow-hidden bg-white">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 border-b text-left font-semibold">Fuente</th>
                                        <th class="px-4 py-3 border-b text-left font-semibold">Descripción</th>
                                        <th class="px-4 py-3 border-b text-left font-semibold">Institución</th>
                                        <th class="px-4 py-3 border-b text-right font-semibold">Monto</th>
                                        <th class="px-4 py-3 border-b text-left font-semibold">Capturado por</th>
                                        <th class="px-4 py-3 border-b text-left font-semibold whitespace-nowrap">Fecha</th>
                                        <th class="px-4 py-3 border-b text-center font-semibold">Acciones</th>
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
                                            <td class="px-4 py-3 align-top">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border
                                                    {{ $fuente === 'fed_federal' ? 'bg-blue-50 text-blue-700 border-blue-100' : '' }}
                                                    {{ $fuente === 'fed_municipal' ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : '' }}
                                                    {{ $fuente === 'est_estatal' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                                    {{ $fuente === 'est_municipal' ? 'bg-green-50 text-green-700 border-green-100' : '' }}
                                                ">
                                                    {{ $fuente }}
                                                </span>

                                                @if($excedeFuente)
                                                    <div class="mt-2 inline-flex items-center gap-2 px-2.5 py-1 rounded-xl bg-red-50 border border-red-100 text-xs text-red-700 font-semibold">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        Excede el límite de la fuente
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3 align-top">
                                                <div class="text-gray-900">{{ $d->descripcion ?? '—' }}</div>
                                            </td>

                                            <td class="px-4 py-3 align-top">
                                                <span class="text-gray-800">{{ $d->institucion?->nombre ?? '—' }}</span>
                                            </td>

                                            <td class="px-4 py-3 text-right font-bold align-top whitespace-nowrap">
                                                ${{ number_format((float)$d->monto, 2) }}
                                            </td>

                                            <td class="px-4 py-3 text-xs text-gray-700 align-top">
                                                {{ $d->creador?->nombres ?? $d->creador?->name ?? '—' }}
                                            </td>

                                            <td class="px-4 py-3 text-xs text-gray-600 align-top whitespace-nowrap">
                                                {{ optional($d->created_at)->format('d/m/Y H:i') }}
                                            </td>

                                            <td class="px-4 py-3 text-center align-top">
                                                @if($locked)
                                                    <span class="text-xs text-gray-400">Bloqueado</span>
                                                @else
                                                    <form method="POST" action="{{ route('validador.fasp_distribuciones.destroy', $d) }}"
                                                          onsubmit="return confirm('¿Eliminar este renglón?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-red-600 text-white text-xs font-semibold hover:bg-red-700 active:scale-[.99] transition">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                                Aún no hay renglones de distribución para este subprograma.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Nota de bloqueo --}}
                    <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-700">
                        <b>Regla:</b> Al bloquear, el sistema valida que las 4 fuentes queden completamente distribuidas (sin “restantes”).
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>