<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Catálogo FASP
        </h2>
    </x-slot>

    @php
        $hasFilters =
            request()->filled('eje') ||
            request()->filled('programa') ||
            request()->filled('subprograma') ||
            request()->filled('capitulo') ||
            request()->filled('concepto') ||
            request()->filled('partida_generica') ||
            request()->filled('bien');

        
        // paleta suave (tailwind)
        $ejePalette = [
            '01' => ['border' => 'border-blue-400',   'bg1' => 'bg-blue-50',   'bg2' => 'bg-blue-100',  'bg3' => 'bg-blue-50/50'],
            '02' => ['border' => 'border-emerald-400','bg1' => 'bg-emerald-50','bg2' => 'bg-emerald-100','bg3' => 'bg-emerald-50/50'],
            '03' => ['border' => 'border-amber-400',  'bg1' => 'bg-amber-50',  'bg2' => 'bg-amber-100', 'bg3' => 'bg-amber-50/50'],
            '04' => ['border' => 'border-violet-400', 'bg1' => 'bg-violet-50', 'bg2' => 'bg-violet-100','bg3' => 'bg-violet-50/50'],
            '05' => ['border' => 'border-teal-400',   'bg1' => 'bg-teal-50',   'bg2' => 'bg-teal-100',  'bg3' => 'bg-teal-50/50'],
        ];

        $defaultPalette = ['border' => 'border-slate-300', 'bg1' => 'bg-slate-50', 'bg2' => 'bg-slate-100', 'bg3' => 'bg-slate-50/50'];

        // según el nivel, aplicamos una “capa” distinta (suave)
        $levelTone = [
            1 => 'bg2', // EJE (un poquito más marcado)
            2 => 'bg1', // Programa
            3 => 'bg3', // Subprograma
            4 => 'bg-white',
            5 => 'bg-white',
            6 => 'bg-white',
            7 => 'bg-white',
        ];
    @endphp

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Barra superior: Año + Importar + Eliminar --}}
            <div class="bg-white shadow rounded-2xl p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-end gap-4">

                    <form method="GET" action="{{ route('admin.fasp.index') }}" class="flex gap-3 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Año</label>
                            <input type="number" name="year" value="{{ $year }}"
                                   class="border rounded-lg px-3 py-2 w-32">
                        </div>
                        <button class="bg-gray-900 text-white px-4 py-2 rounded-lg">
                            Ver
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.fasp.import') }}"
                          enctype="multipart/form-data"
                          class="flex-1 flex flex-col md:flex-row gap-3 md:items-end">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">

                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Importar Excel</label>
                            <input type="file" name="archivo" class="border rounded-lg px-3 py-2 w-full">
                            <p class="text-xs text-gray-500 mt-1">Se lee desde fila 77 (desglose). Encabezados/total se ignoran.</p>
                        </div>

                        <button class="bg-[#691C32] text-white px-6 py-2 rounded-lg">
                            Importar
                        </button>
                    </form>

                    {{-- Eliminar catálogo del año seleccionado --}}
                    <form method="POST" action="{{ route('admin.fasp.destroyByYear') }}"
                          onsubmit="return confirm('¿Seguro que quieres eliminar TODO el catálogo FASP del año {{ $year }}? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg"
                                {{ ($summary['count'] ?? 0) === 0 ? 'disabled' : '' }}>
                            Eliminar catálogo
                        </button>
                    </form>

                </div>

                {{-- Resumen --}}
                <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-3 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-gray-500">Registros</div>
                        <div class="font-semibold">{{ $summary['count'] ?? 0 }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-gray-500">Fed (Federal)</div>
                        <div class="font-semibold">{{ number_format($summary['total_fed_federal'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-gray-500">Fed (Municipal)</div>
                        <div class="font-semibold">{{ number_format($summary['total_fed_municipal'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-gray-500">Est (Estatal)</div>
                        <div class="font-semibold">{{ number_format($summary['total_est_estatal'] ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-gray-500">Est (Municipal)</div>
                        <div class="font-semibold">{{ number_format($summary['total_est_municipal'] ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- Filtros jerárquicos --}}
            <form method="GET" action="{{ route('admin.fasp.index') }}" class="bg-white shadow rounded-2xl p-4 mb-6 border">
                <input type="hidden" name="year" value="{{ $year }}">
                <div class="flex flex-wrap gap-3 items-end">

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Eje</label>
                        <select name="eje" class="border rounded-lg px-3 py-2 w-28" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(($ejes ?? collect()) as $v)
                                <option value="{{ $v }}" @selected(request('eje')==$v)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Programa</label>
                        <select name="programa" class="border rounded-lg px-3 py-2 w-28" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(($programas ?? collect()) as $v)
                                <option value="{{ $v }}" @selected(request('programa')==$v)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Subprograma</label>
                        <select name="subprograma" class="border rounded-lg px-3 py-2 w-32" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(($subprogramas ?? collect()) as $v)
                                <option value="{{ $v }}" @selected(request('subprograma')==$v)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Capítulo</label>
                        <select name="capitulo" class="border rounded-lg px-3 py-2 w-28" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(($capitulos ?? collect()) as $v)
                                <option value="{{ $v }}" @selected(request('capitulo')==$v)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Concepto</label>
                        <select name="concepto" class="border rounded-lg px-3 py-2 w-28" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(($conceptos ?? collect()) as $v)
                                <option value="{{ $v }}" @selected(request('concepto')==$v)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Partida</label>
                        <select name="partida_generica" class="border rounded-lg px-3 py-2 w-32" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach(($partidas ?? collect()) as $v)
                                <option value="{{ $v }}" @selected(request('partida_generica')==$v)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Bien</label>
                        <select name="bien" class="border rounded-lg px-3 py-2 w-32" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(($bienes ?? collect()) as $v)
                                <option value="{{ $v }}" @selected(request('bien')==$v)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button class="bg-[#691C32] text-white px-4 py-2 rounded-lg">
                            Aplicar
                        </button>
                        <a href="{{ route('admin.fasp.index', ['year'=>$year]) }}"
                           class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                            Limpiar
                        </a>
                    </div>

                    @if($hasFilters)
                        <div class="text-xs text-gray-500 ml-auto">
                            Mostrando resultados filtrados.
                        </div>
                    @endif
                </div>
            </form>

            {{-- Tabla lectura --}}
            <div class="bg-white shadow rounded-2xl overflow-x-auto border max-h-[70vh] overflow-y-auto"
                x-data="faspTree({{ $hasFilters ? 'true' : 'false' }})"
                x-init="init()">

                @if($hasFilters)
                <div class="flex flex-wrap gap-2 p-4">
                    <button type="button"
                            class="px-4 py-2 rounded-lg bg-gray-900 text-white"
                            @click="expandAll()">
                        Expandir todo
                    </button>

                    <button type="button"
                            class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300"
                            @click="collapseAll()">
                        Colapsar todo
                    </button>

                    <button type="button"
                            class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200"
                            @click="expandToLevel(2)">
                        Expandir 2 niveles
                    </button>

                    <button type="button"
                            class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200"
                            @click="expandToLevel(3)">
                        Expandir 3 niveles
                    </button>
                </div>
                @endif

                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-20">
                        <tr>
                            <th class="p-3 text-left">Nivel</th>

                            {{-- columnas por código --}}
                            <th class="p-3 text-center">Eje</th>
                            <th class="p-3 text-center">Prog</th>
                            <th class="p-3 text-center">Sub</th>
                            <th class="p-3 text-center">Cap</th>
                            <th class="p-3 text-center">Conc</th>
                            <th class="p-3 text-center">Part</th>
                            <th class="p-3 text-center">Bien</th>

                            {{-- concatenado para copiar/validar --}}
                            <th class="p-3 text-left">Código</th>

                            <th class="p-3 text-left">Nombre</th>

                            <th class="p-3 text-right">Fed</th>
                            <th class="p-3 text-right">Mun</th>
                            <th class="p-3 text-right">Subt Fed</th>

                            <th class="p-3 text-right">Est</th>
                            <th class="p-3 text-right">Mun</th>
                            <th class="p-3 text-right">Subt Est</th>

                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-left">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rows as $r)
                            @php
                                // ---- Código concatenado (para la columna Código)
                                $codigo = collect([$r->eje,$r->programa,$r->subprograma,$r->capitulo,$r->concepto,$r->partida_generica,$r->bien])
                                    ->filter(fn($v) => $v !== null && $v !== '')
                                    ->implode('.');

                                // ---- Totales por fila
                                $fedSub = (float)$r->fed_federal + (float)$r->fed_municipal;
                                $estSub = (float)$r->est_estatal + (float)$r->est_municipal;
                                $total  = $fedSub + $estSub;

                                // ---- Colores por eje/nivel
                                $ejeKey = $r->eje ?: '00';
                                $pal = $ejePalette[$ejeKey] ?? $defaultPalette;

                                $toneKey = $levelTone[$r->nivel] ?? 'bg-white';
                                $bgClass = is_string($toneKey) ? $toneKey : ($pal[$toneKey] ?? 'bg-white');

                                $borderClass = $r->nivel <= 3 ? ('border-l-4 ' . $pal['border']) : 'border-l-4 border-gray-200';
                                $rowClass = $bgClass . ' ' . $borderClass;
                            @endphp

                            <tr class="border-t hover:bg-gray-50 {{ $rowClass }}"
                                data-row
                                data-id="{{ $r->id }}"
                                data-parent="{{ $r->parent_id ?? '' }}"
                                data-nivel="{{ $r->nivel }}"
                                x-show="visible[{{ $r->id }}] ?? false"
                            >
                                <td class="p-3">{{ $r->nivel }}</td>

                                <td class="p-3 text-center">{{ $r->eje }}</td>
                                <td class="p-3 text-center">{{ $r->programa }}</td>
                                <td class="p-3 text-center">{{ $r->subprograma }}</td>
                                <td class="p-3 text-center">{{ $r->capitulo }}</td>
                                <td class="p-3 text-center">{{ $r->concepto }}</td>
                                <td class="p-3 text-center">{{ $r->partida_generica }}</td>
                                <td class="p-3 text-center">{{ $r->bien }}</td>

                                <td class="p-3 font-mono text-xs">{{ $codigo }}</td>

                                <td class="p-3">
                                    <div class="flex items-center gap-2" style="padding-left: {{ ($r->nivel - 1) * 14 }}px;">
                                        {{-- Botón expandir si tiene hijos --}}
                                        <button type="button"
                                                class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center"
                                                x-show="hasChildren({{ $r->id }}) && {{ $hasFilters ? 'true' : 'false' }}"
                                                @click="toggle({{ $r->id }})"
                                                :title="expanded[{{ $r->id }}] ? 'Contraer' : 'Expandir'">

                                            <span x-text="expanded[{{ $r->id }}] ? '−' : '+'"></span>
                                        </button>

                                        {{-- Espacio para alinear cuando no hay hijos --}}
                                        <div class="w-6 h-6" x-show="!hasChildren({{ $r->id }})"></div>

                                        <div class="font-medium text-gray-800">
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-black/5 mr-2">
                                                Nivel {{ $r->nivel }}
                                            </span>
                                            {{ $r->nombre }}
                                        </div>
                                        @if(!$hasFilters && $r->nivel == 1)
                                            <div class="mt-1">
                                                <a href="{{ route('admin.fasp.index', ['year'=>$year, 'eje'=>$r->eje]) }}"
                                                class="text-xs font-semibold text-[#691C32] hover:underline">
                                                    Ver eje {{ $r->eje }} →
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-3 text-right">{{ number_format($r->fed_federal,2) }}</td>
                                <td class="p-3 text-right">{{ number_format($r->fed_municipal,2) }}</td>
                                <td class="p-3 text-right font-semibold">{{ number_format($fedSub,2) }}</td>

                                <td class="p-3 text-right">{{ number_format($r->est_estatal,2) }}</td>
                                <td class="p-3 text-right">{{ number_format($r->est_municipal,2) }}</td>
                                <td class="p-3 text-right font-semibold">{{ number_format($estSub,2) }}</td>

                                <td class="p-3 text-right font-bold">{{ number_format($total,2) }}</td>

                                <td class="p-3 whitespace-nowrap align-top">
                                    <details class="inline-block">
                                        <summary class="cursor-pointer text-[#691C32] font-semibold">Editar</summary>

                                        <form method="POST"
                                            action="{{ route('admin.fasp.update', $r) }}"
                                            class="mt-2 grid grid-cols-2 gap-2 bg-white border rounded-lg p-3 w-[360px]">
                                            @csrf
                                            @method('PATCH')

                                            {{-- Nombre --}}
                                            <label class="col-span-2 text-xs font-semibold text-gray-600">Nombre</label>
                                            <input class="border rounded px-2 py-1 col-span-2"
                                                name="nombre"
                                                value="{{ $r->nombre }}" />

                                            {{-- FASP --}}
                                            <label class="text-xs font-semibold text-gray-600">Fed. Federal (FASP)</label>
                                            <label class="text-xs font-semibold text-gray-600">Fed. Municipal (FASP)</label>
                                            <input class="border rounded px-2 py-1" name="fed_federal" value="{{ $r->fed_federal }}" />
                                            <input class="border rounded px-2 py-1" name="fed_municipal" value="{{ $r->fed_municipal }}" />

                                            {{-- Estatal --}}
                                            <label class="text-xs font-semibold text-gray-600">Est. Estatal</label>
                                            <label class="text-xs font-semibold text-gray-600">Est. Municipal</label>
                                            <input class="border rounded px-2 py-1" name="est_estatal" value="{{ $r->est_estatal }}" />
                                            <input class="border rounded px-2 py-1" name="est_municipal" value="{{ $r->est_municipal }}" />

                                            {{-- Unidad / Cantidad --}}
                                            <label class="text-xs font-semibold text-gray-600">Unidad de medida</label>
                                            <label class="text-xs font-semibold text-gray-600">Cantidad</label>
                                            <input class="border rounded px-2 py-1" name="unidad_medida" value="{{ $r->unidad_medida }}" />
                                            <input class="border rounded px-2 py-1" name="cantidad" value="{{ $r->cantidad }}" />

                                            {{-- RLCF --}}
                                            <label class="col-span-2 text-xs font-semibold text-gray-600">RLCF</label>
                                            <input class="border rounded px-2 py-1 col-span-2"
                                                name="rlcf"
                                                value="{{ $r->rlcf }}" />

                                            <button class="bg-[#691C32] text-white rounded px-3 py-1 col-span-2 mt-2">
                                                Guardar
                                            </button>
                                        </form>
                                    </details>
                                    <details class="inline-block ml-2">
                                        <summary class="cursor-pointer text-emerald-700 font-semibold">➕ Agregar</summary>
                                        @php
                                            $labels = [
                                                1 => 'Programa',
                                                2 => 'Subprograma',
                                                3 => 'Capítulo',
                                                4 => 'Concepto',
                                                5 => 'Partida genérica',
                                                6 => 'Bien',
                                                7 => null,
                                            ];
                                            $childLabel = $labels[$r->nivel] ?? null;
                                        @endphp

                                        @if($r->nivel < 7)
                                        <form method="POST"
                                            action="{{ route('admin.fasp.store') }}"
                                            class="mt-2 grid grid-cols-2 gap-2 bg-white border rounded-lg p-3 w-[360px]">
                                            @csrf

                                            <input type="hidden" name="year" value="{{ $r->year }}">
                                            <input type="hidden" name="parent_id" value="{{ $r->id }}">
                                            <input type="hidden" name="parent_nivel" value="{{ $r->nivel }}">

                                            {{-- heredados: la rama actual --}}
                                            <input type="hidden" name="eje" value="{{ $r->eje }}">
                                            <input type="hidden" name="programa" value="{{ $r->programa }}">
                                            <input type="hidden" name="subprograma" value="{{ $r->subprograma }}">
                                            <input type="hidden" name="capitulo" value="{{ $r->capitulo }}">
                                            <input type="hidden" name="concepto" value="{{ $r->concepto }}">
                                            <input type="hidden" name="partida_generica" value="{{ $r->partida_generica }}">

                                            <div class="col-span-2 text-xs text-gray-500">
                                                Se agregará un(a) <b>{{ $childLabel }}</b> debajo de: <span class="font-mono">{{ $codigo }}</span>
                                            </div>

                                            <label class="text-xs font-semibold text-gray-600 col-span-2">Código del hijo ({{ $childLabel }})</label>
                                            <input class="border rounded px-2 py-1 col-span-2" name="child_codigo" placeholder="Solo el código del hijo. Ej: 01 | 3000 | 3600 | 361 | 0507">

                                            <label class="text-xs font-semibold text-gray-600 col-span-2">Nombre</label>
                                            <input class="border rounded px-2 py-1 col-span-2" name="nombre" placeholder="Nombre del {{ strtolower($childLabel) }}">

                                            {{-- Solo si el hijo será BIEN (cuando parent es Partida nivel 6) mostramos unidad/cantidad y montos --}}
                                            @if($r->nivel == 6)
                                                <label class="text-xs font-semibold text-gray-600">Fed. Federal</label>
                                                <label class="text-xs font-semibold text-gray-600">Fed. Municipal</label>
                                                <input class="border rounded px-2 py-1" name="fed_federal" value="0">
                                                <input class="border rounded px-2 py-1" name="fed_municipal" value="0">

                                                <label class="text-xs font-semibold text-gray-600">Est. Estatal</label>
                                                <label class="text-xs font-semibold text-gray-600">Est. Municipal</label>
                                                <input class="border rounded px-2 py-1" name="est_estatal" value="0">
                                                <input class="border rounded px-2 py-1" name="est_municipal" value="0">

                                                <label class="text-xs font-semibold text-gray-600">Unidad</label>
                                                <label class="text-xs font-semibold text-gray-600">Cantidad</label>
                                                <input class="border rounded px-2 py-1" name="unidad_medida" placeholder="Ej: Paquete">
                                                <input class="border rounded px-2 py-1" name="cantidad" placeholder="Ej: 1">

                                                <label class="text-xs font-semibold text-gray-600 col-span-2">RLCF</label>
                                                <input class="border rounded px-2 py-1 col-span-2" name="rlcf" placeholder="Opcional">
                                            @endif

                                            <button class="bg-emerald-700 text-white rounded px-3 py-1 col-span-2 mt-2">
                                                Crear {{ $childLabel }}
                                            </button>
                                        </form>
                                        @else
                                            <div class="mt-2 text-xs text-gray-500">Este nivel (BIEN) no admite hijos.</div>
                                        @endif
                                    </details>
                                    <form method="POST"
                                        action="{{ route('admin.fasp.destroyRow', $r->id) }}"
                                        onsubmit="return confirm('¿Quieres borrar SOLO este registro: {{ $codigo }} ?');"
                                        class="inline-block ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 font-semibold">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="19" class="p-6 text-center text-gray-500">
                                    No hay datos cargados para el año {{ $year }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
    function faspTree(hasFilters) {
        return {
            childrenMap: {},   // parentId => [childId...]
            visible: {},       // id => bool
            expanded: {},      // id => bool
            allIds: [],        // ids de todos los nodos

            init() {
                const rows = Array.from(document.querySelectorAll('[data-row]'));

                rows.forEach(tr => {
                    const id = parseInt(tr.dataset.id);
                    const parent = tr.dataset.parent ? parseInt(tr.dataset.parent) : null;

                    this.allIds.push(id);

                    if (!this.childrenMap[parent]) this.childrenMap[parent] = [];
                    this.childrenMap[parent].push(id);

                    this.visible[id] = false;
                    this.expanded[id] = false;
                });

                // Si hay filtros: muestra todo para que sea “plano” y fácil de leer
                if (hasFilters) {
                    this.expandAll();
                    return;
                }

                // Inicial sin filtros: mostrar solo raíces (EJES)
                (this.childrenMap[null] || []).forEach(id => {
                    this.visible[id] = true;
                });
            },

            hasChildren(id) {
                return (this.childrenMap[id] || []).length > 0;
            },

            toggle(id) {
                const isOpen = !!this.expanded[id];
                if (isOpen) {
                    this.collapseNode(id);
                } else {
                    this.expandNode(id);
                }
            },

            expandNode(id) {
                this.expanded[id] = true;
                (this.childrenMap[id] || []).forEach(childId => {
                    this.visible[childId] = true;
                });
            },

            collapseNode(id) {
                this.expanded[id] = false;
                this.hideDescendants(id);
            },

            hideDescendants(id) {
                (this.childrenMap[id] || []).forEach(childId => {
                    this.visible[childId] = false;
                    this.expanded[childId] = false;
                    this.hideDescendants(childId);
                });
            },

            expandAll() {
                this.allIds.forEach(id => this.visible[id] = true);
                this.allIds.forEach(id => this.expanded[id] = this.hasChildren(id));
            },

            collapseAll() {
                this.allIds.forEach(id => {
                    this.visible[id] = false;
                    this.expanded[id] = false;
                });

                (this.childrenMap[null] || []).forEach(id => {
                    this.visible[id] = true;
                });
            },

            expandToLevel(maxNivel) {
                const rows = Array.from(document.querySelectorAll('[data-row]'));

                this.allIds.forEach(id => {
                    this.visible[id] = false;
                    this.expanded[id] = false;
                });

                rows.forEach(tr => {
                    const id = parseInt(tr.dataset.id);
                    const nivel = parseInt(tr.dataset.nivel);

                    if (nivel <= maxNivel) {
                        this.visible[id] = true;
                        if (nivel < maxNivel && this.hasChildren(id)) {
                            this.expanded[id] = true;
                        }
                    }
                });
            }
        }
    }
    </script>

</x-app-layout>
