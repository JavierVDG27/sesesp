{{-- resources/views/expedientes/segunda_parte/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Captura del expediente (2da parte)
        </h2>
    </x-slot>

@php
    $detalle = $expediente->detalle;

    // === Nombres default desde catálogo (ya vienen como mapas del controller) ===
    $ejeKey = "{$expediente->eje}|{$expediente->programa}|{$expediente->subprograma}";
    $ejeNombre = $mapNombresEje[(string)$expediente->eje] ?? '';
    $programaNombre = $mapNombresPrograma["{$expediente->eje}|{$expediente->programa}"] ?? '';
    $subNombre = $mapNombresSubprograma[$ejeKey]['nombre'] ?? '';

    $ejeDefault = trim((string)$expediente->eje) . '. ' . trim($ejeNombre);
    $programaDefaultLocal = $programaDefault ?? (trim((string)$expediente->programa) . '. ' . trim($programaNombre));
    $subDefaultLocal = $subDefault ?? (trim((string)$expediente->subprograma) . '. ' . trim($subNombre));

    // Partida genérica (nivel 6)
    $pgKey = "{$expediente->eje}|{$expediente->programa}|{$expediente->subprograma}|{$expediente->capitulo}|{$expediente->concepto}|{$expediente->partida_generica}";
    $pgNombre = $mapNombresPartidaGenerica[$pgKey] ?? '';
    $partidaLabelLocal = $partidaLabel ?? (trim((string)$expediente->partida_generica) . '. ' . trim((string)$pgNombre));

    // Bienes (nivel 7/8)
    $bienes = (array)($expediente->bienes ?? []);
    $bienesLista = [];
    foreach ($bienes as $bien) {
        $bienKey = "{$expediente->eje}|{$expediente->programa}|{$expediente->subprograma}|{$expediente->capitulo}|{$expediente->concepto}|{$expediente->partida_generica}|{$bien}";
        $bn = $mapNombresBien[$bienKey] ?? '';
        $bienesLista[] = trim((string)$bien) . '. ' . trim((string)$bn);
    }
    $bienesLista = array_values(array_filter($bienesLista, fn($x) => trim($x) !== ''));

    $bienesLabel = '';
    if (count($bienesLista) === 1) {
        $bienesLabel = $bienesLista[0];
    } elseif (count($bienesLista) === 2) {
        $bienesLabel = $bienesLista[0] . ' y ' . $bienesLista[1];
    } elseif (count($bienesLista) >= 3) {
        $last = array_pop($bienesLista);
        $bienesLabel = implode(', ', $bienesLista) . ' y ' . $last;
    }

    // Marco legal (estructura JSON)
    $marco = [];
    if ($detalle?->marco_legal_json) {
        $tmp = json_decode($detalle->marco_legal_json, true);
        if (is_array($tmp)) $marco = $tmp;
    }

    // Logo fijo
    $logoUrl = asset('images/LogoExpediente.png');

    // Autosave base
    $autosaveBase = route('expedientes.segunda.autosave', ['expediente' => $expediente->id, 'section' => '__SECTION__']);

    // Defaults de textos 9..20
    $t9  = $detalle->no_aplica_9  ?? 'No aplica.';
    $t10 = $detalle->no_aplica_10 ?? 'No aplica.';
    $t11 = $detalle->no_aplica_11 ?? 'No aplica.';
    $t12 = $detalle->no_aplica_12 ?? 'No aplica.';
    $t13 = $detalle->no_aplica_13 ?? 'No aplica.';
    $t14 = $detalle->no_aplica_14 ?? 'No aplica.';
    $t15 = $detalle->no_aplica_15 ?? 'No aplica.';
    $t16 = $detalle->no_aplica_16 ?? 'No aplica.';
    $t17 = $detalle->no_aplica_17 ?? 'No aplica.';
    $t18 = $detalle->no_aplica_18 ?? 'No aplica.';
    $t19 = $detalle->no_aplica_19 ?? 'No aplica.';
    $t20 = $detalle->no_aplica_20 ?? "Anexo 1. Cotización.";

    $payload = [
        'expedienteId' => $expediente->id,
        'csrf' => csrf_token(),
        'autosaveUrlBase' => $autosaveBase,
        'initial' => [
            'step' => 1,

            // Portada editables
            'titulo_documento' => $detalle->titulo_documento ?? 'EXPEDIENTE TÉCNICO',
            'subtitulo_documento' => $detalle->subtitulo_documento ?? 'ADQUISICIÓN Y CONTRATACIÓN DE SERVICIOS',
            'fasp_texto' => $detalle->fasp_texto ?? 'FONDO DE APORTACIONES PARA LA SEGURIDAD PÚBLICA DE LOS ESTADOS Y DEL DISTRITO FEDERAL (FASP)',
            'ejercicio_fiscal_label' => $detalle->ejercicio_fiscal_label ?? 'EJERCICIO FISCAL AÑO',
            'logo_url' => $logoUrl,

            // Año base + override
            'anio_base' => (int)$expediente->anio_ejercicio,
            'anio_override' => $detalle->anio_override ?? null,

            // Overrides (editables)
            'eje_override' => $detalle->eje_override ?? '',
            'programa_override' => $detalle->programa_override ?? '',
            'subprograma_override' => $detalle->subprograma_override ?? '',

            // Defaults (referencia)
            'eje_default' => $ejeDefault,
            'programa_default' => $programaDefaultLocal,
            'subprograma_default' => $subDefaultLocal,

            // Partida/Bienes
            'partida_label' => $partidaLabelLocal,
            'bienes_label' => $bienesLabel,

            // Textos
            'introduccion' => $detalle->introduccion ?? '',
            'marco_legal' => $marco,

            // 3..5
            'objeto' => $detalle->objeto ?? '',
            'alcance' => $detalle->alcance ?? '',
            'justificacion' => $detalle->justificacion ?? '',

            // TABLAS 6/7/8 (vienen del controller)
            'tabla6' => $t6 ?? [],
            'tabla7' => $t7 ?? [],
            'tabla8' => $t8 ?? [],

            // ✅ TABLA 8 globales (IMPORTANTE)
            'tabla8_fecha_entrega' => $detalle->tabla8_fecha_entrega ?? '',
            'tabla8_responsable_validar' => $detalle->tabla8_responsable_validar ?? '',
            'tabla8_lugar_entrega' => $detalle->tabla8_lugar_entrega ?? '',

            // 9..20
            'no_aplica_9'  => $t9,
            'no_aplica_10' => $t10,
            'no_aplica_11' => $t11,
            'no_aplica_12' => $t12,
            'no_aplica_13' => $t13,
            'no_aplica_14' => $t14,
            'no_aplica_15' => $t15,
            'no_aplica_16' => $t16,
            'no_aplica_17' => $t17,
            'no_aplica_18' => $t18,
            'no_aplica_19' => $t19,
            'no_aplica_20' => $t20,

            // 21 (firmas)
            'responsable_subprograma_nombre' => $detalle->responsable_subprograma_nombre ?? '',
            'responsable_subprograma_cargo'  => $detalle->responsable_subprograma_cargo ?? '',
            'titular_dependencia_nombre'     => $detalle->titular_dependencia_nombre ?? '',
            'titular_dependencia_cargo'      => $detalle->titular_dependencia_cargo ?? '',
        ],
    ];
@endphp

<script>
    window.__EXP_WIZARD__ = @json($payload);

    document.addEventListener('alpine:init', () => {
        Alpine.data('expWizard', (payload) => ({
            step: payload.initial.step || 1,
            previewOpen: false,
            saving: false,
            savedMsg: '',
            saveError: '',

            // Sub-tab del paso 5 (tablas)
            tablasTab: 't6', // t6 | t7 | t8

            steps: [
                { key: 'datos',          label: 'Datos generales (solo lectura)' },
                { key: 'portada_intro',  label: 'Portada e Introducción' },
                { key: 'marco_legal',    label: 'Marco legal' },
                { key: 'seccion_3_5',    label: 'Objeto, Alcance y Justificación' },
                { key: 'tablas_6_8',     label: '5. Tablas (6, 7 y 8)' },
                { key: 'seccion_9_20',   label: 'Secciones 9 a 20' },
                { key: 'seccion_17_21',  label: '21. Validación del expediente' },
            ],

            form: {
                titulo_documento: payload.initial.titulo_documento || '',
                subtitulo_documento: payload.initial.subtitulo_documento || '',
                fasp_texto: payload.initial.fasp_texto || '',
                ejercicio_fiscal_label: payload.initial.ejercicio_fiscal_label || '',
                logo_url: payload.initial.logo_url || '',

                anio_base: payload.initial.anio_base || new Date().getFullYear(),
                anio_override: payload.initial.anio_override,

                eje_override: payload.initial.eje_override || '',
                programa_override: payload.initial.programa_override || '',
                subprograma_override: payload.initial.subprograma_override || '',

                eje_default: payload.initial.eje_default || '',
                programa_default: payload.initial.programa_default || '',
                subprograma_default: payload.initial.subprograma_default || '',

                partida_label: payload.initial.partida_label || '',
                bienes_label: payload.initial.bienes_label || '',

                introduccion: payload.initial.introduccion || '',
                marco_legal: Array.isArray(payload.initial.marco_legal) ? payload.initial.marco_legal : [],

                objeto: payload.initial.objeto || '',
                alcance: payload.initial.alcance || '',
                justificacion: payload.initial.justificacion || '',

                // tablas
                tabla6: Array.isArray(payload.initial.tabla6) ? payload.initial.tabla6 : [],
                tabla7: Array.isArray(payload.initial.tabla7) ? payload.initial.tabla7 : [],
                tabla8: Array.isArray(payload.initial.tabla8) ? payload.initial.tabla8 : [],

                // ✅ tabla 8 globales
                tabla8_fecha_entrega: payload.initial.tabla8_fecha_entrega || '',
                tabla8_responsable_validar: payload.initial.tabla8_responsable_validar || '',
                tabla8_lugar_entrega: payload.initial.tabla8_lugar_entrega || '',

                // 9..20
                no_aplica_9:  payload.initial.no_aplica_9 || '',
                no_aplica_10: payload.initial.no_aplica_10 || '',
                no_aplica_11: payload.initial.no_aplica_11 || '',
                no_aplica_12: payload.initial.no_aplica_12 || '',
                no_aplica_13: payload.initial.no_aplica_13 || '',
                no_aplica_14: payload.initial.no_aplica_14 || '',
                no_aplica_15: payload.initial.no_aplica_15 || '',
                no_aplica_16: payload.initial.no_aplica_16 || '',
                no_aplica_17: payload.initial.no_aplica_17 || '',
                no_aplica_18: payload.initial.no_aplica_18 || '',
                no_aplica_19: payload.initial.no_aplica_19 || '',
                no_aplica_20: payload.initial.no_aplica_20 || '',

                // firmas
                responsable_subprograma_nombre: payload.initial.responsable_subprograma_nombre || '',
                responsable_subprograma_cargo: payload.initial.responsable_subprograma_cargo || '',
                titular_dependencia_nombre: payload.initial.titular_dependencia_nombre || '',
                titular_dependencia_cargo: payload.initial.titular_dependencia_cargo || '',
            },

            stepKey() { return this.steps[this.step - 1]?.key || ''; },
            goToStep(n) {
                const nn = Number(n);
                if (!Number.isFinite(nn)) return;
                if (nn < 1) return;
                if (nn > this.steps.length) return;
                this.step = nn;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            prev() { if (this.step > 1) this.goToStep(this.step - 1); },
            next() { if (this.step < this.steps.length) this.goToStep(this.step + 1); },

            // ===== Marco legal UI =====
            addBloque() { this.form.marco_legal.push({ titulo: '', articulos: [] }); },
            removeBloque(i) { this.form.marco_legal.splice(i, 1); },
            addArticulo(i) { this.form.marco_legal[i].articulos.push({ articulo: '', descripcion: '', incisos: [] }); },
            removeArticulo(i, j) { this.form.marco_legal[i].articulos.splice(j, 1); },
            addInciso(i, j) {
                this.form.marco_legal[i].articulos[j].incisos ||= [];
                this.form.marco_legal[i].articulos[j].incisos.push({ romano: '', descripcion: '' });
            },
            removeInciso(i, j, k) { this.form.marco_legal[i].articulos[j].incisos.splice(k, 1); },

            // ===== Tabla 7: editor técnico =====
            ensureTechArray(rowIdx) {
                const v = this.form.tabla7[rowIdx]?.descripcion_tecnica;
                if (!Array.isArray(v)) this.form.tabla7[rowIdx].descripcion_tecnica = [];
            },
            addTechItem(rowIdx, tipo) {
                this.ensureTechArray(rowIdx);
                this.form.tabla7[rowIdx].descripcion_tecnica.push({ tipo, texto: '' });
            },
            removeTechItem(rowIdx, k) {
                this.ensureTechArray(rowIdx);
                this.form.tabla7[rowIdx].descripcion_tecnica.splice(k, 1);
            },

            // Totales Tabla 7
            t7RowImporte(row) {
                const cant = Number(row.cantidad || 0);
                const pu = (row.precio_unitario === null || row.precio_unitario === '' || row.precio_unitario === undefined)
                    ? null
                    : Number(row.precio_unitario);
                if (pu === null || Number.isNaN(pu)) return 0;
                return Math.round((pu * cant) * 100) / 100;
            },
            t7Subtotal() {
                return this.form.tabla7.reduce((acc, r) => acc + this.t7RowImporte(r), 0);
            },
            t7IVA() {
                return Math.round((this.t7Subtotal() * 0.16) * 100) / 100;
            },
            t7Total() {
                return Math.round(((this.t7Subtotal() + this.t7IVA())) * 100) / 100;
            },

            autoGrow(el) {
                if (!el) return;
                el.style.height = 'auto';
                el.style.height = (el.scrollHeight + 2) + 'px';
            },

            autosaveUrl(sectionKey) {
                return payload.autosaveUrlBase.replace('__SECTION__', sectionKey);
            },

            anioFinal() {
                const v = this.form.anio_override;
                if (v === null || v === undefined || v === '' || Number.isNaN(Number(v))) return this.form.anio_base;
                return Number(v);
            },

            epsEje() { return this.form.eje_override?.trim() ? this.form.eje_override : this.form.eje_default; },
            epsProg() { return this.form.programa_override?.trim() ? this.form.programa_override : this.form.programa_default; },
            epsSub() { return this.form.subprograma_override?.trim() ? this.form.subprograma_override : this.form.subprograma_default; },

            // Preview anexos
            anexosParsed() {
                const raw = (this.form.no_aplica_20 || '').split('\n').map(s => s.trim()).filter(Boolean);
                return raw.map(line => {
                    const m = line.match(/^(Anexo\s+\d+\.)\s*(.*)$/i);
                    if (!m) return { head: '', tail: line };
                    return { head: m[1], tail: m[2] || '' };
                });
            },

            async saveSection(sectionKey = null) {
                const key = sectionKey || this.stepKey();
                let body = {};
                let endpointKey = key;

                if (key === 'portada_intro') {
                    body = {
                        titulo_documento: this.form.titulo_documento,
                        subtitulo_documento: this.form.subtitulo_documento,
                        fasp_texto: this.form.fasp_texto,
                        ejercicio_fiscal_label: this.form.ejercicio_fiscal_label,
                        anio_override: this.form.anio_override,
                        eje_override: this.form.eje_override,
                        programa_override: this.form.programa_override,
                        subprograma_override: this.form.subprograma_override,
                        introduccion: this.form.introduccion,
                    };
                } else if (key === 'marco_legal') {
                    body = { marco_legal: this.form.marco_legal };
                } else if (key === 'seccion_3_5') {
                    body = { objeto: this.form.objeto, alcance: this.form.alcance, justificacion: this.form.justificacion };
                } else if (key === 'tablas_6_8') {
                    if (this.tablasTab === 't6') {
                        endpointKey = 'tablas_6';
                        body = {
                            rows: this.form.tabla6.map(r => {
                                const parsed = (r.meta_cantidad === '' || r.meta_cantidad === null || r.meta_cantidad === undefined)
                                    ? null
                                    : parseInt(r.meta_cantidad, 10);
                                const meta = Number.isFinite(parsed) ? parsed : null;

                                return {
                                    id: r.id ?? null,
                                    orden: r.orden,
                                    unidad_medida: r.unidad_medida ?? '',
                                    meta_cantidad: meta,
                                    aportacion: r.aportacion ?? '',
                                };
                            })
                        };
                    } else if (this.tablasTab === 't7') {
                        endpointKey = 'tablas_7';
                        body = {
                            rows: this.form.tabla7.map(r => ({
                                id: r.id ?? null,
                                orden: r.orden,
                                titulo_producto: r.titulo_producto ?? '',
                                descripcion_tecnica: Array.isArray(r.descripcion_tecnica) ? r.descripcion_tecnica : [],
                                precio_unitario: (r.precio_unitario === '' || r.precio_unitario === null || r.precio_unitario === undefined)
                                    ? null
                                    : Number(r.precio_unitario),
                            }))
                        };
                    } else {
                        endpointKey = 'tablas_8';
                        body = {
                            tabla8_fecha_entrega: this.form.tabla8_fecha_entrega,
                            tabla8_responsable_validar: this.form.tabla8_responsable_validar,
                            tabla8_lugar_entrega: this.form.tabla8_lugar_entrega,
                            rows: this.form.tabla8.map(r => ({ id: r.id ?? null, orden: r.orden })),
                            };
                    }
                } else if (key === 'seccion_9_20') {
                    body = {
                        no_aplica_9:  this.form.no_aplica_9,
                        no_aplica_10: this.form.no_aplica_10,
                        no_aplica_11: this.form.no_aplica_11,
                        no_aplica_12: this.form.no_aplica_12,
                        no_aplica_13: this.form.no_aplica_13,
                        no_aplica_14: this.form.no_aplica_14,
                        no_aplica_15: this.form.no_aplica_15,
                        no_aplica_16: this.form.no_aplica_16,
                        no_aplica_17: this.form.no_aplica_17,
                        no_aplica_18: this.form.no_aplica_18,
                        no_aplica_19: this.form.no_aplica_19,
                        no_aplica_20: this.form.no_aplica_20,
                    };
                } else if (key === 'seccion_17_21') {
                    body = {
                        responsable_subprograma_nombre: this.form.responsable_subprograma_nombre,
                        responsable_subprograma_cargo: this.form.responsable_subprograma_cargo,
                        titular_dependencia_nombre: this.form.titular_dependencia_nombre,
                        titular_dependencia_cargo: this.form.titular_dependencia_cargo,
                    };
                } else if (key === 'datos') {
                    return { ok: true };
                } else {
                    return { ok: true, note: 'Sección no implementada en Blade.' };
                }

                this.saving = true;
                this.savedMsg = '';
                this.saveError = '';

                const res = await fetch(this.autosaveUrl(endpointKey), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': payload.csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(body),
                });

                const data = await res.json().catch(() => ({}));
                this.saving = false;

                if (!res.ok || data.ok === false) {
                    this.saveError = data.message || 'No se pudo guardar la sección.';
                    throw new Error(this.saveError);
                }

                this.savedMsg = 'Sección guardada.';
                return data;
            },

            async saveAll() {
                await this.saveSection('portada_intro');
                await this.saveSection('marco_legal');
                await this.saveSection('seccion_3_5');

                const prevTab = this.tablasTab;
                this.tablasTab = 't6'; await this.saveSection('tablas_6_8');
                this.tablasTab = 't7'; await this.saveSection('tablas_6_8');
                this.tablasTab = 't8'; await this.saveSection('tablas_6_8');
                this.tablasTab = prevTab;

                await this.saveSection('seccion_9_20');
                await this.saveSection('seccion_17_21');
                return true;
            },
        }));
    });
</script>

<div class="py-8 bg-gray-50 min-h-screen" x-data="expWizard(window.__EXP_WIZARD__)">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-2xl p-6 md:p-8">

            {{-- TOP ACTIONS --}}
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('expedientes.index') }}"
                       class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        ← Volver a expedientes
                    </a>

                    <button type="button"
                            class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            @click="goToStep(1)">
                        ⤒ Ir al inicio
                    </button>
                </div>

                <button type="button"
                        class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        @click="previewOpen = !previewOpen">
                    <span x-text="previewOpen ? 'Ocultar vista previa' : 'Ver vista previa'"></span>
                </button>
            </div>

            {{-- Header --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-[#691C32]">Captura del expediente</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Paso <span class="font-semibold" x-text="step"></span> de
                        <span class="font-semibold" x-text="steps.length"></span>
                        — <span class="font-semibold" x-text="steps[step-1]?.label || ''"></span>
                    </p>
                </div>
            </div>

            {{-- Stepper horizontal (clickeable) --}}
            <div class="mt-6 border-t pt-4">
                <div class="flex flex-wrap items-center gap-2">
                    <template x-for="(s, idx) in steps" :key="s.key">
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg border text-xs font-semibold cursor-pointer"
                                    @click="goToStep(idx+1)"
                                    :class="{
                                        'bg-gray-50 text-gray-600 border-gray-200': (idx+1) > step,
                                        'bg-[#9F2241]/10 text-[#691C32] border-[#9F2241]/20': (idx+1) === step,
                                        'bg-green-50 text-green-800 border-green-200': (idx+1) < step
                                     }">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[11px]"
                                      :class="{
                                        'bg-gray-200 text-gray-700': (idx+1) > step,
                                        'bg-[#691C32] text-white': (idx+1) === step,
                                        'bg-green-600 text-white': (idx+1) < step
                                      }"
                                      x-text="idx+1"></span>
                                <span x-text="s.label"></span>
                            </button>

                            <div class="text-gray-300" x-show="idx !== steps.length-1">—</div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Layout: formulario + preview --}}
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- FORM --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Mensajes --}}
                    <template x-if="savedMsg">
                        <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3" x-text="savedMsg"></div>
                    </template>
                    <template x-if="saveError">
                        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3" x-text="saveError"></div>
                    </template>

                    {{-- Paso 1: solo lectura --}}
                    <section x-show="step === 1" class="space-y-4" style="display:none;">
                        <div class="rounded-xl border border-gray-200 p-5 bg-gray-50">
                            <h4 class="font-semibold text-gray-800 mb-3">Datos generales (solo lectura)</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <div class="text-xs text-gray-500">Folio</div>
                                    <div class="font-mono">{{ $expediente->folio }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">Año (1ra parte)</div>
                                    <div class="font-semibold">{{ $expediente->anio_ejercicio }}</div>
                                </div>

                                <div class="md:col-span-2">
                                    <div class="text-xs text-gray-500">Partida genérica</div>
                                    <div class="font-semibold">{{ $partidaLabelLocal }}</div>
                                </div>

                                <div class="md:col-span-2">
                                    <div class="text-xs text-gray-500">Bien(es)</div>
                                    <div class="text-gray-800">{{ $bienesLabel ?: '—' }}</div>
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 mt-4">
                                Estos datos vienen de la 1ra parte. Puedes ajustar textos (override) en el siguiente paso.
                            </p>
                        </div>
                    </section>
                        {{-- PASO 2: Portada + Introducción --}}
                        <section x-show="step === 2" class="space-y-6" style="display:none;">
                            <div class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-4">Portada</h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1" for="titulo_documento">
                                            Título principal (editable)
                                        </label>
                                        <input id="titulo_documento" type="text"
                                               x-model="form.titulo_documento"
                                               class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1" for="subtitulo_documento">
                                            Subtítulo (editable)
                                        </label>
                                        <input id="subtitulo_documento" type="text"
                                               x-model="form.subtitulo_documento"
                                               class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1" for="fasp_texto">
                                            Texto FASP (editable)
                                        </label>
                                        <input id="fasp_texto" type="text"
                                               x-model="form.fasp_texto"
                                               class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1" for="ejercicio_fiscal_label">
                                            Etiqueta “Ejercicio fiscal” (editable)
                                        </label>
                                        <input id="ejercicio_fiscal_label" type="text"
                                               x-model="form.ejercicio_fiscal_label"
                                               class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">

                                        <label class="block text-xs font-semibold text-gray-700 mb-1 mt-3" for="anio_override">
                                            Año (editable)
                                        </label>
                                        <input id="anio_override" type="number" min="2000" max="2100"
                                               x-model.number="form.anio_override"
                                               class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">

                                        <p class="text-[11px] text-gray-500 mt-1">
                                            Default (1ra parte): <span class="font-semibold" x-text="form.anio_base"></span>
                                            · Si lo dejas vacío, se usa el default.
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Logo</label>
                                        <div class="flex items-center gap-3">
                                            <img :src="form.logo_url" alt="Logo" class="h-12 w-auto object-contain border rounded bg-white p-1">
                                            <div class="text-[11px] text-gray-500">
                                                Ruta: <code class="font-mono">public/images/LogoExpediente.png</code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-4">Estructura (override opcional)</h4>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Eje (default)</div>
                                        <div class="text-sm italic text-gray-700" x-text="form.eje_default"></div>
                                        <input type="text" x-model="form.eje_override"
                                               placeholder="Override (opcional)"
                                               class="mt-2 w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                                    </div>

                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Programa (default)</div>
                                        <div class="text-sm italic text-gray-700" x-text="form.programa_default"></div>
                                        <input type="text" x-model="form.programa_override"
                                               placeholder="Override (opcional)"
                                               class="mt-2 w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                                    </div>

                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Subprograma (default)</div>
                                        <div class="text-sm italic text-gray-700" x-text="form.subprograma_default"></div>
                                        <input type="text" x-model="form.subprograma_override"
                                               placeholder="Override (opcional)"
                                               class="mt-2 w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-3">1. Introducción</h4>
                                <textarea x-model="form.introduccion" rows="10"
                                          class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                          placeholder="Redacta la introducción..."></textarea>
                            </div>
                        </section>

                        {{-- PASO 3: Marco legal --}}
                        <section x-show="step === 3" class="space-y-4" style="display:none;">
                            <div class="rounded-xl border border-gray-200 p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <h4 class="font-semibold text-gray-800">2. Marco legal</h4>
                                    <button type="button"
                                            class="inline-flex items-center px-3 py-2 rounded-md bg-[#691C32] text-white text-sm font-semibold hover:bg-[#4e1324]"
                                            @click="addBloque()">
                                        + Agregar título
                                    </button>
                                </div>

                                <p class="text-xs text-gray-500 mt-2">
                                    Estructura: <span class="font-semibold">Título</span> → Artículos → (Opcional) Incisos romanos (I., II., VII., etc.).
                                </p>

                                <template x-if="form.marco_legal.length === 0">
                                    <div class="mt-4 text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        Aún no has agregado títulos. Haz clic en <span class="font-semibold">“Agregar título”</span>.
                                    </div>
                                </template>

                                <div class="mt-4 space-y-4">
                                    <template x-for="(bloque, i) in form.marco_legal" :key="i">
                                        <div class="border border-gray-200 rounded-xl p-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex-1">
                                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                                        Título (negrita)
                                                    </label>
                                                    <input type="text" x-model="bloque.titulo"
                                                           class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                           placeholder="Ej: Constitución Política">
                                                </div>

                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 rounded-md border border-red-200 bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100"
                                                        @click="removeBloque(i)">
                                                    Eliminar
                                                </button>
                                            </div>

                                            <div class="mt-4 flex items-center justify-between">
                                                <div class="text-xs text-gray-600 font-semibold">Artículos</div>
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50"
                                                        @click="addArticulo(i)">
                                                    + Agregar artículo
                                                </button>
                                            </div>

                                            <div class="mt-3 space-y-3">
                                                <template x-for="(a, j) in bloque.articulos" :key="j">
                                                    <div class="border border-gray-100 rounded-lg p-3 bg-gray-50">
                                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                                            <div class="md:col-span-4">
                                                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                                                    Artículo (negrita)
                                                                </label>
                                                                <input type="text" x-model="a.articulo"
                                                                       class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                                       placeholder="Ej: Artículo 134">
                                                            </div>
                                                            <div class="md:col-span-7">
                                                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                                                    Descripción (sin negrita)
                                                                </label>
                                                                <textarea x-model="a.descripcion" rows="2"
                                                                          class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                                          placeholder="Texto del artículo..."></textarea>
                                                            </div>
                                                            <div class="md:col-span-1 flex items-end">
                                                                <button type="button"
                                                                        class="w-full inline-flex items-center justify-center px-2 py-2 rounded-md border border-red-200 bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100"
                                                                        @click="removeArticulo(i, j)">
                                                                    ✕
                                                                </button>
                                                            </div>
                                                        </div>

                                                        {{-- Incisos romanos --}}
                                                        <div class="mt-3 flex items-center justify-between">
                                                            <div class="text-[11px] text-gray-600 font-semibold">
                                                                Incisos (romanos) — opcional
                                                            </div>
                                                            <button type="button"
                                                                    class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-200 bg-white text-[11px] font-semibold text-gray-700 hover:bg-gray-50"
                                                                    @click="addInciso(i, j)">
                                                                + Agregar inciso
                                                            </button>
                                                        </div>

                                                        <div class="mt-2 space-y-2" x-show="(a.incisos && a.incisos.length)">
                                                            <template x-for="(inc, k) in (a.incisos || [])" :key="k">
                                                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                                                    <div class="md:col-span-3">
                                                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                                                            Romano (negrita)
                                                                        </label>
                                                                        <input type="text" x-model="inc.romano"
                                                                               class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                                               placeholder="Ej: VII">
                                                                    </div>
                                                                    <div class="md:col-span-8">
                                                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">
                                                                            Descripción (sin negrita)
                                                                        </label>
                                                                        <textarea x-model="inc.descripcion" rows="2"
                                                                                  class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                                                  placeholder="Descripción del inciso..."></textarea>
                                                                    </div>
                                                                    <div class="md:col-span-1 flex items-end">
                                                                        <button type="button"
                                                                                class="w-full inline-flex items-center justify-center px-2 py-2 rounded-md border border-red-200 bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100"
                                                                                @click="removeInciso(i, j, k)">
                                                                            ✕
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </section>

                        {{-- Paso 4: 3..5 --}}
                        <section x-show="step === 4" class="space-y-4" style="display:none;">
                            <div class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-3">3. Objeto</h4>
                                <textarea x-model="form.objeto" rows="6"
                                          class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"></textarea>
                            </div>
                            <div class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-3">4. Alcance</h4>
                                <textarea x-model="form.alcance" rows="6"
                                          class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"></textarea>
                            </div>
                            <div class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-3">5. Justificación</h4>
                                <textarea x-model="form.justificacion" rows="8"
                                          class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"></textarea>
                            </div>
                        </section>

                        {{-- Paso 5: TABLAS 6/7/8 --}}
                        <section x-show="step === 5" class="space-y-4" style="display:none;">

                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button"
                                        class="px-3 py-2 rounded-md text-sm font-semibold border"
                                        :class="tablasTab==='t6' ? 'bg-[#691C32] text-white border-[#691C32]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                        @click="tablasTab='t6'">
                                    Tabla 6 · Estructura programática
                                </button>

                                <button type="button"
                                        class="px-3 py-2 rounded-md text-sm font-semibold border"
                                        :class="tablasTab==='t7' ? 'bg-[#691C32] text-white border-[#691C32]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                        @click="tablasTab='t7'">
                                    Tabla 7 · Especificaciones técnicas
                                </button>

                                <button type="button"
                                        class="px-3 py-2 rounded-md text-sm font-semibold border"
                                        :class="tablasTab==='t8' ? 'bg-[#691C32] text-white border-[#691C32]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                        @click="tablasTab='t8'">
                                    Tabla 8 · Entregables
                                </button>
                            </div>

                            {{-- TABLA 6 --}}
                            <div x-show="tablasTab==='t6'" style="display:none;" class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-3">6. Estructura Programática (transcripción presupuestal)</h4>

                                <div class="overflow-auto">
                                    <table class="min-w-full text-sm border border-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr class="text-left">
                                                <th class="px-3 py-2 border">Programa</th>
                                                <th class="px-3 py-2 border">Subprograma</th>
                                                <th class="px-3 py-2 border">Partida específica / Bien o servicio</th>
                                                <th class="px-3 py-2 border">Costo del bien o servicio (con IVA)</th>
                                                <th class="px-3 py-2 border">Unidad de Medida</th>
                                                <th class="px-3 py-2 border">Metas (Cantidad)</th>
                                                <th class="px-3 py-2 border">Aportación (Federal/Estatal)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(r, i) in form.tabla6" :key="r.id || ('t6'+i)">
                                                <tr>
                                                    <td class="px-3 py-2 border" x-text="r.programa"></td>
                                                    <td class="px-3 py-2 border" x-text="r.subprograma"></td>
                                                    <td class="px-3 py-2 border" x-text="r.partida_bien_servicio"></td>
                                                    <td class="px-3 py-2 border">
                                                        <span class="font-mono" x-text="(r.costo ?? 0).toLocaleString('es-MX', {style:'currency', currency:'MXN'})"></span>
                                                    </td>
                                                    <td class="px-3 py-2 border">
                                                        <input type="text" x-model="r.unidad_medida"
                                                               class="w-40 rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                               placeholder="Ej. Pieza">
                                                    </td>
                                                    <td class="px-3 py-2 border">
                                                        <input type="number" min="0" step="1" x-model.number="r.meta_cantidad"
                                                               class="w-28 rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                               placeholder="0">
                                                    </td>
                                                    <td class="px-3 py-2 border">
                                                        <select x-model="r.aportacion"
                                                                class="w-40 rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                                                            <option value="">—</option>
                                                            <option value="Federal">Federal</option>
                                                            <option value="Estatal">Estatal</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                <p class="text-xs text-gray-500 mt-3">
                                    * “Costo del bien o servicio” se actualiza automático desde Tabla 7 (Total con IVA).
                                </p>
                            </div>

                            {{-- TABLA 7 --}}
                            <div x-show="tablasTab==='t7'" style="display:none;" class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-3">7. Especificaciones Técnicas</h4>

                                <div class="space-y-4">
                                    <template x-for="(r, i) in form.tabla7" :key="r.id || ('t7'+i)">
                                        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                                <div class="md:col-span-3">
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Partida</label>
                                                    <input type="text" x-model="r.partida" disabled
                                                           class="w-full rounded-md border-gray-300 bg-gray-100">
                                                </div>

                                                <div class="md:col-span-9">
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Título (se usa también en Tabla 8)</label>
                                                    <input type="text" x-model="r.titulo_producto"
                                                           class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                           placeholder="Ej. COMPUTADORA DE ESCRITORIO">
                                                </div>

                                                <div class="md:col-span-12">
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-2">
                                                        Descripción Técnica (características mínimas)
                                                    </label>

                                                    <div class="flex flex-wrap gap-2 mb-2">
                                                        <button type="button"
                                                                class="px-3 py-1.5 rounded-md border border-gray-200 bg-white text-[11px] font-semibold hover:bg-gray-50"
                                                                @click="addTechItem(i,'subtitulo')">
                                                            + Subtítulo
                                                        </button>
                                                        <button type="button"
                                                                class="px-3 py-1.5 rounded-md border border-gray-200 bg-white text-[11px] font-semibold hover:bg-gray-50"
                                                                @click="addTechItem(i,'texto')">
                                                            + Texto
                                                        </button>
                                                    </div>

                                                    <div class="space-y-2">
                                                        <template x-for="(it, k) in (Array.isArray(r.descripcion_tecnica) ? r.descripcion_tecnica : [])" :key="k">
                                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-start">
                                                                <div class="md:col-span-3">
                                                                    <select x-model="it.tipo"
                                                                            class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32] text-sm">
                                                                        <option value="subtitulo">Subtítulo (negrita)</option>
                                                                        <option value="texto">Texto (normal)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="md:col-span-8">
                                                                    <textarea x-model="it.texto" rows="2"
                                                                              class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                                              placeholder="Escribe aquí..."></textarea>
                                                                </div>
                                                                <div class="md:col-span-1">
                                                                    <button type="button"
                                                                            class="w-full px-2 py-2 rounded-md border border-red-200 bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100"
                                                                            @click="removeTechItem(i,k)">
                                                                        ✕
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- Cantidad / UM (ahora SÍ se muestran porque vienen desde controller) --}}
                                                <div class="md:col-span-3">
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cantidad (desde Tabla 6)</label>
                                                    <input type="number" x-model.number="r.cantidad" disabled
                                                           class="w-full rounded-md border-gray-300 bg-gray-100">
                                                </div>

                                                <div class="md:col-span-3">
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Unidad de Medida (desde Tabla 6)</label>
                                                    <input type="text" x-model="r.unidad_medida" disabled
                                                           class="w-full rounded-md border-gray-300 bg-gray-100">
                                                </div>

                                                <div class="md:col-span-3">
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Precio Unitario</label>
                                                    <input type="number" min="0" step="0.01" x-model.number="r.precio_unitario"
                                                           class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]">
                                                </div>

                                                <div class="md:col-span-3">
                                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Importe sin IVA</label>
                                                    <div class="w-full rounded-md border border-gray-200 bg-white px-3 py-2 font-mono text-sm">
                                                        <span x-text="t7RowImporte(r).toLocaleString('es-MX', {style:'currency', currency:'MXN'})"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- Totales (3 filas como tu formato) --}}
                                <div class="mt-4 border-t pt-4">
                                    <div class="max-w-md ml-auto">
                                        <div class="grid grid-cols-2 text-sm border border-gray-200">
                                            <div class="px-3 py-2 border-b border-r font-semibold">Subtotal</div>
                                            <div class="px-3 py-2 border-b font-mono" x-text="t7Subtotal().toLocaleString('es-MX', {style:'currency', currency:'MXN'})"></div>

                                            <div class="px-3 py-2 border-b border-r font-semibold">IVA (16%)</div>
                                            <div class="px-3 py-2 border-b font-mono" x-text="t7IVA().toLocaleString('es-MX', {style:'currency', currency:'MXN'})"></div>

                                            <div class="px-3 py-2 border-r font-semibold">Total</div>
                                            <div class="px-3 py-2 font-mono font-semibold" x-text="t7Total().toLocaleString('es-MX', {style:'currency', currency:'MXN'})"></div>
                                        </div>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-3">
                                        * Al guardar Tabla 7, el sistema actualiza Tabla 6: “Costo (con IVA)” por bien.
                                    </p>
                                </div>
                            </div>

                            {{-- TABLA 8 --}}
                            <div x-show="tablasTab==='t8'" style="display:none;" class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-3">8. Entregables del Proyecto de Inversión</h4>

                                {{-- Tabla: Núm / Descripción / Cantidad (VA ARRIBA) --}}
                                <div class="overflow-auto">
                                    <table class="min-w-full text-sm border border-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr class="text-left">
                                                <th class="px-3 py-2 border">Núm.</th>
                                                <th class="px-3 py-2 border">Descripción</th>
                                                <th class="px-3 py-2 border">Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(r, i) in form.tabla8" :key="r.id || ('t8'+i)">
                                                <tr>
                                                    <td class="px-3 py-2 border font-mono" x-text="r.num ?? r.orden"></td>
                                                    <td class="px-3 py-2 border" x-text="r.descripcion"></td>
                                                    <td class="px-3 py-2 border font-mono" x-text="r.cantidad ?? 0"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Campos globales grandes (COMO secciones 9–20) --}}
                                <div class="mt-5 space-y-4">

                                    {{-- Fecha de Entrega --}}
                                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-[#691C32]">
                                                1
                                            </div>
                                            <div class="font-semibold text-gray-800">Fecha de Entrega (texto)</div>
                                        </div>

                                        <div class="mt-3">
                                            <textarea
                                                x-model="form.tabla8_fecha_entrega"
                                                rows="3"
                                                class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32] resize-y"
                                                placeholder="Ej. Dentro de los 30 días hábiles a partir de la firma del contrato, en un horario de 09:00 a 15:00 horas"
                                                @input="autoGrow($event.target)"
                                                x-init="autoGrow($el)"
                                            ></textarea>
                                        </div>
                                    </div>

                                    {{-- Responsable --}}
                                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-[#691C32]">
                                                2
                                            </div>
                                            <div class="font-semibold text-gray-800">Responsable de Validar el Entregable (texto)</div>
                                        </div>

                                        <div class="mt-3">
                                            <textarea
                                                x-model="form.tabla8_responsable_validar"
                                                rows="3"
                                                class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32] resize-y"
                                                placeholder="Ej. Jefe de la Unidad... / Coordinador General..."
                                                @input="autoGrow($event.target)"
                                                x-init="autoGrow($el)"
                                            ></textarea>
                                        </div>
                                    </div>

                                    {{-- Lugar --}}
                                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-[#691C32]">
                                                3
                                            </div>
                                            <div class="font-semibold text-gray-800">Lugar de Entrega (texto)</div>
                                        </div>

                                        <div class="mt-3">
                                            <textarea
                                                x-model="form.tabla8_lugar_entrega"
                                                rows="3"
                                                class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32] resize-y"
                                                placeholder="Ej. Oficinas centrales de la XXX"
                                                @input="autoGrow($event.target)"
                                                x-init="autoGrow($el)"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        * Estos 3 campos se imprimen una sola vez en el PDF y se “extienden” como en el formato oficial.
                                    </p>
                                </div>
                            </div>
                        </section>

                        {{-- Paso 6: 9..20 --}}
                        <section x-show="step === 6" class="space-y-4" style="display:none;">
                            <div class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-4">Secciones 9 a 20</h4>

                                <div class="space-y-4">
                                    @php
                                        $sec = [
                                            9  => 'Muestras',
                                            10 => 'Recursos Humanos',
                                            11 => 'Soporte Técnico',
                                            12 => 'Mantenimiento',
                                            13 => 'Capacitación, Actualización y Puesta en Marcha',
                                            14 => 'Vigencia',
                                            15 => 'Criterio de Evaluación',
                                            16 => 'Indicador de Medición',
                                            17 => 'Forma de Pago',
                                            18 => 'Garantías',
                                            19 => 'Formato para que el proveedor presente sus Propuestas Técnicas y Económicas',
                                            20 => 'Documentos Anexos',
                                        ];
                                    @endphp

                                    {{-- 9..19 como textareas normales --}}
                                    @foreach($sec as $num => $titulo)
                                        @if($num !== 20)
                                            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-[#691C32]">
                                                        {{ $num }}
                                                    </div>
                                                    <div class="font-semibold text-gray-800">{{ $titulo }}</div>
                                                </div>
                                                <div class="mt-3">
                                                    <textarea
                                                        x-model="form.no_aplica_{{ $num }}"
                                                        rows="{{ in_array($num,[17,18,19]) ? 6 : 3 }}"
                                                        class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                    ></textarea>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- 20 Documentos anexos --}}
                                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-[#691C32]">
                                                20
                                            </div>
                                            <div class="font-semibold text-gray-800">Documentos Anexos</div>
                                        </div>

                                        <p class="text-xs text-gray-600 mt-2">
                                            Captura uno por línea. En el PDF: <span class="font-semibold">“Anexo X.”</span> va en negrita y lo demás normal (Arial Narrow 12).
                                        </p>

                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <textarea x-model="form.no_aplica_20" rows="6"
                                                          class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"></textarea>
                                            </div>

                                            {{-- Preview rápido del formato (solo visual) --}}
                                            <div class="rounded-lg border border-gray-200 bg-white p-3">
                                                <div class="text-xs text-gray-500 mb-2">Vista previa (solo visual)</div>
                                                <div class="space-y-1 text-sm" style="font-family: 'Arial Narrow', Arial, sans-serif;">
                                                    <template x-for="(it, i) in anexosParsed()" :key="'an'+i">
                                                        <div>
                                                            <span class="font-semibold" x-text="it.head"></span>
                                                            <span x-text="it.tail ? (' ' + it.tail) : ''"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>

                        {{-- Paso 7: 21 Validación (tabla firmas como imagen) --}}
                        <section x-show="step === 7" class="space-y-4" style="display:none;">
                            <div class="rounded-xl border border-gray-200 p-5">
                                <h4 class="font-semibold text-gray-800 mb-4">21. Validación del Expediente Técnico</h4>

                                <div class="border border-gray-300 rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 text-center font-semibold py-2">
                                        RESPONSABLES DEL PROYECTO
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2">
                                        {{-- Responsable del Subprograma --}}
                                        <div class="border-r border-gray-300 p-4 min-h-[260px]">
                                            <div class="text-center font-semibold mb-3">Responsable del Subprograma</div>

                                            <div class="mt-20 text-center">
                                                <div class="font-semibold">Nombre</div>
                                                <input type="text" x-model="form.responsable_subprograma_nombre"
                                                       class="mt-2 w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32] text-center"
                                                       placeholder="Nombre completo">
                                            </div>

                                            <div class="mt-4 text-center">
                                                <div class="font-semibold">Cargo</div>
                                                <input type="text" x-model="form.responsable_subprograma_cargo"
                                                       class="mt-2 w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32] text-center"
                                                       placeholder="Cargo">
                                            </div>
                                        </div>

                                        {{-- Titular de la Dependencia --}}
                                        <div class="p-4 min-h-[260px]">
                                            <div class="text-center font-semibold mb-3">Titular de la Dependencia</div>

                                            <div class="mt-20 text-center">
                                                <div class="font-semibold">Nombre</div>
                                                <input type="text" x-model="form.titular_dependencia_nombre"
                                                       class="mt-2 w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32] text-center"
                                                       placeholder="Nombre completo">
                                            </div>

                                            <div class="mt-4 text-center">
                                                <div class="font-semibold">Cargo</div>
                                                <input type="text" x-model="form.titular_dependencia_cargo"
                                                       class="mt-2 w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32] text-center"
                                                       placeholder="Cargo">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 mt-3">
                                    El espacio central queda libre para firma en el formato final (PDF).
                                </p>
                            </div>
                        </section>

                        {{-- Botonera --}}
                        <div class="flex items-center justify-between pt-2">
                            <button type="button"
                                    class="inline-flex items-center px-4 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                                    :disabled="step === 1 || saving"
                                    @click="prev()">
                                ← Anterior
                            </button>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                        class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 text-sm font-semibold hover:bg-gray-300 disabled:opacity-50"
                                        :disabled="saving"
                                        @click="saveSection().catch(()=>{})">
                                    <span x-show="!saving">Guardar sección</span>
                                    <span x-show="saving" style="display:none;">Guardando…</span>
                                </button>

                                <button type="button"
                                        class="inline-flex items-center px-4 py-2 bg-[#691C32] text-white text-sm font-semibold rounded-md hover:bg-[#4e1324] disabled:opacity-50"
                                        :disabled="saving"
                                        @click="next()">
                                    Siguiente →
                                </button>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500">
                            Tip: En el Paso 5 guarda por sub-tab (Tabla 6 / 7 / 8) antes de cambiar.
                        </p>
                    </div>

                {{-- PREVIEW --}}
                <div class="lg:col-span-4" x-show="previewOpen" style="display:none;">
                    <div class="sticky top-6">
                        <div class="rounded-2xl border border-gray-200 bg-white shadow p-4">
                            <div class="text-xs text-gray-500 mb-2">Vista previa (referencia del PDF)</div>

                            <div class="border border-gray-300 rounded-lg p-4 bg-white"
                                 style="font-family: 'Arial Narrow', Arial, sans-serif;">
                                <div class="flex items-center justify-between">
                                    <img :src="form.logo_url" alt="Logo" class="h-10 w-auto object-contain">
                                </div>

                                <div class="mt-4 font-bold uppercase text-center" style="font-size:34px;" x-text="form.titulo_documento"></div>
                                <div class="text-center uppercase" style="font-size:20px;" x-text="form.subtitulo_documento"></div>

                                <div class="mt-5" style="font-size:14px; font-weight:700;" x-text="form.partida_label"></div>
                                <div class="mt-1" style="font-size:12px;" x-text="form.bienes_label"></div>

                                <div class="mt-3 text-center" style="font-size:14px;" x-text="form.fasp_texto"></div>

                                <div class="mt-3 text-center" style="font-size:14px;">
                                    <span x-text="form.ejercicio_fiscal_label"></span>
                                    <span class="font-semibold" x-text="anioFinal()"></span>
                                </div>

                                <div class="mt-4 text-right">
                                    <div style="font-size:12px; font-weight:700;">Eje</div>
                                    <div style="font-style:italic; font-size:11px;" x-text="epsEje()"></div>

                                    <div class="mt-2" style="font-size:12px; font-weight:700;">Programa</div>
                                    <div style="font-style:italic; font-size:11px;" x-text="epsProg()"></div>

                                    <div class="mt-2" style="font-size:12px; font-weight:700;">Subprograma</div>
                                    <div style="font-style:italic; font-size:11px;" x-text="epsSub()"></div>
                                </div>

                                <div class="mt-5" style="font-size:14px; font-weight:700;">5. Tablas</div>
                                <div class="mt-1 text-gray-700" style="font-size:11px;">
                                    <div class="font-semibold">Totales Tabla 7</div>
                                    <div>Subtotal: <span class="font-mono" x-text="t7Subtotal().toLocaleString('es-MX', {style:'currency', currency:'MXN'})"></span></div>
                                    <div>IVA: <span class="font-mono" x-text="t7IVA().toLocaleString('es-MX', {style:'currency', currency:'MXN'})"></span></div>
                                    <div>Total: <span class="font-mono font-semibold" x-text="t7Total().toLocaleString('es-MX', {style:'currency', currency:'MXN'})"></span></div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-2">
                                <button type="button"
                                        class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                                        :disabled="saving"
                                        @click="saveAll().catch(()=>{})">
                                    Guardar expediente (borrador)
                                </button>

                                <button type="button"
                                        class="inline-flex items-center px-3 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700"
                                        disabled>
                                    Vista previa PDF
                                </button>

                                <button type="button"
                                        class="inline-flex items-center px-3 py-2 rounded-md bg-green-600 text-white text-sm font-semibold hover:bg-green-700"
                                        disabled>
                                    Enviar a revisión
                                </button>
                            </div>

                            <div class="mt-2 text-[11px] text-gray-500">
                                PDF/Envío se habilitan cuando metamos checklist + validación presupuesto.
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- grid --}}
        </div>
    </div>
</div>
</x-app-layout>
