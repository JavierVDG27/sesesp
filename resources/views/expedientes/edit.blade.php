{{-- resources/views/expedientes/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Editar expediente
        </h2>
    </x-slot>

    @php
        $year = $year ?? ($expediente->anio_ejercicio ?? now()->year);
        $entidad = $entidad ?? ($expediente->entidad ?? '8300');

        $resumenAsignaciones = $resumenAsignaciones ?? collect();

        $ejesPermitidos = $ejesPermitidos ?? collect();
        $programasPorEje = $programasPorEje ?? [];
        $subprogramasPorEjePrograma = $subprogramasPorEjePrograma ?? [];

        $mapNombresEje = $mapNombresEje ?? [];
        $mapNombresPrograma = $mapNombresPrograma ?? [];
        $mapNombresSubprograma = $mapNombresSubprograma ?? [];

        $proyectosPorEPS = $proyectosPorEPS ?? [];
        $bienesPorProyecto = $bienesPorProyecto ?? [];

        $estatus = $expediente->estatus ?? 'borrador';
        $badge = match($estatus) {
            'borrador'      => ['bg-gray-100 text-gray-800', 'Borrador'],
            'en validacion' => ['bg-yellow-100 text-yellow-800', 'En validación'],
            'aprobado'      => ['bg-green-100 text-green-800', 'Aprobado'],
            'rechazado'     => ['bg-red-100 text-red-800', 'Rechazado'],
            default         => ['bg-gray-100 text-gray-800', ucfirst($estatus)],
        };
    @endphp

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-8">

                {{-- Encabezado del expediente --}}
                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32]">
                            Expediente: {{ $expediente->folio }}
                        </h3>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badge[0] }}">
                                Estatus: {{ $badge[1] }}
                            </span>
                            <span class="text-xs text-gray-500">
                                Puedes modificar EPS/Proyecto/Bienes y datos generales (1ra parte).
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('expedientes.index') }}"
                       class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition">
                        ← Volver
                    </a>
                </div>

                {{-- Mensajes --}}
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Errores --}}
                @if($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <div class="font-semibold mb-1">Hay errores en el formulario:</div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- 0) Asignaciones (seleccionables) --}}
                <div class="mb-6 border border-gray-200 rounded-2xl p-6">
                    <h4 class="text-lg font-semibold text-[#691C32] mb-2">Asignaciones de tu institución</h4>
                    <p class="text-xs text-gray-500 mb-4">
                        Da clic sobre un <span class="font-semibold">subprograma</span> para seleccionarlo y llenar automáticamente la estructura programática.
                    </p>

                    @if($resumenAsignaciones->isEmpty())
                        <div class="text-sm text-yellow-800 bg-yellow-50 border border-yellow-100 rounded-lg px-4 py-3">
                           ⚠️ Por el momento no tienes asignaciones activas.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($resumenAsignaciones as $item)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                        <div class="text-sm">
                                            <div class="font-semibold text-gray-800">
                                                Eje {{ $item['eje'] }} — {{ $mapNombresEje[$item['eje']] ?? '—' }}
                                            </div>
                                            <div class="text-gray-700">
                                                Programa {{ $item['programa'] }} —
                                                {{ $mapNombresPrograma[$item['eje'].'|'.$item['programa']] ?? '—' }}
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Subprogramas asignados: <span class="font-semibold">{{ count($item['subprogramas'] ?? []) }}</span>
                                        </div>
                                    </div>

                                    {{-- Chips seleccionables --}}
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach(($item['subprogramas'] ?? []) as $sp)
                                            @php
                                                $k = $item['eje'].'|'.$item['programa'].'|'.$sp;
                                                $nombreSp = $mapNombresSubprograma[$k]['nombre'] ?? '—';
                                            @endphp

                                            <button
                                                type="button"
                                                class="eps-chip inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs border transition
                                                        bg-white border-gray-200 hover:bg-[#9F2241]/10 hover:border-[#9F2241]/30
                                                        data-[selected=true]:bg-[#691C32] data-[selected=true]:border-[#691C32]
                                                        data-[selected=true]:text-white"
                                                data-selected="false"
                                                data-eje="{{ $item['eje'] }}"
                                                data-programa="{{ $item['programa'] }}"
                                                data-subprograma="{{ $sp }}"
                                                data-label="Eje {{ $item['eje'] }} · Programa {{ $item['programa'] }} · Subprograma {{ $sp }}"
                                                aria-pressed="false"
                                                >
                                                <span class="font-semibold">{{ $sp }}</span>
                                                <span class="opacity-70">—</span>
                                                <span class="opacity-90">{{ $nombreSp }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Indicador selección --}}
                        <div class="mt-4 text-xs text-gray-600">
                            Selección actual: <span id="seleccion_actual" class="font-semibold text-[#691C32]">—</span>
                        </div>
                    @endif
                </div>

                <form action="{{ route('expedientes.update', $expediente) }}" method="POST" id="form_expediente_edit">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="anio_ejercicio" value="{{ old('anio_ejercicio', $year) }}">
                    <input type="hidden" name="entidad" value="{{ old('entidad', $entidad) }}">

                    {{-- 1) Estructura programática --}}
                    <div class="mb-6 border border-gray-200 rounded-2xl p-6">
                        <h4 class="text-lg font-semibold text-[#691C32] mb-4">Estructura programática</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Área ejecutora</label>
                                <select name="area_ejecutora" id="area_ejecutora"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>

                                    @foreach(($instituciones ?? []) as $inst)
                                        <option value="{{ $inst->id }}" @selected((string)old('area_ejecutora', $expediente->area_ejecutora)===(string)$inst->id)>
                                            {{ $inst->siglas ? $inst->siglas.' - ' : '' }}{{ $inst->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    Esta lista la administra el Administrador (catálogo de instituciones).
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Eje</label>
                                <select name="eje" id="eje"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                    @foreach($ejesPermitidos as $e)
                                        <option value="{{ $e }}" @selected((string)old('eje', $expediente->eje)===(string)$e)>{{ $e }}</option>
                                    @endforeach
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="eje_nombre">—</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Programa</label>
                                <select name="programa" id="programa"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="programa_nombre">—</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subprograma</label>
                                <select name="subprograma" id="subprograma"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="subprograma_nombre">—</div>
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-gray-500">
                            Puedes cambiar Eje/Programa/Subprograma manualmente si lo necesitas.
                        </p>
                    </div>

                    {{-- 2) Selector rápido: Proyecto (PG) + Bienes (subtemas) --}}
                    <div class="mb-6 border border-gray-200 rounded-2xl p-6">
                        <h4 class="text-lg font-semibold text-[#691C32] mb-2">Selección rápida (Proyecto y bienes)</h4>
                        <p class="text-xs text-gray-500 mb-4">
                            Elige el proyecto (Partida genérica) y los bienes. Esto llenará automáticamente la clasificación.
                        </p>

                        {{-- Campos ocultos que se llenan con el selector rápido --}}
                        <input type="hidden" name="capitulo" id="capitulo_hidden" value="{{ old('capitulo', $expediente->capitulo) }}">
                        <input type="hidden" name="concepto" id="concepto_hidden" value="{{ old('concepto', $expediente->concepto) }}">
                        <input type="hidden" name="partida_generica" id="pg_hidden" value="{{ old('partida_generica', $expediente->partida_generica) }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Proyecto (Partida genérica)</label>
                                <select id="proyecto_pg"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccione...</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1" id="proyecto_pg_nombre">—</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bienes (subtemas)</label>
                                <select name="bienes[]" id="bienes_multi" multiple
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm h-44">
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Puedes seleccionar uno o varios.</p>
                            </div>
                        </div>

                        {{-- Vista rápida de lo autollenado --}}
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
                            <div class="bg-gray-50 border rounded-xl p-3">
                                <div class="text-gray-500">Capítulo</div>
                                <div class="font-semibold" id="capitulo_auto">—</div>
                            </div>
                            <div class="bg-gray-50 border rounded-xl p-3">
                                <div class="text-gray-500">Concepto</div>
                                <div class="font-semibold" id="concepto_auto">—</div>
                            </div>
                            <div class="bg-gray-50 border rounded-xl p-3">
                                <div class="text-gray-500">Partida genérica</div>
                                <div class="font-semibold" id="pg_auto">—</div>
                            </div>
                            <div class="bg-gray-50 border rounded-xl p-3">
                                <div class="text-gray-500">Nombre del proyecto</div>
                                <div class="font-semibold" id="nombre_proyecto_auto">—</div>
                            </div>
                        </div>

                        {{-- Modo avanzado: gestor de bienes --}}
                        <details class="mt-4" id="modo_avanzado">
                            <summary class="cursor-pointer text-sm text-[#691C32] font-semibold">
                                Modo avanzado: gestionar bienes (agregar/quitar) y guardar lista
                            </summary>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Disponibles --}}
                                <div class="border rounded-xl p-4 bg-gray-50">
                                    <div class="text-sm font-semibold text-gray-700 mb-2">Bienes disponibles</div>

                                    <select id="bien_disponible"
                                            class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="">Seleccione un bien...</option>
                                    </select>

                                    <button type="button" id="btn_agregar_bien"
                                            class="mt-3 inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                                        + Agregar
                                    </button>

                                    <p class="mt-2 text-xs text-gray-500">
                                        Esta lista se actualiza según el proyecto (partida genérica) seleccionado.
                                    </p>
                                </div>

                                {{-- Seleccionados --}}
                                <div class="border rounded-xl p-4 bg-white">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <div class="text-sm font-semibold text-gray-700">Bienes seleccionados</div>
                                        <button type="button" id="btn_guardar_lista_bienes"
                                                class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                                            Guardar lista
                                        </button>
                                    </div>

                                    <ul id="lista_bienes_seleccionados" class="space-y-2 text-sm"></ul>

                                    <div class="mt-2 text-xs text-gray-500">
                                        Quita o agrega bienes. Al guardar, se sincroniza con el formulario.
                                    </div>

                                    <div id="toast_bienes"
                                         class="hidden mt-3 text-xs rounded-lg border border-green-200 bg-green-50 text-green-800 px-3 py-2">
                                        ✅ Lista de bienes guardada (sincronizada).
                                    </div>
                                </div>
                            </div>
                        </details>
                    </div>

                    {{-- 3) Datos generales --}}
                    <div class="mb-6 border border-gray-200 rounded-2xl p-6">
                        <h4 class="text-lg font-semibold text-[#691C32] mb-4">Datos generales del expediente</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre del proyecto</label>
                                <input name="nombre_proyecto" id="nombre_proyecto"
                                       value="{{ old('nombre_proyecto', $expediente->nombre_proyecto) }}"
                                       class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                <p class="text-xs text-gray-500 mt-1">Se llena con la Partida genérica, pero puedes editarlo.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tema (opcional)</label>
                                <input name="tema"
                                       value="{{ old('tema', $expediente->tema) }}"
                                       class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Tipo de recurso (opcional)</label>
                            <input name="tipo_recurso" value="{{ old('tipo_recurso', $expediente->tipo_recurso) }}"
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm"
                                   placeholder="Ej. FASP / Estatal / Federal...">
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-between mt-8">
                        <a href="{{ route('expedientes.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition">
                            ← Volver
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                            Guardar cambios (1ra parte)
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

{{-- Data para JS --}}
<script>
window.__fasp = {
  programasPorEje: @json($programasPorEje),
  subprogramasPorEjePrograma: @json($subprogramasPorEjePrograma),

  // mapas de nombres
  nomEje: @json($mapNombresEje),
  nomPrograma: @json($mapNombresPrograma),
  nomSub: @json($mapNombresSubprograma),

  // selector rápido
  proyectosPorEPS: @json($proyectosPorEPS),
  bienesPorProyecto: @json($bienesPorProyecto),

  // old values (EDIT: default = valores del expediente)
  old: {
    eje: @json(old('eje', $expediente->eje)),
    programa: @json(old('programa', $expediente->programa)),
    subprograma: @json(old('subprograma', $expediente->subprograma)),
    capitulo: @json(old('capitulo', $expediente->capitulo)),
    concepto: @json(old('concepto', $expediente->concepto)),
    partida_generica: @json(old('partida_generica', $expediente->partida_generica)),
    bienes: @json(old('bienes', $expediente->bienes ?? [])),
  },
};
</script>

<script>
(function () {
  const data = window.__fasp || {};
  const old  = data.old || {};

  // ====== selects EPS ======
  const ejeSel  = document.getElementById('eje');
  const progSel = document.getElementById('programa');
  const subSel  = document.getElementById('subprograma');

  const ejeNombre  = document.getElementById('eje_nombre');
  const progNombre = document.getElementById('programa_nombre');
  const subNombre  = document.getElementById('subprograma_nombre');

  // ====== selector rápido ======
  const selPG     = document.getElementById('proyecto_pg');
  const pgNombre  = document.getElementById('proyecto_pg_nombre');
  const selBienes = document.getElementById('bienes_multi');

  const capHidden = document.getElementById('capitulo_hidden');
  const conHidden = document.getElementById('concepto_hidden');
  const pgHidden  = document.getElementById('pg_hidden');

  const capAuto    = document.getElementById('capitulo_auto');
  const conAuto    = document.getElementById('concepto_auto');
  const pgAuto     = document.getElementById('pg_auto');
  const nombreAuto = document.getElementById('nombre_proyecto_auto');

  const nombreInput = document.getElementById('nombre_proyecto');

  // ====== chips ======
  const chips = Array.from(document.querySelectorAll('.eps-chip'));
  const seleccionActual = document.getElementById('seleccion_actual');

  // ====== modo avanzado bienes ======
  const selBienDisponible  = document.getElementById('bien_disponible');
  const btnAgregarBien     = document.getElementById('btn_agregar_bien');
  const listaSeleccionados = document.getElementById('lista_bienes_seleccionados');
  const btnGuardarLista    = document.getElementById('btn_guardar_lista_bienes');
  const toast              = document.getElementById('toast_bienes');

  function setText(el, value) {
    if (!el) return;
    el.textContent = value && String(value).trim() !== '' ? value : '—';
  }

  function epsKey() {
    return `${ejeSel.value}|${progSel.value}|${subSel.value}`;
  }

  function fillSelect(select, items, valueFn, labelFn, placeholder = 'Seleccione...') {
    select.innerHTML =
      `<option value="">${placeholder}</option>` +
      (items || []).map(it => `<option value="${valueFn(it)}">${labelFn(it)}</option>`).join('');
  }

  function fillMulti(select, items, selectedValues) {
    const selected = new Set((selectedValues || []).map(String));
    select.innerHTML = (items || []).map(it => {
      const v = String(it.bien);
      const label = it.nombre ? `${v} — ${it.nombre}` : v;
      const sel = selected.has(v) ? 'selected' : '';
      return `<option value="${v}" ${sel}>${label}</option>`;
    }).join('');
  }

  // ========= CHIPS =========
  function clearChipSelection() {
    chips.forEach(btn => {
      btn.setAttribute('aria-pressed', 'false');
      btn.dataset.selected = 'false';
    });
    if (seleccionActual) setText(seleccionActual, '—');
  }

  function markChipSelected(eje, programa, subprograma) {
    let any = false;

    chips.forEach(btn => {
      const match =
        btn.dataset.eje === String(eje) &&
        btn.dataset.programa === String(programa) &&
        btn.dataset.subprograma === String(subprograma);

      btn.setAttribute('aria-pressed', match ? 'true' : 'false');
      btn.dataset.selected = match ? 'true' : 'false';
      if (match) any = true;
    });

    if (seleccionActual) {
      setText(seleccionActual, any ? `Eje ${eje} · Programa ${programa} · Subprograma ${subprograma}` : '—');
    }
  }

  function setEPSFromChip(eje, programa, subprograma) {
    ejeSel.value = String(eje);
    onEjeChange(false);

    setTimeout(() => {
      progSel.value = String(programa);
      onProgramaChange(false);

      setTimeout(() => {
        subSel.value = String(subprograma);
        onSubprogramaChange(false);
        markChipSelected(eje, programa, subprograma);
      }, 0);
    }, 0);
  }

  chips.forEach(btn => {
    btn.addEventListener('click', () => {
      setEPSFromChip(btn.dataset.eje, btn.dataset.programa, btn.dataset.subprograma);
    });
  });

  // ========= EPS dependiente =========
  function onEjeChange(keepOld = false) {
    const eje = String(ejeSel.value || '');
    setText(ejeNombre, data.nomEje?.[eje] || '—');

    const programas = data.programasPorEje?.[eje] || [];
    fillSelect(progSel, programas, x => String(x), x => String(x));
    setText(progNombre, '—');

    fillSelect(subSel, [], x => x, x => x);
    setText(subNombre, '—');

    onEPSChange();

    if (keepOld && old.programa) {
      progSel.value = old.programa;
      onProgramaChange(true);
    } else {
      clearChipSelection();
    }
  }

  function onProgramaChange(keepOld = false) {
    const eje = String(ejeSel.value || '');
    const programa = String(progSel.value || '');

    setText(progNombre, data.nomPrograma?.[`${eje}|${programa}`] || '—');

    const subs = data.subprogramasPorEjePrograma?.[`${eje}|${programa}`] || [];
    fillSelect(subSel, subs, x => String(x), x => String(x));
    setText(subNombre, '—');

    onEPSChange();

    if (keepOld && old.subprograma) {
      subSel.value = old.subprograma;
      onSubprogramaChange(true);
    } else {
      clearChipSelection();
    }
  }

  function onSubprogramaChange(keepOld = false) {
    const eje = String(ejeSel.value || '');
    const programa = String(progSel.value || '');
    const sub = String(subSel.value || '');

    const subObj = data.nomSub?.[`${eje}|${programa}|${sub}`];
    setText(subNombre, subObj?.nombre || '—');

    onEPSChange();

    const existeChip = chips.some(b =>
      b.dataset.eje === eje && b.dataset.programa === programa && b.dataset.subprograma === sub
    );
    if (existeChip) {
      markChipSelected(eje, programa, sub);
    } else {
      clearChipSelection();
    }

    if (keepOld && old.partida_generica && old.capitulo && old.concepto) {
      const v = `${old.capitulo}|${old.concepto}|${old.partida_generica}`;
      selPG.value = v;
      onProyectoChange(true);
    }
  }

  // ========= selector rápido =========
  function onEPSChange() {
    fillSelect(selPG, [], x => x, x => x);
    fillMulti(selBienes, [], []);

    capHidden.value = '';
    conHidden.value = '';
    pgHidden.value = '';

    setText(capAuto, '—');
    setText(conAuto, '—');
    setText(pgAuto, '—');
    setText(nombreAuto, '—');
    setText(pgNombre, '—');

    syncAdvancedFromMulti();

    if (!ejeSel.value || !progSel.value || !subSel.value) return;

    const key = epsKey();
    const proyectos = data.proyectosPorEPS?.[key] || [];

    fillSelect(
      selPG,
      proyectos,
      o => `${o.capitulo}|${o.concepto}|${o.pg}`,
      o => `${o.pg} — ${o.nombre ?? ''}`
    );
  }

  function onProyectoChange(keepOld = false) {
    const v = String(selPG.value || '');
    if (!v) {
      capHidden.value = '';
      conHidden.value = '';
      pgHidden.value = '';
      fillMulti(selBienes, [], []);

      setText(capAuto, '—');
      setText(conAuto, '—');
      setText(pgAuto, '—');
      setText(nombreAuto, '—');
      setText(pgNombre, '—');

      syncAdvancedFromMulti();
      return;
    }

    const [cap, con, pg] = v.split('|');

    capHidden.value = cap || '';
    conHidden.value = con || '';
    pgHidden.value  = pg  || '';

    setText(capAuto, cap);
    setText(conAuto, con);
    setText(pgAuto, pg);

    const key = epsKey();
    const proyectos = data.proyectosPorEPS?.[key] || [];
    const item = proyectos.find(x => `${x.capitulo}|${x.concepto}|${x.pg}` === v);

    const nombre = item?.nombre || '';
    setText(pgNombre, nombre);
    setText(nombreAuto, nombre);

    if (nombre && (!nombreInput.value || nombreInput.value.trim() === '')) {
      nombreInput.value = nombre;
    }

    const bienesKey = `${key}|${cap}|${con}|${pg}`;
    const bienes = data.bienesPorProyecto?.[bienesKey] || [];
    fillMulti(selBienes, bienes, keepOld ? old.bienes : []);

    syncAdvancedFromMulti();
  }

  // ========= modo avanzado bienes =========
  function getMultiOptionsMap() {
    const map = new Map();
    Array.from(selBienes.options).forEach(opt => {
      map.set(String(opt.value), String(opt.textContent || opt.value));
    });
    return map;
  }

  function getSelectedBienes() {
    return Array.from(selBienes.selectedOptions).map(o => String(o.value));
  }

  function setSelectedBienes(values) {
    const set = new Set(values.map(String));
    Array.from(selBienes.options).forEach(opt => {
      opt.selected = set.has(String(opt.value));
    });
  }

  function renderSelectedList(selectedValues) {
    if (!listaSeleccionados) return;

    const map = getMultiOptionsMap();
    if (!selectedValues.length) {
      listaSeleccionados.innerHTML = `<li class="text-xs text-gray-500">— No hay bienes seleccionados —</li>`;
      return;
    }

    listaSeleccionados.innerHTML = selectedValues.map(v => {
      const label = map.get(v) || v;
      return `
        <li class="flex items-center justify-between gap-3 border rounded-lg px-3 py-2">
          <span class="text-sm text-gray-800">${label}</span>
          <button type="button"
                  class="btn_quitar_bien text-xs px-2 py-1 rounded-md bg-red-50 border border-red-200 text-red-700 hover:bg-red-100"
                  data-bien="${v}">
            Quitar
          </button>
        </li>
      `;
    }).join('');

    Array.from(listaSeleccionados.querySelectorAll('.btn_quitar_bien')).forEach(b => {
      b.addEventListener('click', () => {
        const bien = b.dataset.bien;
        const next = getSelectedBienes().filter(x => x !== String(bien));
        setSelectedBienes(next);
        syncAdvancedFromMulti();
      });
    });
  }

  function fillDisponiblesSelect() {
    if (!selBienDisponible) return;

    const map = getMultiOptionsMap();
    const selected = new Set(getSelectedBienes());

    const opts = Array.from(map.entries())
      .filter(([value]) => !selected.has(String(value)))
      .sort((a, b) => a[0].localeCompare(b[0]));

    selBienDisponible.innerHTML =
      `<option value="">Seleccione un bien...</option>` +
      opts.map(([value, label]) => `<option value="${value}">${label}</option>`).join('');
  }

  function syncAdvancedFromMulti() {
    const selected = getSelectedBienes();
    renderSelectedList(selected);
    fillDisponiblesSelect();
  }

  if (btnAgregarBien) {
    btnAgregarBien.addEventListener('click', () => {
      const v = String(selBienDisponible?.value || '');
      if (!v) return;

      const current = new Set(getSelectedBienes());
      current.add(v);
      setSelectedBienes(Array.from(current));
      syncAdvancedFromMulti();
    });
  }

  if (btnGuardarLista) {
    btnGuardarLista.addEventListener('click', () => {
      if (toast) {
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 2000);
      }
    });
  }

  selBienes.addEventListener('change', syncAdvancedFromMulti);

  // ====== listeners ======
  ejeSel.addEventListener('change', () => onEjeChange(false));
  progSel.addEventListener('change', () => onProgramaChange(false));
  subSel.addEventListener('change', () => onSubprogramaChange(false));
  selPG.addEventListener('change', () => onProyectoChange(false));

  // ====== init ======
  if (old.eje) {
    ejeSel.value = old.eje;
    onEjeChange(true);

    if (old.eje && old.programa && old.subprograma) {
      const existe = chips.some(b =>
        b.dataset.eje === String(old.eje) &&
        b.dataset.programa === String(old.programa) &&
        b.dataset.subprograma === String(old.subprograma)
      );
      if (existe) markChipSelected(old.eje, old.programa, old.subprograma);
    }
  } else {
    onEjeChange(false);
  }

  syncAdvancedFromMulti();
})();
</script>

</x-app-layout>