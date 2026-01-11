{{-- resources/views/validador/fasp_asignaciones_institucion/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Asignaciones FASP por institución
                </h2>
                <p class="mt-1 text-white/70 text-sm">
                    Catálogo (solo lectura) + asignación por subprograma e institución.
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

            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">

                {{-- Header + filtros --}}
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-[#691C32]">Catálogo (solo lectura) + Asignación</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Asigna subprogramas a una o más instituciones. Puedes quitar chips individuales.
                            </p>
                        </div>

                        <form method="GET" class="w-full lg:w-auto">
                            <div class="flex flex-col sm:flex-row sm:items-end gap-2">
                                <div class="flex flex-wrap gap-2 items-end">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Año</label>
                                        <input name="year" value="{{ $year }}"
                                               class="border-gray-200 rounded-xl px-3 py-2 text-sm w-28 bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Entidad</label>
                                        <input name="entidad" value="{{ $entidad }}"
                                               class="border-gray-200 rounded-xl px-3 py-2 text-sm w-28 bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Eje</label>
                                        <select name="eje"
                                                class="border-gray-200 rounded-xl px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]">
                                            <option value="">Todos</option>
                                            @foreach($ejes as $x)
                                                <option value="{{ $x }}" @selected((string)$eje === (string)$x)>{{ $x }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Programa</label>
                                        <select name="programa"
                                                class="border-gray-200 rounded-xl px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]">
                                            <option value="">Todos</option>
                                            @foreach($programas as $p)
                                                <option value="{{ $p }}" @selected((string)$programa === (string)$p)>{{ $p }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <button class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#691C32] text-white text-sm font-semibold shadow-sm hover:bg-[#5a182b] active:scale-[.99] transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 4a1 1 0 011-1h2.586a1 1 0 01.707.293l1.414 1.414A1 1 0 009.414 5H20a1 1 0 011 1v2M3 10h18M7 14h10M9 18h6" />
                                    </svg>
                                    Filtrar
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Tip:</span> Arrastra con el mouse para desplazarte horizontalmente.
                            </p>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-white text-gray-700 border border-gray-200">
                                Tabla con arrastre
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Tabla --}}
                <div class="p-6 sm:p-8">
                    <div class="rounded-2xl border border-gray-200 overflow-hidden bg-white">
                        <div id="dragScrollTableInstituciones"
                             class="overflow-x-auto overflow-y-hidden cursor-grab active:cursor-grabbing select-none"
                             style="scrollbar-gutter: stable;">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Eje</th>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Programa</th>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Subprograma</th>

                                    <th class="px-4 py-3 border-b text-right font-semibold whitespace-nowrap">Subtotal Federal</th>
                                    <th class="px-4 py-3 border-b text-right font-semibold whitespace-nowrap">Subtotal Estatal</th>
                                    <th class="px-4 py-3 border-b text-right font-semibold whitespace-nowrap">Total</th>

                                    <th class="px-4 py-3 border-b text-left font-semibold">Distribución</th>
                                    <th class="px-4 py-3 border-b text-left font-semibold">Instituciones</th>
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
                                        <td class="px-4 py-3 align-top">
                                            <div class="font-semibold text-gray-900 whitespace-nowrap">{{ $r->eje }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $r->parent?->parent?->nombre ?? '—' }}
                                            </div>
                                        </td>

                                        {{-- Programa --}}
                                        <td class="px-4 py-3 align-top">
                                            <div class="font-semibold text-gray-900 whitespace-nowrap">{{ $r->programa }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $r->parent?->nombre ?? '—' }}
                                            </div>
                                        </td>

                                        {{-- Subprograma --}}
                                        <td class="px-4 py-3 align-top">
                                            <div class="font-semibold text-gray-900 whitespace-nowrap">{{ $r->subprograma }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $r->nombre ?? '—' }}
                                            </div>
                                        </td>

                                        {{-- Subtotales --}}
                                        <td class="px-4 py-3 text-right whitespace-nowrap">${{ number_format($fed, 2) }}</td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">${{ number_format($est, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">${{ number_format($tot, 2) }}</td>

                                        {{-- Distribución: estatus + botón --}}
                                        <td class="px-5 py-3 align-top">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if($locked)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                        Bloqueado
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-100">
                                                        Editable
                                                    </span>
                                                @endif

                                                @if(!$ok)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 border border-red-100">
                                                        Excede
                                                    </span>
                                                @elseif($completa)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                        Completa
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                        Parcial
                                                    </span>
                                                @endif
                                            </div>

                                            <a href="{{ route('validador.fasp_distribuciones.edit', $r) }}"
                                               class="mt-2 inline-flex items-center gap-2 px-3 py-2 bg-white text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                                                Ver distribución
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </td>

                                        {{-- Instituciones --}}
                                        <td class="px-4 py-3 align-top">
                                            <div class="flex flex-wrap gap-2 items-center">
                                                @forelse($asigs as $a)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                        {{ $a->institucion->nombre ?? ('ID '.$a->institucion_id) }}
                                                    </span>

                                                    <form method="POST" action="{{ route('validador.fasp_asignaciones_institucion.quitar', $a) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="inline-flex items-center justify-center gap-2 text-[11px] px-2.5 py-1.5 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 active:scale-[.99] transition">
                                                            Quitar
                                                        </button>
                                                    </form>
                                                @empty
                                                    <span class="text-xs text-gray-400">Sin asignaciones</span>
                                                @endforelse
                                            </div>

                                            <form method="POST"
                                                  action="{{ route('validador.fasp_asignaciones_institucion.asignar') }}"
                                                  class="mt-3 flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
                                                @csrf
                                                <input type="hidden" name="year" value="{{ $year }}">
                                                <input type="hidden" name="entidad" value="{{ $entidad }}">
                                                <input type="hidden" name="eje" value="{{ $r->eje }}">
                                                <input type="hidden" name="programa" value="{{ $r->programa }}">
                                                <input type="hidden" name="subprograma" value="{{ $r->subprograma }}">

                                                <select name="institucion_id"
                                                        class="w-full sm:w-auto border-gray-200 rounded-xl px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-[#9F2241]/30 focus:border-[#9F2241]">
                                                    <option value="">+ Agregar institución</option>
                                                    @foreach($instituciones as $inst)
                                                        @continue(in_array((int)$inst->id, $assignedIds, true))
                                                        <option value="{{ $inst->id }}">{{ $inst->nombre }}</option>
                                                    @endforeach
                                                </select>

                                                <button class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-[#691C32] text-white text-xs font-semibold hover:bg-[#5a182b] active:scale-[.99] transition">
                                                    +
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                            No hay registros del catálogo para estos filtros.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                            <p class="text-xs text-gray-600">
                                Mantén presionado el clic y arrastra para moverte horizontalmente. También funciona con touchpad.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $rows->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Drag-to-scroll horizontal para la tabla (mouse/touchpad)
        (function () {
            const el = document.getElementById('dragScrollTableInstituciones');
            if (!el) return;

            let isDown = false;
            let startX = 0;
            let scrollLeft = 0;
            let moved = false;

            const onDown = (e) => {
                if (e.button !== undefined && e.button !== 0) return;

                // No iniciar arrastre si el usuario intenta interactuar con controles
                if (e.target.closest('a,button,input,select,textarea,label')) return;

                isDown = true;
                moved = false;
                startX = e.pageX - el.getBoundingClientRect().left;
                scrollLeft = el.scrollLeft;
                el.classList.add('cursor-grabbing');
            };

            const onUp = () => {
                if (!isDown) return;
                isDown = false;
                el.classList.remove('cursor-grabbing');
            };

            const onMove = (e) => {
                if (!isDown) return;
                e.preventDefault();

                const x = e.pageX - el.getBoundingClientRect().left;
                const walk = (x - startX);
                if (Math.abs(walk) > 3) moved = true;

                el.scrollLeft = scrollLeft - walk;
            };

            // Evita clicks accidentales tras arrastrar (especialmente en links/botones dentro de la tabla)
            const onClickCapture = (e) => {
                if (!moved) return;
                if (e.target.closest('a,button')) {
                    e.preventDefault();
                    e.stopPropagation();
                    moved = false;
                }
            };

            el.addEventListener('mousedown', onDown);
            el.addEventListener('mousemove', onMove);
            el.addEventListener('mouseleave', onUp);
            window.addEventListener('mouseup', onUp);
            el.addEventListener('click', onClickCapture, true);
        })();
    </script>
</x-app-layout>
