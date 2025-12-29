<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Presupuesto;
use App\Models\HistorialModificacion;
use App\Models\FaspAsignacionInstitucion;
use App\Models\FaspCatalogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Lista de instituciones
use App\Models\Institucion;

class ExpedienteController extends Controller
{
    /**
     * Listado de expedientes del capturista.
     */
    public function index()
    {
        $user = auth()->user();

        $expedientes = Expediente::query()
            ->where('user_id', auth()->id())
            ->with('areaEjecutora:id,nombre,siglas')
            ->latest('updated_at')
            ->paginate(10);

        return view('expedientes.index', compact('expedientes'));
    }

    /**
     * Formulario crear expediente (solo con opciones asignadas a la institución del capturista).
     * Aquí armamos:
     * - Asignaciones EPS
     * - Maps del catálogo (cap/con/pg/bien) por EPS
     * - Selector rápido:
     *   - proyectosPorEPS (PG nivel 6 con cap+con)
     *   - bienesPorProyecto (Bienes nivel 7/8 por cap+con+pg)
     */
    public function create()
    {
        $user = auth()->user();

        $year = now()->year;
        $entidad = '8300';

        if (!$user->institucion_id) {
            return redirect()
                ->route('expedientes.index')
                ->with('error', 'Tu usuario no tiene institución asignada. Pide al admin asignarte una institución.');
        }

        $asig = $this->asignacionesInstitucion($user->institucion_id, $year, $entidad);

        if ($asig->isEmpty()) {
            return redirect()
                ->route('expedientes.index')
                ->with('error', 'Tu institución no tiene subprogramas FASP asignados para este año. Pide al validador que asigne.');
        }

        $pad2 = fn($v) => str_pad((string)$v, 2, '0', STR_PAD_LEFT);

        // === 1) Listado/resumen asignaciones (para mostrar arriba) ===
        $resumenAsignaciones = $asig
            ->groupBy(fn($a) => $pad2($a->eje) . '|' . $pad2($a->programa))
            ->map(function ($items) use ($pad2) {
                $first = $items->first();
                return [
                    'eje' => $pad2($first->eje),
                    'programa' => $pad2($first->programa),
                    'subprogramas' => $items->pluck('subprograma')
                        ->filter()
                        ->map(fn($s) => $pad2($s))
                        ->unique()
                        ->sort()
                        ->values(),
                ];
            })
            ->values()
            ->sortBy(fn($x) => $x['eje'].'|'.$x['programa'])
            ->values();

        // === 2) Selects dependientes del 1er apartado (Eje->Programa->Subprograma) ===
        $pad2 = fn($v) => str_pad((string)$v, 2, '0', STR_PAD_LEFT);

            // 2A) Traer del catálogo los EPS (nivel 3) correspondientes a las asignaciones del usuario
            $epsAsignados = FaspCatalogo::query()
                ->where('year', $year)
                ->where('entidad', $entidad)
                ->where('nivel', 3)
                ->where(function ($w) use ($asig) {
                    foreach ($asig as $a) {
                        $w->orWhere(function ($q) use ($a) {
                            $q->where('eje', $a->eje)
                            ->where('programa', $a->programa)      // si tu asignación sí trae programa, perfecto
                            ->where('subprograma', $a->subprograma);
                        });
                    }
                })
                ->get(['eje','programa','subprograma']);

            // 2B) Normalizar
            $epsAsignados = $epsAsignados->map(function ($x) use ($pad2) {
                return (object)[
                    'eje' => $pad2($x->eje),
                    'programa' => $pad2($x->programa),
                    'subprograma' => $pad2($x->subprograma),
                ];
            });

            // 2C) Construir selects dependientes
            $ejesPermitidos = $epsAsignados->pluck('eje')->unique()->values();

            $programasPorEje = $epsAsignados
                ->groupBy('eje')
                ->map(fn($g) => $g->pluck('programa')->unique()->values())
                ->toArray();

            $subprogramasPorEjePrograma = $epsAsignados
                ->groupBy(fn($x) => $x->eje.'|'.$x->programa)
                ->map(fn($g) => $g->pluck('subprograma')->unique()->values())
                ->toArray();

        // === 3) Nombres (eje/programa/subprograma) + id del subprograma (nivel 3) ===
        [$mapNombresEje, $mapNombresPrograma, $mapNombresSubprograma] =
            $this->buildNombreMapsNivel1a3($year, $entidad, $asig);

        // === 4) Opciones del catálogo colgando del EPS asignado ===
        [
            $capitulosPorEPS,              // key: eje|programa|subprograma -> [capitulo...]
            $conceptosPorEPSC,             // key: eje|programa|subprograma|capitulo -> [concepto...]
            $partidasGenPorEPSCC,          // key: eje|programa|subprograma|capitulo|concepto -> [partida_generica...]
            $bienesPorEPSCCP,              // key: eje|programa|subprograma|capitulo|concepto|partida_generica -> [bien...]
            $mapNombresCapitulo,           // capitulo -> nombre
            $mapNombresConcepto,           // eje|programa|subprograma|capitulo|concepto -> nombre
            $mapNombresPartidaGenerica,    // eje|programa|subprograma|capitulo|concepto|partida_generica -> nombre
            $mapNombresBien                // eje|programa|subprograma|capitulo|concepto|partida_generica|bien -> nombre
        ] = $this->buildMapsNivel4a8($year, $entidad, $asig);

        // ============================================================
        // 5) NUEVO: selector rápido
        //   - proyectosPorEPS: por EPS, lista de (cap, con, pg, nombre)
        //   - bienesPorProyecto: por EPS|cap|con|pg lista de (bien, nombre)
        // ============================================================
        $proyectosPorEPS = [];
        foreach ($partidasGenPorEPSCC as $keyEPSCC => $pgs) {
            // $keyEPSCC = eje|programa|subprograma|capitulo|concepto
            $parts = explode('|', (string)$keyEPSCC);
            if (count($parts) < 5) continue;

            [$eje, $prog, $sub, $cap, $con] = $parts;
            $epsKey = "{$eje}|{$prog}|{$sub}";

            foreach ((array)$pgs as $pg) {
                $pg = (string)$pg;
                if ($pg === '') continue;

                $nombre = $mapNombresPartidaGenerica["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}"] ?? '';
                $proyectosPorEPS[$epsKey][] = [
                    'capitulo' => $cap,
                    'concepto' => $con,
                    'pg'       => $pg,
                    'nombre'   => $nombre,
                ];
            }
        }

        // normalizar: unique por cap|con|pg y sort
        foreach ($proyectosPorEPS as $epsKey => $list) {
            $unique = collect($list)
                ->unique(fn($x) => $x['capitulo'].'|'.$x['concepto'].'|'.$x['pg'])
                ->sortBy(fn($x) => $x['capitulo'].'|'.$x['concepto'].'|'.$x['pg'])
                ->values()
                ->all();
            $proyectosPorEPS[$epsKey] = $unique;
        }

        $bienesPorProyecto = [];
        foreach ($bienesPorEPSCCP as $keyEPSCCP => $bienes) {
            // $keyEPSCCP = eje|programa|subprograma|capitulo|concepto|partida_generica
            $parts = explode('|', (string)$keyEPSCCP);
            if (count($parts) < 6) continue;

            [$eje, $prog, $sub, $cap, $con, $pg] = $parts;
            $k = "{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}";

            $items = [];
            foreach ((array)$bienes as $bien) {
                $bien = (string)$bien;
                if ($bien === '') continue;

                $nombre = $mapNombresBien["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}|{$bien}"] ?? '';
                $items[] = ['bien' => $bien, 'nombre' => $nombre];
            }

            $bienesPorProyecto[$k] = collect($items)
                ->unique('bien')
                ->sortBy('bien')
                ->values()
                ->all();
        }
        // Instituciones lista
        $instituciones = Institucion::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'siglas']);

        return view('expedientes.create', compact(
            'year',
            'entidad',
            'resumenAsignaciones',
            'ejesPermitidos',
            'programasPorEje',
            'subprogramasPorEjePrograma',
            'mapNombresEje',
            'mapNombresPrograma',
            'mapNombresSubprograma',

            'capitulosPorEPS',
            'conceptosPorEPSC',
            'partidasGenPorEPSCC',
            'bienesPorEPSCCP',
            'mapNombresCapitulo',
            'mapNombresConcepto',
            'mapNombresPartidaGenerica',
            'mapNombresBien',

            //selector rápido
            'proyectosPorEPS',
            'bienesPorProyecto',
            //Lista de Instituciones
            'instituciones'
        ));
    }

    /**
     * Guardar expediente (BORRADOR) - primera parte:
     * - EPS (asignación)
     * - Clasificación (cap/con/pg)
     * - Bienes (multi)
     * - Datos generales (nombre_proyecto + tema + area_ejecutora)
     */
    public function store(Request $request)
    {
        $validated = $this->validateExpediente($request);

        $user = auth()->user();
        $institucionId = $user->institucion_id;

        abort_if(!$institucionId, 403, 'Tu usuario no tiene institución asignada.');

        $year = (int)$validated['anio_ejercicio'];
        $entidad = (string)($validated['entidad'] ?? '8300');

        // Seguridad 1: validar que EPS pertenece a lo asignado a la institución
        $this->assertAsignacionInstitucion(
            $institucionId,
            $year,
            $entidad,
            (string)$validated['eje'],
            (string)$validated['programa'],
            (string)$validated['subprograma']
        );

        // Seguridad 2: validar que el proyecto (PG nivel 6) exista bajo ese EPS+cap+con
        $this->assertProyectoEnCatalogo(
            $year,
            $entidad,
            (string)$validated['eje'],
            (string)$validated['programa'],
            (string)$validated['subprograma'],
            (string)$validated['capitulo'],
            (string)$validated['concepto'],
            (string)$validated['partida_generica']
        );

        // Seguridad 3: validar bienes (nivel 7/8) bajo ese proyecto
        $bienes = $validated['bienes'] ?? [];
        $this->assertBienesEnCatalogo(
            $year,
            $entidad,
            (string)$validated['eje'],
            (string)$validated['programa'],
            (string)$validated['subprograma'],
            (string)$validated['capitulo'],
            (string)$validated['concepto'],
            (string)$validated['partida_generica'],
            $bienes
        );

        // Candado: evitar duplicar 1ra parte (EPS + Proyecto) para la misma institución/año
        $yaExiste = Expediente::query()
            ->where('anio_ejercicio', $year)
            ->where('entidad', $entidad)
            ->where('institucion_id', $institucionId)
            ->where('eje', (string)$validated['eje'])
            ->where('programa', (string)$validated['programa'])
            ->where('subprograma', (string)$validated['subprograma'])
            ->where('capitulo', (string)$validated['capitulo'])
            ->where('concepto', (string)$validated['concepto'])
            ->where('partida_generica', (string)$validated['partida_generica'])
            ->whereIn('estatus', [
                Expediente::ESTADO_BORRADOR,
                Expediente::ESTADO_EN_VALIDACION,
                Expediente::ESTADO_RECHAZADO,
            ])
            ->exists();

        if ($yaExiste) {
            return back()
                ->withErrors([
                    'partida_generica' => 'Ya existe un expediente creado para esta asignación/proyecto. Ve a "Mis expedientes" para continuarlo.',
                ])
                ->withInput();
        }

        return DB::transaction(function () use ($validated, $user, $bienes) {

            $expedienteData = collect($validated)->only([
                'nombre_proyecto',
                'anio_ejercicio',
                'entidad',
                'eje',
                'programa',
                'subprograma',
                'capitulo',
                'concepto',
                'partida_generica',
                'tema',
                'area_ejecutora',
                'tipo_recurso',
            ])->toArray();

            $expedienteData['user_id']        = $user->id;
            $expedienteData['institucion_id'] = $user->institucion_id;
            $expedienteData['folio']          = 'EXP-' . now()->format('YmdHis');
            $expedienteData['estatus']        = Expediente::ESTADO_BORRADOR;
            $expedienteData['bienes']         = array_values($bienes);

            $expediente = Expediente::create($expedienteData);

            // Historial simple (opcional)
            if (class_exists(HistorialModificacion::class)) {
                try {
                    HistorialModificacion::create([
                        'expediente_id' => $expediente->id,
                        'user_id'       => $user->id,
                        'accion'        => 'CREAR_1RA_PARTE',
                        'descripcion'   => 'Primera parte del expediente guardada.',
                    ]);
                } catch (\Throwable $e) {
                    // no romper si el esquema difiere
                }
            }

            return redirect()
                ->route('expedientes.edit', $expediente)
                ->with('success', '1ra parte guardada. Continúa con la captura del expediente.');
        });
    }

    /**
     * Formulario editar expediente.
     */
    public function edit(Expediente $expediente)
    {
        $user = auth()->user();

        // Seguridad: por ahora solo el dueño edita (luego lo abrimos a institución si quieres)
        abort_if($expediente->user_id !== $user->id, 403, 'No tienes permiso para editar este expediente.');

        // Bloqueo por estatus
        if (in_array($expediente->estatus, [Expediente::ESTADO_APROBADO], true)) {
            return redirect()
                ->route('expedientes.index')
                ->with('error', 'Este expediente está aprobado y no puede editarse.');
        }

        // Si quieres bloquear también cuando está en validación (recomendado), descomenta:
        // if ($expediente->estatus === Expediente::ESTADO_EN_VALIDACION) {
        //     return redirect()->route('expedientes.index')->with('error', 'Este expediente está en validación y no puede editarse.');
        // }

        $year = (int)($expediente->anio_ejercicio ?? now()->year);
        $entidad = (string)($expediente->entidad ?? '8300');

        abort_if(!$user->institucion_id, 403, 'Tu usuario no tiene institución asignada.');

        // Traer asignaciones activas de la institución (igual que create)
        $asig = $this->asignacionesInstitucion($user->institucion_id, $year, $entidad);

        if ($asig->isEmpty()) {
            return redirect()
                ->route('expedientes.index')
                ->with('error', 'Tu institución no tiene subprogramas FASP asignados para este año.');
        }

        $pad2 = fn($v) => str_pad((string)$v, 2, '0', STR_PAD_LEFT);

        // === 1) Listado/resumen asignaciones (para mostrar arriba) ===
        $resumenAsignaciones = $asig
            ->groupBy(fn($a) => $pad2($a->eje) . '|' . $pad2($a->programa))
            ->map(function ($items) use ($pad2) {
                $first = $items->first();
                return [
                    'eje' => $pad2($first->eje),
                    'programa' => $pad2($first->programa),
                    'subprogramas' => $items->pluck('subprograma')->filter()->map(fn($s) => $pad2($s))->unique()->sort()->values(),
                ];
            })
            ->values()
            ->sortBy(fn($x) => $x['eje'].'|'.$x['programa'])
            ->values();

        // === 2) Selects dependientes EPS ===
        $ejesPermitidos = $asig->pluck('eje')->map(fn($v) => $pad2($v))->unique()->values();
        $programasPorEje = $asig
            ->groupBy(fn($x) => $pad2($x->eje))
            ->map(fn($g) => $g->pluck('programa')->map(fn($p) => $pad2($p))->unique()->values())
            ->toArray();
        $subprogramasPorEjePrograma = $asig
            ->groupBy(fn($x) => $pad2($x->eje) . '|' . $pad2($x->programa))
            ->map(fn($g) => $g->pluck('subprograma')->map(fn($s) => $pad2($s))->unique()->values())
            ->toArray();

        // === 3) Nombres nivel 1 a 3 ===
        [$mapNombresEje, $mapNombresPrograma, $mapNombresSubprograma] =
            $this->buildNombreMapsNivel1a3($year, $entidad, $asig);

        // === 4) Opciones catálogo nivel 4 a 8 bajo EPS asignado ===
        [
            $capitulosPorEPS,
            $conceptosPorEPSC,
            $partidasGenPorEPSCC,
            $bienesPorEPSCCP,
            $mapNombresCapitulo,
            $mapNombresConcepto,
            $mapNombresPartidaGenerica,
            $mapNombresBien
        ] = $this->buildMapsNivel4a8($year, $entidad, $asig);

        // === 5) Selector rápido proyectos/bienes ===
        $proyectosPorEPS = [];
        foreach ($partidasGenPorEPSCC as $keyEPSCC => $pgs) {
            $parts = explode('|', (string)$keyEPSCC);
            if (count($parts) < 5) continue;
            [$eje, $prog, $sub, $cap, $con] = $parts;
            $epsKey = "{$eje}|{$prog}|{$sub}";

            foreach ((array)$pgs as $pg) {
                $pg = (string)$pg;
                if ($pg === '') continue;

                $nombre = $mapNombresPartidaGenerica["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}"] ?? '';
                $proyectosPorEPS[$epsKey][] = [
                    'capitulo' => $cap,
                    'concepto' => $con,
                    'pg'       => $pg,
                    'nombre'   => $nombre,
                ];
            }
        }
        foreach ($proyectosPorEPS as $epsKey => $list) {
            $proyectosPorEPS[$epsKey] = collect($list)
                ->unique(fn($x) => $x['capitulo'].'|'.$x['concepto'].'|'.$x['pg'])
                ->sortBy(fn($x) => $x['capitulo'].'|'.$x['concepto'].'|'.$x['pg'])
                ->values()
                ->all();
        }

        $bienesPorProyecto = [];
        foreach ($bienesPorEPSCCP as $keyEPSCCP => $bienes) {
            $parts = explode('|', (string)$keyEPSCCP);
            if (count($parts) < 6) continue;

            [$eje, $prog, $sub, $cap, $con, $pg] = $parts;
            $k = "{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}";

            $items = [];
            foreach ((array)$bienes as $bien) {
                $bien = (string)$bien;
                if ($bien === '') continue;

                $nombre = $mapNombresBien["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}|{$bien}"] ?? '';
                $items[] = ['bien' => $bien, 'nombre' => $nombre];
            }

            $bienesPorProyecto[$k] = collect($items)
                ->unique('bien')
                ->sortBy('bien')
                ->values()
                ->all();
        }

        // Lista instituciones para área ejecutora
        $instituciones = \App\Models\Institucion::query()
            ->orderBy('orden')->orderBy('nombre')
            ->get(['id','nombre','siglas']);

        return view('expedientes.edit', compact(
            'expediente',
            'year',
            'entidad',
            'resumenAsignaciones',
            'ejesPermitidos',
            'programasPorEje',
            'subprogramasPorEjePrograma',
            'mapNombresEje',
            'mapNombresPrograma',
            'mapNombresSubprograma',

            'capitulosPorEPS',
            'conceptosPorEPSC',
            'partidasGenPorEPSCC',
            'bienesPorEPSCCP',
            'mapNombresCapitulo',
            'mapNombresConcepto',
            'mapNombresPartidaGenerica',
            'mapNombresBien',

            'proyectosPorEPS',
            'bienesPorProyecto',
            'instituciones'
        ));
    }

    public function update(Request $request, Expediente $expediente)
    {
        $user = auth()->user();

        abort_if($expediente->user_id !== $user->id, 403, 'No tienes permiso para editar este expediente.');

        if (in_array($expediente->estatus, [Expediente::ESTADO_APROBADO], true)) {
            return redirect()->route('expedientes.index')->with('error', 'Este expediente está aprobado y no puede editarse.');
        }

        $validated = $this->validateExpediente($request, true);

        $institucionId = $user->institucion_id;
        abort_if(!$institucionId, 403, 'Tu usuario no tiene institución asignada.');

        $year = (int)$validated['anio_ejercicio'];
        $entidad = (string)($validated['entidad'] ?? '8300');

        // Seguridad 1: validar EPS asignado a institución
        $this->assertAsignacionInstitucion(
            $institucionId,
            $year,
            $entidad,
            (string)$validated['eje'],
            (string)$validated['programa'],
            (string)$validated['subprograma']
        );

        // Seguridad 2: validar proyecto (PG nivel 6)
        $this->assertProyectoEnCatalogo(
            $year,
            $entidad,
            (string)$validated['eje'],
            (string)$validated['programa'],
            (string)$validated['subprograma'],
            (string)$validated['capitulo'],
            (string)$validated['concepto'],
            (string)$validated['partida_generica']
        );

        // Seguridad 3: validar bienes (nivel 7/8)
        $bienes = $validated['bienes'] ?? [];
        $this->assertBienesEnCatalogo(
            $year,
            $entidad,
            (string)$validated['eje'],
            (string)$validated['programa'],
            (string)$validated['subprograma'],
            (string)$validated['capitulo'],
            (string)$validated['concepto'],
            (string)$validated['partida_generica'],
            $bienes
        );

        // Candado anti-duplicado (cuando cambias EPS/proyecto) ignorando el mismo expediente
        $yaExiste = Expediente::query()
            ->where('anio_ejercicio', $year)
            ->where('entidad', $entidad)
            ->where('institucion_id', (int)$institucionId)
            ->where('eje', (string)$validated['eje'])
            ->where('programa', (string)$validated['programa'])
            ->where('subprograma', (string)$validated['subprograma'])
            ->where('capitulo', (string)$validated['capitulo'])
            ->where('concepto', (string)$validated['concepto'])
            ->where('partida_generica', (string)$validated['partida_generica'])
            ->where('id', '!=', $expediente->id)
            ->whereIn('estatus', [
                Expediente::ESTADO_BORRADOR,
                Expediente::ESTADO_EN_VALIDACION,
                Expediente::ESTADO_RECHAZADO,
            ])
            ->exists();

        if ($yaExiste) {
            return back()
                ->withErrors([
                    'partida_generica' => 'Ya existe un expediente para esta asignación/proyecto. Elige otro EPS/Proyecto.',
                ])
                ->withInput();
        }

        return DB::transaction(function () use ($expediente, $validated, $user, $bienes) {

            $data = collect($validated)->only([
                'nombre_proyecto',
                'tipo_recurso',
                'anio_ejercicio',
                'entidad',
                'eje',
                'programa',
                'subprograma',
                'capitulo',
                'concepto',
                'partida_generica',
                'tema',
                'area_ejecutora',
            ])->toArray();

            // Nota: dependencia ya no se usa (debe ser nullable en DB)
            $expediente->update($data);

            // bienes (json)
            $expediente->bienes = array_values($bienes);
            $expediente->save();

            if (class_exists(\App\Models\HistorialModificacion::class)) {
                try {
                    \App\Models\HistorialModificacion::create([
                        'expediente_id' => $expediente->id,
                        'user_id'       => $user->id,
                        'accion'        => 'ACTUALIZAR_1RA_PARTE',
                        'descripcion'   => 'Actualización de 1ra parte (EPS/Proyecto/Bienes/Datos generales).',
                    ]);
                } catch (\Throwable $e) {}
            }

            return redirect()
                ->route('expedientes.edit', $expediente)
                ->with('success', 'Expediente actualizado.');
        });
    }


    /**
     * Eliminar expediente.
     */
    public function destroy(Expediente $expediente)
    {
        $user = auth()->user();

        abort_if($expediente->user_id !== $user->id, 403, 'No tienes permiso para eliminar este expediente.');

        if (!in_array($expediente->estatus, [Expediente::ESTADO_BORRADOR, Expediente::ESTADO_RECHAZADO], true)) {
            return back()->with('error', 'Solo puedes eliminar expedientes en borrador o rechazados.');
        }

        DB::transaction(function () use ($expediente) {
            Presupuesto::where('expediente_id', $expediente->id)->delete();
            $expediente->delete();
        });

        return redirect()->route('expedientes.index')->with('success', 'Expediente eliminado.');
    }

    // =========================
    //  FLUJO VALIDACIÓN
    // =========================

    public function enviarValidacion(Expediente $expediente)
    {
        $user = auth()->user();
        abort_if($expediente->user_id !== $user->id, 403);

        if ($expediente->estatus !== Expediente::ESTADO_BORRADOR && $expediente->estatus !== Expediente::ESTADO_RECHAZADO) {
            return back()->with('error', 'Solo puedes enviar a validación expedientes en borrador o rechazados.');
        }

        $expediente->estatus = Expediente::ESTADO_EN_VALIDACION;
        $expediente->save();

        return back()->with('success', 'Expediente enviado a validación.');
    }

    public function listaEnValidacion()
    {
        // Ajusta a tu vista/rol de validador
        $expedientes = Expediente::where('estatus', Expediente::ESTADO_EN_VALIDACION)
            ->with(['usuario', 'historiales.usuario'])
            ->latest()
            ->paginate(15);

        return view('validador.expedientes.en_validacion', compact('expedientes'));
    }

    public function mostrarParaRevision(Expediente $expediente)
    {
        $expediente->load(['usuario', 'historiales.usuario']);
        $presupuestos = Presupuesto::where('expediente_id', $expediente->id)->get();

        return view('validador.expedientes.revisar', compact('expediente', 'presupuestos'));
    }

    public function decidir(Request $request, Expediente $expediente)
    {
        $request->validate([
            'accion' => 'required|in:aprobar,rechazar',
            'observacion' => 'nullable|string|max:5000',
        ]);

        if ($expediente->estatus !== Expediente::ESTADO_EN_VALIDACION) {
            return back()->with('error', 'Este expediente no está en validación.');
        }

        $accion = $request->input('accion');

        if ($accion === 'aprobar') {
            $expediente->estatus = Expediente::ESTADO_APROBADO;
        } else {
            $expediente->estatus = Expediente::ESTADO_RECHAZADO;
            if ($request->filled('observacion')) {
                $expediente->observacion_rechazo = $request->input('observacion');
            }
        }

        $expediente->save();

        if (class_exists(HistorialModificacion::class)) {
            try {
                HistorialModificacion::create([
                    'expediente_id' => $expediente->id,
                    'user_id'       => auth()->id(),
                    'accion'        => strtoupper($accion),
                    'descripcion'   => $request->input('observacion', ''),
                ]);
            } catch (\Throwable $e) {
            }
        }

        return redirect()->route('validador.expedientes.en_validacion')->with('success', 'Decisión registrada.');
    }

    // =========================
    //  VALIDACIONES / HELPERS
    // =========================

    /**
     * Validación para create/update.
     */
    public function validateExpediente(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            // Datos generales
            'nombre_proyecto' => 'required|string|max:255',
            'tipo_recurso'    => 'nullable|string|max:50',
            'anio_ejercicio'  => 'required|integer',

            // Estructura programática (OBLIGATORIA)
            'entidad'         => 'nullable|string|max:10',
            'eje'             => 'required|string|max:10',
            'programa'        => 'required|string|max:10',
            'subprograma'     => 'required|string|max:10',

            // Clasificación (catálogo)
            'capitulo'         => 'required|string|max:10',
            'concepto'         => 'required|string|max:10',
            'partida_generica' => 'required|string|max:10',

            // Bienes (subtemas)
            'bienes'   => 'nullable|array',
            'bienes.*' => 'string|max:255',

            // Tema/Área
            'tema'           => 'nullable|string|max:255',
            'area_ejecutora' => 'nullable|integer|exists:instituciones,id',

            // Presupuesto 
            'presupuestos'                        => 'nullable|array',
            'presupuestos.*.descripcion_concepto' => 'nullable|string',
            'presupuestos.*.cantidad'             => 'nullable|numeric',
            'presupuestos.*.unidad'               => 'nullable|string|max:50',
            'presupuestos.*.precio_unitario'      => 'nullable|numeric',
            'presupuestos.*.subtotal'             => 'nullable|numeric',
            'presupuestos.*.iva'                  => 'nullable|numeric',
            'presupuestos.*.total'                => 'nullable|numeric',
        ], [
            'capitulo.required' => 'Selecciona un capítulo.',
            'concepto.required' => 'Selecciona un concepto.',
            'partida_generica.required' => 'Selecciona un proyecto (partida genérica).',
        ]);
    }

    /**
     * Guarda/actualiza presupuesto 
     */
    protected function guardarPresupuesto(Expediente $expediente, array $presupuestos): void
    {
        Presupuesto::where('expediente_id', $expediente->id)->delete();

        foreach ($presupuestos as $p) {
            // ignora filas vacías
            if (
                empty($p['descripcion_concepto']) &&
                empty($p['cantidad']) &&
                empty($p['precio_unitario'])
            ) {
                continue;
            }

            Presupuesto::create([
                'expediente_id'         => $expediente->id,
                'descripcion_concepto'  => $p['descripcion_concepto'] ?? null,
                'cantidad'              => $p['cantidad'] ?? null,
                'unidad'                => $p['unidad'] ?? null,
                'precio_unitario'       => $p['precio_unitario'] ?? null,
                'subtotal'              => $p['subtotal'] ?? null,
                'iva'                   => $p['iva'] ?? null,
                'total'                 => $p['total'] ?? null,
            ]);
        }
    }

    /**
     * Consulta asignaciones activas de la institución (nivel 3).
     */
    protected function asignacionesInstitucion(int $institucionId, int $year, string $entidad)
    {
        return FaspAsignacionInstitucion::query()
            ->activas()
            ->where('institucion_id', $institucionId)
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->orderBy('eje')
            ->orderBy('programa')
            ->orderBy('subprograma')
            ->get(['id','year','entidad','nivel','eje','programa','subprograma','institucion_id']);
    }

    /**
     * Asegura que el EPS está asignado a la institución.
     */
    protected function assertAsignacionInstitucion(
        int $institucionId,
        int $year,
        string $entidad,
        string $eje,
        string $programa,
        string $subprograma
    ): void {
        $ok = FaspAsignacionInstitucion::query()
            ->activas()
            ->where('institucion_id', $institucionId)
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->where('eje', $eje)
            ->where('programa', $programa)
            ->where('subprograma', $subprograma)
            ->exists();

        abort_if(!$ok, 403, 'No tienes asignado ese subprograma para tu institución.');
    }

    /**
     * Asegura que la PG (nivel 6) existe bajo ese EPS + cap + con.
     */
    protected function assertProyectoEnCatalogo(
        int $year,
        string $entidad,
        string $eje,
        string $programa,
        string $subprograma,
        string $capitulo,
        string $concepto,
        string $partidaGenerica
    ): void {
        $ok = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 6)
            ->where('eje', $eje)
            ->where('programa', $programa)
            ->where('subprograma', $subprograma)
            ->where('capitulo', $capitulo)
            ->where('concepto', $concepto)
            ->where('partida_generica', $partidaGenerica)
            ->exists();

        abort_if(!$ok, 403, 'El proyecto (partida genérica) no es válido para el EPS seleccionado.');
    }

    /**
     * Asegura que cada bien existe bajo ese mismo proyecto (nivel 7/8).
     */
    protected function assertBienesEnCatalogo(
        int $year,
        string $entidad,
        string $eje,
        string $programa,
        string $subprograma,
        string $capitulo,
        string $concepto,
        string $partidaGenerica,
        array $bienes
    ): void {
        $bienes = array_values(array_filter(array_map('strval', $bienes ?? [])));

        // si no seleccionó bienes, lo dejamos pasar
        if (count($bienes) === 0) return;

        $existentes = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->whereIn('nivel', [7, 8])
            ->where('eje', $eje)
            ->where('programa', $programa)
            ->where('subprograma', $subprograma)
            ->where('capitulo', $capitulo)
            ->where('concepto', $concepto)
            ->where('partida_generica', $partidaGenerica)
            ->whereIn('bien', $bienes)
            ->pluck('bien')
            ->map(fn($x) => (string)$x)
            ->all();

        $faltantes = array_values(array_diff($bienes, $existentes));

        abort_if(count($faltantes) > 0, 403, 'Hay bienes inválidos para el proyecto seleccionado: '.implode(', ', $faltantes));
    }

    /**
     * Nombres (nivel 1 a 3): Eje/Programa/Subprograma.
     */
    protected function buildNombreMapsNivel1a3(int $year, string $entidad, $asig): array
    {
        // combos asignados
        $ejes = $asig->pluck('eje')->filter()->unique()->values()->all();

        $pairs = $asig->map(fn($x) => [(string)$x->eje, (string)$x->programa])
            ->unique(fn($p) => $p[0].'|'.$p[1])
            ->values()
            ->all();

        $q = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3);

        if (count($ejes)) {
            $q->whereIn('eje', $ejes);
        }

        if (count($pairs)) {
            $q->where(function ($w) use ($pairs) {
                foreach ($pairs as $p) {
                    $w->orWhere(function ($x) use ($p) {
                        $x->where('eje', $p[0])->where('programa', $p[1]);
                    });
                }
            });
        }

        $subs = $q->with([
                'parent:id,year,entidad,nivel,parent_id,eje,programa,nombre',       // programa (nivel 2)
                'parent.parent:id,year,entidad,nivel,eje,nombre'                    // eje (nivel 1)
            ])
            ->get(['id','year','entidad','nivel','parent_id','eje','programa','subprograma','nombre']);

        $mapNombresEje = [];
        $mapNombresPrograma = [];
        $mapNombresSubprograma = [];

        $pad2 = fn($v) => str_pad((string)$v, 2, '0', STR_PAD_LEFT);
        foreach ($subs as $row) {
            $eje = $pad2($row->eje);
            $programa = $pad2($row->programa);
            $subprograma = $pad2($row->subprograma);

            if ($eje !== '' && $programa !== '' && $subprograma !== '') {
                $k = "{$eje}|{$programa}|{$subprograma}";
                $mapNombresSubprograma[$k] = [
                    'id' => $row->id,
                    'nombre' => (string)($row->nombre ?? ''),
                ];
            }

            if ($row->parent) {
                $kp = "{$eje}|{$programa}";
                if (!isset($mapNombresPrograma[$kp])) {
                    $mapNombresPrograma[$kp] = (string)($row->parent->nombre ?? '');
                }
            }

            if ($row->parent && $row->parent->parent) {
                if (!isset($mapNombresEje[$eje])) {
                    $mapNombresEje[$eje] = (string)($row->parent->parent->nombre ?? '');
                }
            }
        }

        return [$mapNombresEje, $mapNombresPrograma, $mapNombresSubprograma];
    }

    /**
     * Trae niveles 4 a 8 del catálogo que cuelgan de los EPS asignados
     * y arma maps para selects dependientes.
     */
    protected function buildMapsNivel4a8(int $year, string $entidad, $asig): array
    {
        $triples = $asig->map(fn($x) => [
                (string)$x->eje,
                (string)$x->programa,
                (string)$x->subprograma
            ])
            ->unique(fn($t) => $t[0].'|'.$t[1].'|'.$t[2])
            ->values()
            ->all();

        $q = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->whereIn('nivel', [4,5,6,7,8]);

        if (count($triples)) {
            $q->where(function ($w) use ($triples) {
                foreach ($triples as $t) {
                    $w->orWhere(function ($x) use ($t) {
                        $x->where('eje', $t[0])->where('programa', $t[1])->where('subprograma', $t[2]);
                    });
                }
            });
        }

        $rows = $q->get([
            'id','year','entidad','nivel',
            'eje','programa','subprograma',
            'capitulo','concepto','partida_generica','bien',
            'nombre'
        ]);

        $capitulosPorEPS = [];
        $conceptosPorEPSC = [];
        $partidasGenPorEPSCC = [];
        $bienesPorEPSCCP = [];

        $mapNombresCapitulo = [];
        $mapNombresConcepto = [];
        $mapNombresPartidaGenerica = [];
        $mapNombresBien = [];

        $pad2 = fn($v) => str_pad((string)$v, 2, '0', STR_PAD_LEFT);
        foreach ($rows as $r) {
            $eje  = $pad2($r->eje);
            $prog = $pad2($r->programa);
            $sub  = $pad2($r->subprograma);

            $cap  = (string)$r->capitulo;
            $con  = (string)$r->concepto;
            $pg   = (string)$r->partida_generica;
            $bien = (string)$r->bien;

            // Nivel 4: capítulo
            if ((int)$r->nivel === 4 && $cap !== '') {
                $capitulosPorEPS["{$eje}|{$prog}|{$sub}"][] = $cap;
                $mapNombresCapitulo[$cap] = (string)($r->nombre ?? '');
            }

            // Nivel 5: concepto
            if ((int)$r->nivel === 5 && $cap !== '' && $con !== '') {
                $conceptosPorEPSC["{$eje}|{$prog}|{$sub}|{$cap}"][] = $con;
                $mapNombresConcepto["{$eje}|{$prog}|{$sub}|{$cap}|{$con}"] = (string)($r->nombre ?? '');
            }

            // Nivel 6: partida genérica (proyecto)
            if ((int)$r->nivel === 6 && $cap !== '' && $con !== '' && $pg !== '') {
                $partidasGenPorEPSCC["{$eje}|{$prog}|{$sub}|{$cap}|{$con}"][] = $pg;
                $mapNombresPartidaGenerica["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}"] = (string)($r->nombre ?? '');
            }

            // Nivel 7/8: bien (partida específica)
            if (in_array((int)$r->nivel, [7,8], true) && $cap !== '' && $con !== '' && $pg !== '' && $bien !== '') {
                $bienesPorEPSCCP["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}"][] = $bien;
                $mapNombresBien["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}|{$bien}"] = (string)($r->nombre ?? '');
            }
        }

        // Normalizar: unique + sort
        foreach ($capitulosPorEPS as $k => $arr) {
            $capitulosPorEPS[$k] = collect($arr)->unique()->sort()->values()->all();
        }
        foreach ($conceptosPorEPSC as $k => $arr) {
            $conceptosPorEPSC[$k] = collect($arr)->unique()->sort()->values()->all();
        }
        foreach ($partidasGenPorEPSCC as $k => $arr) {
            $partidasGenPorEPSCC[$k] = collect($arr)->unique()->sort()->values()->all();
        }
        foreach ($bienesPorEPSCCP as $k => $arr) {
            $bienesPorEPSCCP[$k] = collect($arr)->unique()->sort()->values()->all();
        }

        return [
            $capitulosPorEPS,
            $conceptosPorEPSC,
            $partidasGenPorEPSCC,
            $bienesPorEPSCCP,
            $mapNombresCapitulo,
            $mapNombresConcepto,
            $mapNombresPartidaGenerica,
            $mapNombresBien
        ];
    }
}