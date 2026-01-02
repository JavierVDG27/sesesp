<?php

namespace App\Http\Controllers\Capturista;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\ExpedienteDetalle;
use App\Models\FaspCatalogo;
use App\Models\ExpedienteEstructuraProgramatica;
use App\Models\ExpedienteEspecificacion;
use App\Models\ExpedienteEntregable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpedienteSegundaParteController extends Controller
{
    public function edit(Expediente $expediente)
    {
        $user = auth()->user();
        abort_if($expediente->user_id !== $user->id, 403, 'No tienes permiso para ver este expediente.');

        if ($expediente->estatus === Expediente::ESTADO_APROBADO) {
            return redirect()->route('expedientes.index')->with('error', 'Expediente aprobado: no editable.');
        }

        // 1) Cargar / crear detalle con defaults (1 sola vez por expediente)
        $detalle = ExpedienteDetalle::query()->firstOrCreate(
            ['expediente_id' => $expediente->id],
            [
                'titulo_documento' => 'EXPEDIENTE TÉCNICO',
                'subtitulo_documento' => 'ADQUISICIÓN Y CONTRATACIÓN DE SERVICIOS',
                'fasp_texto' => 'FONDO DE APORTACIONES PARA LA SEGURIDAD PÚBLICA DE LOS ESTADOS Y DEL DISTRITO FEDERAL (FASP)',
                'ejercicio_fiscal_label' => 'EJERCICIO FISCAL AÑO',
                'logo_path' => 'images/LogoExpediente.png',

                // Defaults (editables)
                'no_aplica_9'  => 'No aplica.',
                'no_aplica_10' => 'No aplica.',
                'no_aplica_11' => 'No aplica.',
                'no_aplica_12' => 'No aplica.',
                'no_aplica_13' => 'No aplica.',
                'no_aplica_14' => 'No aplica.',
                'no_aplica_15' => 'No aplica.',
                'no_aplica_16' => 'No aplica.',

                // NUEVOS 17..20 (requiere migración)
                'no_aplica_17' => 'No aplica.',
                'no_aplica_18' => 'No aplica.',
                'no_aplica_19' => 'No aplica.',
                'no_aplica_20' => "Anexo 1. Cotización.\nAnexo 2. Anexo A Computadoras de Escritorio.\nAnexo 3. Anexo B Computadoras Portátiles.",

                'marco_legal_json' => json_encode([], JSON_UNESCAPED_UNICODE),
                'segunda_parte_completa' => false,
            ]
        );

        $expediente->setRelation('detalle', $detalle);

        // 2) Mapas del catálogo para render de nombres (tomados de la 1ra parte)
        $year = (int)$expediente->anio_ejercicio;
        $entidad = (string)$expediente->entidad;

        $eje = (string)$expediente->eje;
        $programa = (string)$expediente->programa;
        $subprograma = (string)$expediente->subprograma;
        $capitulo = (string)$expediente->capitulo;
        $concepto = (string)$expediente->concepto;
        $pg = (string)$expediente->partida_generica;

        // ===== Niveles 1-3 (subprograma con parents) =====
        $nivel3 = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->where('eje', $eje)
            ->where('programa', $programa)
            ->where('subprograma', $subprograma)
            ->with([
                'parent:id,year,entidad,nivel,parent_id,eje,programa,nombre',
                'parent.parent:id,year,entidad,nivel,eje,nombre'
            ])
            ->first();

        $mapNombresEje = [];
        $mapNombresPrograma = [];
        $mapNombresSubprograma = [];

        if ($nivel3) {
            $mapNombresSubprograma["{$eje}|{$programa}|{$subprograma}"] = [
                'id' => $nivel3->id,
                'nombre' => (string)($nivel3->nombre ?? ''),
            ];

            if ($nivel3->parent) {
                $mapNombresPrograma["{$eje}|{$programa}"] = (string)($nivel3->parent->nombre ?? '');
            }

            if ($nivel3->parent && $nivel3->parent->parent) {
                $mapNombresEje[$eje] = (string)($nivel3->parent->parent->nombre ?? '');
            }
        }

        // ===== Nivel 6 (partida genérica) =====
        $mapNombresPartidaGenerica = [];
        $rowPg = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 6)
            ->where('eje', $eje)
            ->where('programa', $programa)
            ->where('subprograma', $subprograma)
            ->where('capitulo', $capitulo)
            ->where('concepto', $concepto)
            ->where('partida_generica', $pg)
            ->first();

        if ($rowPg) {
            $mapNombresPartidaGenerica["{$eje}|{$programa}|{$subprograma}|{$capitulo}|{$concepto}|{$pg}"]
                = (string)($rowPg->nombre ?? '');
        }

        // ===== Niveles 7/8 (bienes) =====
        $mapNombresBien = [];
        $bienes = (array)($expediente->bienes ?? []);

        if (!empty($bienes)) {
            $rowsBien = FaspCatalogo::query()
                ->where('year', $year)
                ->where('entidad', $entidad)
                ->whereIn('nivel', [7, 8])
                ->where('eje', $eje)
                ->where('programa', $programa)
                ->where('subprograma', $subprograma)
                ->where('capitulo', $capitulo)
                ->where('concepto', $concepto)
                ->where('partida_generica', $pg)
                ->whereIn('bien', $bienes)
                ->get(['bien', 'nombre']);

            foreach ($rowsBien as $rb) {
                $bien = (string)$rb->bien;
                $mapNombresBien["{$eje}|{$programa}|{$subprograma}|{$capitulo}|{$concepto}|{$pg}|{$bien}"]
                    = (string)($rb->nombre ?? '');
            }
        }

        // ===== Labels para UI (default) =====
        $ejeKey = "{$expediente->eje}|{$expediente->programa}|{$expediente->subprograma}";
        $ejeNombre = $mapNombresEje[(string)$expediente->eje] ?? '';
        $programaNombre = $mapNombresPrograma["{$expediente->eje}|{$expediente->programa}"] ?? '';
        $subNombre = $mapNombresSubprograma[$ejeKey]['nombre'] ?? '';

        $ejeDefault = trim((string)$expediente->eje) . '. ' . trim($ejeNombre);
        $programaDefault = trim((string)$expediente->programa) . '. ' . trim($programaNombre);
        $subDefault = trim((string)$expediente->subprograma) . '. ' . trim($subNombre);

        $pgKey = "{$expediente->eje}|{$expediente->programa}|{$expediente->subprograma}|{$expediente->capitulo}|{$expediente->concepto}|{$expediente->partida_generica}";
        $pgNombre = $mapNombresPartidaGenerica[$pgKey] ?? '';
        $partidaLabel = trim((string)$expediente->partida_generica) . '. ' . trim((string)$pgNombre);

        // 3) Seed de Tablas 6/7/8 (crea filas por bien si no existen)
        $this->seedTablasPorBien($expediente, $programaDefault, $subDefault, $partidaLabel, $mapNombresBien);

        // 4) Cargar tablas para el wizard
        $t6 = $expediente->estructuraProgramatica()->get()->toArray();
        $t7 = $expediente->especificaciones()->get()->toArray();
        $t8 = $expediente->entregables()->get()->toArray();

        // === FIX: Asegurar que t7 recibe Cantidad/Unidad desde t6 y t8 recibe Cantidad desde t6 ===
        $mapT6 = [];
        foreach ($t6 as $r) {
            $ord = (int)($r['orden'] ?? 0);
            if ($ord <= 0) continue;
            $mapT6[$ord] = [
                'cantidad' => $r['meta_cantidad'] ?? null,
                'unidad_medida' => $r['unidad_medida'] ?? '',
            ];
        }

        foreach ($t7 as &$r) {
            $ord = (int)($r['orden'] ?? 0);
            if ($ord > 0 && isset($mapT6[$ord])) {
                $r['cantidad'] = $mapT6[$ord]['cantidad'] ?? 0;
                $r['unidad_medida'] = $mapT6[$ord]['unidad_medida'] ?? '';
            }
        }
        unset($r);

        foreach ($t8 as &$r) {
            $ord = (int)($r['orden'] ?? 0);
            if ($ord > 0 && isset($mapT6[$ord])) {
                $r['cantidad'] = $mapT6[$ord]['cantidad'] ?? 0;
            }
        }
        unset($r);

        return view('expedientes.segunda_parte.edit', compact(
            'expediente',
            'mapNombresEje',
            'mapNombresPrograma',
            'mapNombresSubprograma',
            'mapNombresPartidaGenerica',
            'mapNombresBien',
            't6',
            't7',
            't8',
            'partidaLabel',
            'programaDefault',
            'subDefault'
        ));
    }

    /**
     * Autosave por sección del wizard.
     */
    public function autosave(Request $request, Expediente $expediente, string $section)
    {
        $user = auth()->user();
        abort_if($expediente->user_id !== $user->id, 403, 'No tienes permiso.');

        if ($expediente->estatus === Expediente::ESTADO_APROBADO) {
            return response()->json(['ok' => false, 'message' => 'Expediente aprobado: no editable.'], 422);
        }

        $detalle = ExpedienteDetalle::query()->firstOrCreate(['expediente_id' => $expediente->id]);

        return DB::transaction(function () use ($request, $detalle, $section, $expediente) {

            if ($section === 'portada_intro') {
                $data = $request->validate([
                    'titulo_documento'        => ['nullable','string','max:255'],
                    'subtitulo_documento'     => ['nullable','string','max:255'],
                    'fasp_texto'              => ['nullable','string','max:800'],
                    'ejercicio_fiscal_label'  => ['nullable','string','max:255'],
                    'anio_override'           => ['nullable','integer','min:2000','max:2100'],
                    'eje_override'            => ['nullable','string','max:255'],
                    'programa_override'       => ['nullable','string','max:255'],
                    'subprograma_override'    => ['nullable','string','max:255'],
                    'introduccion'            => ['nullable','string'],
                ]);

                $detalle->fill($data);
                $detalle->save();

                return response()->json(['ok' => true, 'section' => $section]);
            }

            if ($section === 'marco_legal') {
                $data = $request->validate([
                    'marco_legal' => ['nullable','array'],
                    'marco_legal.*.titulo' => ['nullable','string','max:255'],
                    'marco_legal.*.articulos' => ['nullable','array'],
                    'marco_legal.*.articulos.*.articulo' => ['nullable','string','max:255'],
                    'marco_legal.*.articulos.*.descripcion' => ['nullable','string','max:5000'],
                    'marco_legal.*.articulos.*.incisos' => ['nullable','array'],
                    'marco_legal.*.articulos.*.incisos.*.romano' => ['nullable','string','max:20'],
                    'marco_legal.*.articulos.*.incisos.*.descripcion' => ['nullable','string','max:5000'],
                ]);

                $arr = $data['marco_legal'] ?? [];
                $detalle->marco_legal_json = json_encode($arr, JSON_UNESCAPED_UNICODE);
                $detalle->marco_legal = $this->marcoLegalPlano($arr);
                $detalle->save();

                return response()->json(['ok' => true, 'section' => $section]);
            }

            if ($section === 'seccion_3_5') {
                $data = $request->validate([
                    'objeto'        => ['nullable','string'],
                    'alcance'       => ['nullable','string'],
                    'justificacion' => ['nullable','string'],
                ]);

                $detalle->fill($data)->save();
                return response()->json(['ok' => true, 'section' => $section]);
            }

            if ($section === 'tablas_6') {
                $data = $request->validate([
                    'rows' => ['required','array'],
                    'rows.*.id' => ['nullable','integer'],
                    'rows.*.orden' => ['required','integer','min:1'],
                    'rows.*.unidad_medida' => ['nullable','string','max:255'],
                    'rows.*.meta_cantidad' => ['nullable','integer','min:0','max:1000000'],
                    'rows.*.aportacion' => ['nullable','string','max:40'],
                ]);

                foreach ($data['rows'] as $r) {
                    $row = ExpedienteEstructuraProgramatica::query()
                        ->where('expediente_id', $expediente->id)
                        ->where('orden', (int)$r['orden'])
                        ->first();

                    if (!$row && !empty($r['id'])) {
                        $row = ExpedienteEstructuraProgramatica::query()
                            ->where('expediente_id', $expediente->id)
                            ->where('id', (int)$r['id'])
                            ->first();
                    }

                    if (!$row) continue;

                    $row->unidad_medida = $r['unidad_medida'] ?? '';
                    $row->meta_cantidad = $r['meta_cantidad'] ?? null;
                    $row->aportacion = $r['aportacion'] ?? '';
                    $row->save();

                    // Sync cantidad/unidad hacia tabla 7 y 8
                    $this->syncCantidadUnidadDesdeTabla6($expediente->id, (int)$row->orden, $row->meta_cantidad, $row->unidad_medida);
                }

                return response()->json(['ok' => true, 'section' => $section]);
            }

            if ($section === 'tablas_7') {
                $data = $request->validate([
                    'rows' => ['required','array'],
                    'rows.*.id' => ['nullable','integer'],
                    'rows.*.orden' => ['required','integer','min:1'],
                    'rows.*.titulo_producto' => ['nullable','string','max:255'],
                    'rows.*.descripcion_tecnica' => ['nullable','array'],
                    'rows.*.descripcion_tecnica.*.tipo' => ['required_with:rows.*.descripcion_tecnica','in:subtitulo,texto'],
                    'rows.*.descripcion_tecnica.*.texto' => ['nullable','string','max:5000'],
                    'rows.*.precio_unitario' => ['nullable','numeric','min:0','max:999999999.99'],
                ]);

                foreach ($data['rows'] as $r) {
                    $orden = (int)$r['orden'];

                    $estructura = ExpedienteEstructuraProgramatica::query()
                        ->where('expediente_id', $expediente->id)
                        ->where('orden', $orden)
                        ->first();

                    $cantidad = (int)($estructura?->meta_cantidad ?? 0);
                    $unidad = (string)($estructura?->unidad_medida ?? '');

                    $espec = ExpedienteEspecificacion::query()
                        ->where('expediente_id', $expediente->id)
                        ->where('orden', $orden)
                        ->first();

                    if (!$espec && !empty($r['id'])) {
                        $espec = ExpedienteEspecificacion::query()
                            ->where('expediente_id', $expediente->id)
                            ->where('id', (int)$r['id'])
                            ->first();
                    }
                    if (!$espec) continue;

                    $precio = isset($r['precio_unitario']) && $r['precio_unitario'] !== '' ? (float)$r['precio_unitario'] : null;

                    $espec->titulo_producto = $r['titulo_producto'] ?? $espec->titulo_producto;
                    $espec->descripcion_tecnica = json_encode(($r['descripcion_tecnica'] ?? []), JSON_UNESCAPED_UNICODE);
                    $espec->cantidad = $cantidad;
                    $espec->unidad_medida = $unidad;
                    $espec->precio_unitario = $precio;
                    $espec->importe_sin_iva = ($precio !== null) ? round($precio * $cantidad, 2) : null;
                    $espec->save();

                    // Costo en tabla 6 = total con IVA del bien (1.16)
                    if ($estructura) {
                        $estructura->costo = ($espec->importe_sin_iva !== null) ? round(((float)$espec->importe_sin_iva) * 1.16, 2) : null;
                        $estructura->save();
                    }

                    // Sync entregables: descripcion = titulo, cantidad = cantidad
                    $this->syncEntregableDesdeTabla7($expediente->id, $orden, (string)$espec->titulo_producto, $cantidad);
                }

                return response()->json(['ok' => true, 'section' => $section]);
            }

            if ($section === 'tablas_8') {
                $data = $request->validate([
                    // campos globales (una sola vez)
                    'tabla8_fecha_entrega' => ['nullable','string'],
                    'tabla8_responsable_validar' => ['nullable','string'],
                    'tabla8_lugar_entrega' => ['nullable','string'],

                    // filas (solo para asegurar orden/ids, NO se captura fecha/responsable/lugar por fila)
                    'rows' => ['required','array'],
                    'rows.*.id' => ['nullable','integer'],
                    'rows.*.orden' => ['required','integer','min:1'],
                ]);

                // Guardar globales en detalle
                $detalle->tabla8_fecha_entrega = $data['tabla8_fecha_entrega'] ?? '';
                $detalle->tabla8_responsable_validar = $data['tabla8_responsable_validar'] ?? '';
                $detalle->tabla8_lugar_entrega = $data['tabla8_lugar_entrega'] ?? '';
                $detalle->save();

                // Asegurar que Tabla 8 tenga num/descripcion/cantidad actualizados (cantidad viene de Tabla 6)
                foreach ($data['rows'] as $r) {
                    $orden = (int)$r['orden'];

                    $ent = ExpedienteEntregable::query()
                        ->where('expediente_id', $expediente->id)
                        ->where('orden', $orden)
                        ->first();

                    if ($ent) {
                        $ent->num = $orden;

                        // cantidad desde tabla 6 (estructura)
                        $estructura = \App\Models\ExpedienteEstructuraProgramatica::query()
                            ->where('expediente_id', $expediente->id)
                            ->where('orden', $orden)
                            ->first();

                        $ent->cantidad = (int)($estructura?->meta_cantidad ?? 0);
                        $ent->save();
                    }
                }

                return response()->json(['ok' => true, 'section' => $section]);
            }


            // =========================
            // PASO: 9..20 (textos)
            // =========================
            if ($section === 'seccion_9_20') {
                $data = $request->validate([
                    'no_aplica_9'  => ['nullable','string'],
                    'no_aplica_10' => ['nullable','string'],
                    'no_aplica_11' => ['nullable','string'],
                    'no_aplica_12' => ['nullable','string'],
                    'no_aplica_13' => ['nullable','string'],
                    'no_aplica_14' => ['nullable','string'],
                    'no_aplica_15' => ['nullable','string'],
                    'no_aplica_16' => ['nullable','string'],
                    'no_aplica_17' => ['nullable','string'],
                    'no_aplica_18' => ['nullable','string'],
                    'no_aplica_19' => ['nullable','string'],
                    'no_aplica_20' => ['nullable','string'],
                ]);

                $detalle->fill($data)->save();
                return response()->json(['ok' => true, 'section' => $section]);
            }

            // =========================
            // PASO: 21 (firmas)
            // =========================
            if ($section === 'seccion_17_21') {
                $data = $request->validate([
                    'responsable_subprograma_nombre' => ['nullable','string','max:255'],
                    'responsable_subprograma_cargo'  => ['nullable','string','max:255'],
                    'titular_dependencia_nombre'     => ['nullable','string','max:255'],
                    'titular_dependencia_cargo'      => ['nullable','string','max:255'],
                ]);

                $detalle->fill($data)->save();
                return response()->json(['ok' => true, 'section' => $section]);
            }

            return response()->json([
                'ok' => false,
                'message' => 'Sección no reconocida: '.$section,
            ], 422);
        });
    }

    // =========================
    // Helpers de sincronización
    // =========================
    private function syncCantidadUnidadDesdeTabla6(int $expedienteId, int $orden, ?int $cantidad, ?string $unidad)
    {
        $cantidadInt = (int)($cantidad ?? 0);
        $unidadStr = (string)($unidad ?? '');

        $espec = ExpedienteEspecificacion::query()
            ->where('expediente_id', $expedienteId)
            ->where('orden', $orden)
            ->first();

        if ($espec) {
            $espec->cantidad = $cantidadInt;
            $espec->unidad_medida = $unidadStr;

            if ($espec->precio_unitario !== null) {
                $espec->importe_sin_iva = round(((float)$espec->precio_unitario) * $cantidadInt, 2);
            }
            $espec->save();

            // Actualiza costo en tabla 6 (si hay importe)
            $estructura = ExpedienteEstructuraProgramatica::query()
                ->where('expediente_id', $expedienteId)
                ->where('orden', $orden)
                ->first();

            if ($estructura) {
                $estructura->costo = ($espec->importe_sin_iva !== null) ? round(((float)$espec->importe_sin_iva) * 1.16, 2) : null;
                $estructura->save();
            }

            $this->syncEntregableDesdeTabla7($expedienteId, $orden, (string)$espec->titulo_producto, $cantidadInt);
        }
    }

    private function syncEntregableDesdeTabla7(int $expedienteId, int $orden, string $titulo, int $cantidad)
    {
        $ent = ExpedienteEntregable::query()
            ->where('expediente_id', $expedienteId)
            ->where('orden', $orden)
            ->first();

        if ($ent) {
            $ent->num = $orden;
            $ent->descripcion = $titulo;
            $ent->cantidad = $cantidad;
            $ent->save();
        }
    }

    /**
     * Seed automático: crea filas por bien en tablas 6/7/8 si no existen.
     */
    private function seedTablasPorBien(Expediente $expediente, string $programaLabel, string $subprogramaLabel, string $partidaLabel, array $mapNombresBien)
    {
        $bienes = array_values((array)($expediente->bienes ?? []));
        if (count($bienes) === 0) return;

        $eje = (string)$expediente->eje;
        $programa = (string)$expediente->programa;
        $subprograma = (string)$expediente->subprograma;
        $capitulo = (string)$expediente->capitulo;
        $concepto = (string)$expediente->concepto;
        $pg = (string)$expediente->partida_generica;

        foreach ($bienes as $idx => $bienCode) {
            $orden = $idx + 1;

            $bienKey = "{$eje}|{$programa}|{$subprograma}|{$capitulo}|{$concepto}|{$pg}|{$bienCode}";
            $bienNombre = $mapNombresBien[$bienKey] ?? '';
            $bienLabel = trim((string)$bienCode) . '. ' . trim((string)$bienNombre);

            ExpedienteEstructuraProgramatica::query()->firstOrCreate(
                ['expediente_id' => $expediente->id, 'orden' => $orden],
                [
                    'programa' => $programaLabel,
                    'subprograma' => $subprogramaLabel,
                    'partida_bien_servicio' => $partidaLabel.' / '.$bienLabel,
                    'costo' => null,
                    'unidad_medida' => '',
                    'meta_cantidad' => null,
                    'aportacion' => '',
                ]
            );

            $titulo = mb_strtoupper(trim($bienNombre ?: (string)$bienCode));
            ExpedienteEspecificacion::query()->firstOrCreate(
                ['expediente_id' => $expediente->id, 'orden' => $orden],
                [
                    'partida' => (string)$expediente->partida_generica,
                    'titulo_producto' => $titulo,
                    'descripcion_tecnica' => json_encode([], JSON_UNESCAPED_UNICODE),
                    'cantidad' => 0,
                    'unidad_medida' => '',
                    'precio_unitario' => null,
                    'importe_sin_iva' => null,
                ]
            );

            ExpedienteEntregable::query()->firstOrCreate(
                ['expediente_id' => $expediente->id, 'orden' => $orden],
                [
                    'num' => $orden,
                    'descripcion' => $titulo,
                    'cantidad' => 0,
                    'fecha_entrega' => '',
                    'responsable_validar' => '',
                    'lugar_entrega' => '',
                ]
            );
        }
    }

    private function marcoLegalPlano(array $arr): string
    {
        $out = [];

        foreach ($arr as $bloque) {
            $titulo = trim((string)($bloque['titulo'] ?? ''));
            if ($titulo !== '') $out[] = $titulo;

            $arts = (array)($bloque['articulos'] ?? []);
            foreach ($arts as $a) {
                $art = trim((string)($a['articulo'] ?? ''));
                $desc = trim((string)($a['descripcion'] ?? ''));

                if ($art === '' && $desc === '') continue;

                if ($art !== '' && $desc !== '') $out[] = "{$art}. {$desc}";
                elseif ($art !== '') $out[] = $art;
                else $out[] = $desc;

                $incisos = (array)($a['incisos'] ?? []);
                foreach ($incisos as $inc) {
                    $rom = trim((string)($inc['romano'] ?? ''));
                    $d   = trim((string)($inc['descripcion'] ?? ''));
                    if ($rom === '' && $d === '') continue;

                    if ($rom !== '' && $d !== '') $out[] = "{$rom}. {$d}";
                    elseif ($rom !== '') $out[] = $rom;
                    else $out[] = $d;
                }
            }

            $out[] = '';
        }

        return trim(implode("\n", $out));
    }

    public function enviarRevision(Request $request, Expediente $expediente)
    {
        abort(501, 'Pendiente por implementar (checklist + validación presupuesto).');
    }
}
