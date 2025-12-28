{{-- resources/views/expedientes/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Nuevo expediente
        </h2>
    </x-slot>

    @php
        $year = $year ?? now()->year;
        $entidad = $entidad ?? '8300';

        $resumenAsignaciones = $resumenAsignaciones ?? collect();

        $ejesPermitidos = $ejesPermitidos ?? collect();
        $programasPorEje = $programasPorEje ?? [];
        $subprogramasPorEjePrograma = $subprogramasPorEjePrograma ?? [];

        $mapNombresEje = $mapNombresEje ?? [];
        $mapNombresPrograma = $mapNombresPrograma ?? [];
        $mapNombresSubprograma = $mapNombresSubprograma ?? [];

        $capitulosPorEPS = $capitulosPorEPS ?? [];
        $conceptosPorEPSC = $conceptosPorEPSC ?? [];
        $partidasGenPorEPSCC = $partidasGenPorEPSCC ?? [];
        $bienesPorEPSCCP = $bienesPorEPSCCP ?? [];

        $mapNombresCapitulo = $mapNombresCapitulo ?? [];
        $mapNombresConcepto = $mapNombresConcepto ?? [];
        $mapNombresPartidaGenerica = $mapNombresPartidaGenerica ?? [];
        $mapNombresBien = $mapNombresBien ?? [];
    @endphp

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-8">

                {{-- 0) LISTADO ASIGNACIONES (solo lectura) --}}
                <div class="mb-6 border border-gray-200 rounded-2xl p-5 bg-gray-50">
                    <h4 class="text-lg font-semibold text-[#691C32]">Asignaciones de tu institución</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        Subprogramas disponibles para capturar expedientes ({{ $year }} / {{ $entidad }}).
                    </p>

                    <div class="mt-4 space-y-3">
                        @foreach($resumenAsignaciones as $row)
                            <div class="bg-white border border-gray-200 rounded-xl p-4">
                                <div class="font-semibold text-gray-800">
                                    Eje {{ $row['eje'] }}
                                    <span class="text-xs text-gray-500 font-normal">
                                        — {{ $mapNombresEje[(string)$row['eje']] ?? '—' }}
                                    </span>
                                    · Programa {{ $row['programa'] }}
                                    <span class="text-xs text-gray-500 font-normal">
                                        — {{ $mapNombresPrograma[(string)$row['eje'].'|'.(string)$row['programa']] ?? '—' }}
                                    </span>
                                </div>

                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($row['subprogramas'] as $sub)
                                        @php
                                            $k = (string)$row['eje'].'|'.(string)$row['programa'].'|'.(string)$sub;
                                            $n = $mapNombresSubprograma[$k]['nombre'] ?? '—';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-800">
                                            {{ $sub }} <span class="ml-1 text-gray-500 font-normal">— {{ $n }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- FORM --}}
                <form action="{{ route('expedientes.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="anio_ejercicio" value="{{ old('anio_ejercicio', $year) }}">
                    <input type="hidden" name="entidad" value="{{ old('entidad', $entidad) }}">

                    {{-- 1) APARTADO: Área ejecutora + Eje/Programa/Subprograma --}}
                    <div class="mb-6 border border-gray-200 rounded-2xl p-6">
                        <h4 class="text-lg font-semibold text-[#691C32] mb-4">Estructura programática</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Área ejecutora</label>
                                <select name="area_ejecutora" id="area_ejecutora"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                    <option value="01" @selected(old('area_ejecutora')=='01')>01 - Secretaría de Seguridad Pública del Estado</option>
                                    <option value="02" @selected(old('area_ejecutora')=='02')>02 - Fiscalía General de Justicia del Estado</option>
                                    <option value="03" @selected(old('area_ejecutora')=='03')>03 - Secretariado Ejecutivo del Sistema Estatal de Seguridad Pública</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Eje</label>
                                <select name="eje" id="eje" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                    @foreach($ejesPermitidos as $e)
                                        <option value="{{ $e }}" @selected((string)old('eje')===(string)$e)>{{ $e }}</option>
                                    @endforeach
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="eje_nombre">—</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Programa</label>
                                <select name="programa" id="programa" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="programa_nombre">—</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subprograma</label>
                                <select name="subprograma" id="subprograma" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="subprograma_nombre">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- 2) APARTADO: Capítulo/Concepto/Partida Genérica/Bien --}}
                    <div class="mb-6 border border-gray-200 rounded-2xl p-6">
                        <h4 class="text-lg font-semibold text-[#691C32] mb-4">Clasificación del presupuesto (Catálogo)</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Capítulo</label>
                                <select name="capitulo" id="capitulo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="capitulo_nombre">—</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Concepto</label>
                                <select name="concepto" id="concepto" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="concepto_nombre">—</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Partida genérica</label>
                                <select name="partida_generica" id="partida_generica" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="partida_generica_nombre">—</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bien (Partida específica)</label>
                                <select name="bien" id="bien" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="bien_nombre">—</div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-3">
                            Nota: estas opciones dependen del Eje/Programa/Subprograma seleccionado y del catálogo FASP.
                        </p>
                    </div>

                    {{-- Datos generales (después) --}}
                    <div class="mb-6 border border-gray-200 rounded-2xl p-6">
                        <h4 class="text-lg font-semibold text-[#691C32] mb-4">Datos generales del expediente</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre del proyecto</label>
                                <input name="nombre_proyecto" value="{{ old('nombre_proyecto') }}"
                                       class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Dependencia</label>
                                <input name="dependencia" value="{{ old('dependencia') }}"
                                       class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Tema (opcional)</label>
                                <input name="tema" value="{{ old('tema') }}"
                                       class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Botón guardar (SIN enviar a validación) --}}
                    <div class="flex justify-end">
                        <button class="px-5 py-2 rounded-md bg-[#691C32] text-white text-sm font-semibold hover:bg-[#4e1324]">
                            Guardar expediente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Datos para selects dependientes --}}
    <script>
        window.__fasp = {
            programasPorEje: @json($programasPorEje),
            subPorEP: @json($subprogramasPorEjePrograma),

            nombresEje: @json($mapNombresEje),
            nombresProg: @json($mapNombresPrograma),
            nombresSub: @json($mapNombresSubprograma),

            capsPorEPS: @json($capitulosPorEPS),
            consPorEPSC: @json($conceptosPorEPSC),
            pgsPorEPSCC: @json($partidasGenPorEPSCC),
            bienesPorEPSCCP: @json($bienesPorEPSCCP),

            nomCap: @json($mapNombresCapitulo),
            nomCon: @json($mapNombresConcepto),
            nomPG: @json($mapNombresPartidaGenerica),
            nomBien: @json($mapNombresBien),
        };
    </script>

    {{-- Script: dependencias (Eje->Programa->Sub) y (Sub->Cap->Con->PG->Bien) --}}
    <script>
        (function () {
            const eje = document.getElementById('eje');
            const programa = document.getElementById('programa');
            const sub = document.getElementById('subprograma');

            const cap = document.getElementById('capitulo');
            const con = document.getElementById('concepto');
            const pg = document.getElementById('partida_generica');
            const bien = document.getElementById('bien');

            const tEje = document.getElementById('eje_nombre');
            const tProg = document.getElementById('programa_nombre');
            const tSub = document.getElementById('subprograma_nombre');
            const tCap = document.getElementById('capitulo_nombre');
            const tCon = document.getElementById('concepto_nombre');
            const tPG = document.getElementById('partida_generica_nombre');
            const tBien = document.getElementById('bien_nombre');

            const data = window.__fasp || {};

            function fill(sel, items) {
                sel.innerHTML = `<option value="">Seleccione...</option>` + (items || []).map(v => `<option value="${v}">${v}</option>`).join('');
            }

            function onEje() {
                const v = eje.value;
                tEje.textContent = data.nombresEje?.[v] || '—';
                fill(programa, data.programasPorEje?.[v] || []);
                fill(sub, []);
                onPrograma(); // limpia abajo
            }

            function onPrograma() {
                const e = eje.value;
                const p = programa.value;
                tProg.textContent = data.nombresProg?.[`${e}|${p}`] || '—';

                fill(sub, data.subPorEP?.[`${e}|${p}`] || []);
                onSub();
            }

            function onSub() {
                const e = eje.value;
                const p = programa.value;
                const s = sub.value;

                const obj = data.nombresSub?.[`${e}|${p}|${s}`];
                tSub.textContent = obj?.nombre || '—';

                // cargar capítulo según EPS
                fill(cap, data.capsPorEPS?.[`${e}|${p}|${s}`] || []);
                fill(con, []);
                fill(pg, []);
                fill(bien, []);

                tCap.textContent = '—';
                tCon.textContent = '—';
                tPG.textContent = '—';
                tBien.textContent = '—';
            }

            function onCap() {
                const e = eje.value, p = programa.value, s = sub.value, c = cap.value;
                tCap.textContent = data.nomCap?.[c] || '—';

                fill(con, data.consPorEPSC?.[`${e}|${p}|${s}|${c}`] || []);
                fill(pg, []);
                fill(bien, []);
                tCon.textContent = '—';
                tPG.textContent = '—';
                tBien.textContent = '—';
            }

            function onCon() {
                const e = eje.value, p = programa.value, s = sub.value, c = cap.value, co = con.value;
                tCon.textContent = data.nomCon?.[`${e}|${p}|${s}|${c}|${co}`] || '—';

                fill(pg, data.pgsPorEPSCC?.[`${e}|${p}|${s}|${c}|${co}`] || []);
                fill(bien, []);
                tPG.textContent = '—';
                tBien.textContent = '—';
            }

            function onPG() {
                const e = eje.value, p = programa.value, s = sub.value, c = cap.value, co = con.value, g = pg.value;
                tPG.textContent = data.nomPG?.[`${e}|${p}|${s}|${c}|${co}|${g}`] || '—';

                fill(bien, data.bienesPorEPSCCP?.[`${e}|${p}|${s}|${c}|${co}|${g}`] || []);
                tBien.textContent = '—';
            }

            function onBien() {
                const e = eje.value, p = programa.value, s = sub.value, c = cap.value, co = con.value, g = pg.value, b = bien.value;
                tBien.textContent = data.nomBien?.[`${e}|${p}|${s}|${c}|${co}|${g}|${b}`] || '—';
            }

            eje.addEventListener('change', onEje);
            programa.addEventListener('change', onPrograma);
            sub.addEventListener('change', onSub);

            cap.addEventListener('change', onCap);
            con.addEventListener('change', onCon);
            pg.addEventListener('change', onPG);
            bien.addEventListener('change', onBien);

            // init
            onEje();
        })();
    </script>
</x-app-layout>
