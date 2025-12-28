<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Presupuesto;
use App\Models\HistorialModificacion;
use App\Models\FaspAsignacionInstitucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FaspCatalogo;


class ExpedienteController extends Controller
{
    // ============================= CAPTURISTA =============================

    /**
     * Listado de expedientes del capturista autenticado.
     */
    public function index()
    {
        $user = auth()->user();

        $expedientes = Expediente::where('user_id', $user->id)
            ->with(['historiales.usuario'])
            ->latest()
            ->paginate(10);

        return view('expedientes.index', compact('expedientes'));
    }

    /**
     * Formulario crear expediente (solo con opciones asignadas a la institución del capturista).
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

        // === 1) Listado/resumen asignaciones (para mostrar arriba) ===
        $resumenAsignaciones = $asig
            ->groupBy(fn($a) => (string)$a->eje . '|' . (string)$a->programa)
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'eje' => (string)$first->eje,
                    'programa' => (string)$first->programa,
                    'subprogramas' => $items->pluck('subprograma')->filter()->unique()->sort()->values(),
                ];
            })
            ->values()
            ->sortBy(fn($x) => $x['eje'].'|'.$x['programa'])
            ->values();

        // === 2) Selects dependientes del 1er apartado (asignación) ===
        $ejesPermitidos = $asig->pluck('eje')->unique()->values();
        $programasPorEje = $asig->groupBy('eje')->map(fn($g) => $g->pluck('programa')->unique()->values());
        $subprogramasPorEjePrograma = $asig->groupBy(fn($x) => $x->eje . '|' . $x->programa)
            ->map(fn($g) => $g->pluck('subprograma')->unique()->values());

        // === 3) Nombres (eje/programa/subprograma) + id del subprograma (nivel 3) ===
        [$mapNombresEje, $mapNombresPrograma, $mapNombresSubprograma] =
            $this->buildNombreMapsNivel1a3($year, $entidad, $asig);

        // === 4) Opciones del 2do apartado: capitulo/concepto/partida_generica/bien (según asignado) ===
        [
            $capitulosPorEPS,              // key: eje|programa|subprograma -> [capitulo...]
            $conceptosPorEPSC,             // key: eje|programa|subprograma|capitulo -> [concepto...]
            $partidasGenPorEPSCC,          // key: eje|programa|subprograma|capitulo|concepto -> [partida_generica...]
            $bienesPorEPSCCP,              // key: eje|programa|subprograma|capitulo|concepto|partida_generica -> [bien...]
            $mapNombresCapitulo,           // capitulo -> nombre
            $mapNombresConcepto,           // eje|programa|subprograma|capitulo|concepto -> nombre (opcional)
            $mapNombresPartidaGenerica,    // eje|...|partida_generica -> nombre (opcional)
            $mapNombresBien                // eje|...|bien -> nombre (opcional)
        ] = $this->buildMapsNivel4a8($year, $entidad, $asig);

        return view('expedientes.create', compact(
            'year',
            'entidad',
            'ejesPermitidos',
            'programasPorEje',
            'subprogramasPorEjePrograma',
            'resumenAsignaciones',
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
            'mapNombresBien'
        ));
    }


    /**
     * Guardar expediente (borrador) + presupuesto.
     */
    public function store(Request $request)
    {
        $validated = $this->validateExpediente($request);

        $user = auth()->user();
        $institucionId = $user->institucion_id;

        abort_if(!$institucionId, 403, 'Tu usuario no tiene institución asignada.');

        $year = (int)$validated['anio_ejercicio'];
        $entidad = (string)($validated['entidad'] ?? '8300');

        // Seguridad: validar que el eje/programa/subprograma pertenece a lo asignado a la institución
        $this->assertAsignacionInstitucion(
            $institucionId,
            $year,
            $entidad,
            $validated['eje'],
            $validated['programa'],
            $validated['subprograma']
        );

        return DB::transaction(function () use ($validated) {

            $expedienteData = collect($validated)->only([
                'nombre_proyecto',
                'dependencia',
                'anio_ejercicio',
                'entidad',
                'eje',
                'programa',
                'subprograma',
                'tema',
                'area_ejecutora',
            ])->toArray();

            $expedienteData['user_id'] = auth()->id();
            $expedienteData['folio']   = 'EXP-' . now()->format('YmdHis');
            $expedienteData['estatus'] = Expediente::ESTADO_BORRADOR;

            $expediente = Expediente::create($expedienteData);

            $this->guardarPresupuesto($expediente, $validated['presupuestos'] ?? []);

            return redirect()
                ->route('expedientes.edit', $expediente)
                ->with('success', 'Expediente creado en borrador y presupuesto guardado.');
        });
    }

    /**
     * Formulario editar expediente.
     */
    public function edit(Expediente $expediente)
    {
        $user = auth()->user();

        abort_if($expediente->user_id !== $user->id, 403, 'No tienes permiso para editar este expediente.');

        // Bloqueo por estatus
        if (in_array($expediente->estatus, [Expediente::ESTADO_EN_VALIDACION, Expediente::ESTADO_APROBADO], true)) {
            return redirect()
                ->route('expedientes.index')
                ->with('error', 'No puedes editar este expediente porque está en validación o aprobado.');
        }

        // Si quieres estricto: si ya no está asignado a su institución, bloquear
        if ($user->institucion_id) {
            $this->assertAsignacionInstitucion(
                $user->institucion_id,
                (int)$expediente->anio_ejercicio,
                (string)($expediente->entidad ?? '8300'),
                (string)$expediente->eje,
                (string)$expediente->programa,
                (string)$expediente->subprograma
            );
        }

        $expediente->load(['presupuestos', 'historiales.usuario']);

        return view('expedientes.edit', compact('expediente'));
    }

    /**
     * Actualizar expediente existente.
     */
    public function update(Request $request, Expediente $expediente)
    {
        $user = auth()->user();

        abort_if($expediente->user_id !== $user->id, 403, 'No tienes permiso para actualizar este expediente.');

        // Bloqueo por estatus
        if (in_array($expediente->estatus, [Expediente::ESTADO_EN_VALIDACION, Expediente::ESTADO_APROBADO], true)) {
            return redirect()
                ->route('expedientes.edit', $expediente)
                ->with('error', 'No puedes modificar este expediente porque está en validación o aprobado.');
        }

        $validated = $this->validateExpediente($request, true);

        $institucionId = $user->institucion_id;
        abort_if(!$institucionId, 403, 'Tu usuario no tiene institución asignada.');

        $year = (int)$validated['anio_ejercicio'];
        $entidad = (string)($validated['entidad'] ?? '8300');

        $this->assertAsignacionInstitucion(
            $institucionId,
            $year,
            $entidad,
            $validated['eje'],
            $validated['programa'],
            $validated['subprograma']
        );

        return DB::transaction(function () use ($validated, $expediente) {

            $expData = collect($validated)->only([
                'nombre_proyecto',
                'dependencia',
                'anio_ejercicio',
                'entidad',
                'eje',
                'programa',
                'subprograma',
                'tema',
                'area_ejecutora',
            ])->toArray();

            $expediente->update($expData);

            // estrategia simple: borrar y recrear presupuesto
            $expediente->presupuestos()->delete();
            $this->guardarPresupuesto($expediente, $validated['presupuestos'] ?? []);

            return redirect()
                ->route('expedientes.edit', $expediente)
                ->with('success', 'Expediente actualizado correctamente.');
        });
    }

    /**
     * Eliminar expediente (solo capturista dueño).
     */
    public function destroy(Expediente $expediente)
    {
        abort_if($expediente->user_id !== auth()->id(), 403, 'No tienes permiso para eliminar este expediente.');

        $expediente->presupuestos()->delete();
        $expediente->delete();

        return redirect()
            ->route('expedientes.index')
            ->with('success', 'Expediente eliminado correctamente.');
    }

    // ============================= FLUJO DE VALIDACIÓN =============================

    /**
     * Enviar expediente a validación.
     */
    public function enviarValidacion(Expediente $expediente)
    {
        $user = Auth::user();

        abort_if($expediente->user_id !== $user->id, 403, 'No tienes permiso para enviar este expediente.');

        if (! $expediente->esBorrador() && ! $expediente->estaRechazado()) {
            return redirect()
                ->route('expedientes.edit', $expediente)
                ->with('error', 'Solo se pueden enviar expedientes en estado Borrador o Rechazado.');
        }

        // (Opcional) aquí podrías validar que ya tenga "PDF" o datos completos antes de enviar

        DB::transaction(function () use ($expediente, $user) {
            $estadoAnterior = $expediente->estatus;

            $expediente->estatus = Expediente::ESTADO_EN_VALIDACION;
            $expediente->save();

            HistorialModificacion::create([
                'expediente_id'   => $expediente->id,
                'usuario_id'      => $user->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo'    => Expediente::ESTADO_EN_VALIDACION,
                'observaciones'   => $estadoAnterior === Expediente::ESTADO_RECHAZADO
                    ? 'Reenvío tras correcciones'
                    : 'Envío inicial a validación',
            ]);
        });

        return redirect()
            ->route('expedientes.edit', $expediente)
            ->with('success', 'Expediente enviado a validación.');
    }

    /**
     * Lista de expedientes en validación para el validador.
     */
    public function listaEnValidacion()
    {
        $expedientes = Expediente::conEstado(Expediente::ESTADO_EN_VALIDACION)
            ->with(['usuario.institucion'])
            ->latest()
            ->paginate(10);

        // Construir llaves de asignación para validar en bloque
        $idsInstitucion = $expedientes->getCollection()
            ->pluck('usuario.institucion_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Si no hay instituciones, marcamos todo como fuera
        if (count($idsInstitucion) === 0) {
            $expedientes->getCollection()->transform(function ($exp) {
                $exp->fuera_asignacion = true;
                return $exp;
            });

            return view('validador.expedientes.index', compact('expedientes'));
        }

        // Tomamos los combos presentes en la página para limitar consulta
        $years = $expedientes->getCollection()->pluck('anio_ejercicio')->filter()->unique()->values()->all();
        $entidades = $expedientes->getCollection()->pluck('entidad')->map(fn($v) => $v ?: '8300')->unique()->values()->all();

        $asig = \App\Models\FaspAsignacionInstitucion::query()
            ->activas()
            ->whereIn('institucion_id', $idsInstitucion)
            ->whereIn('year', $years)
            ->whereIn('entidad', $entidades)
            ->where('nivel', 3)
            ->get(['institucion_id','year','entidad','eje','programa','subprograma']);

        // Set de llaves válidas: inst|year|entidad|eje|programa|subprograma
        $set = [];
        foreach ($asig as $a) {
            $key = implode('|', [
                $a->institucion_id,
                $a->year,
                $a->entidad,
                (string)$a->eje,
                (string)$a->programa,
                (string)$a->subprograma,
            ]);
            $set[$key] = true;
        }

        // Marcar expedientes
        $expedientes->getCollection()->transform(function ($exp) use ($set) {
            $instId = $exp->usuario?->institucion_id;
            $year = (int)$exp->anio_ejercicio;
            $entidad = (string)($exp->entidad ?? '8300');

            $key = implode('|', [
                $instId,
                $year,
                $entidad,
                (string)$exp->eje,
                (string)$exp->programa,
                (string)$exp->subprograma,
            ]);

            $exp->fuera_asignacion = !($instId && isset($set[$key]));
            return $exp;
        });

        return view('validador.expedientes.index', compact('expedientes'));
    }

    /**
     * Ver expediente para revisión del validador.
     */
    public function mostrarParaRevision(Expediente $expediente)
    {
        if (! $expediente->estaEnValidacion()) {
            abort(403, 'Solo se pueden revisar expedientes en estado "En validación".');
        }

        $expediente->load(['presupuestos', 'usuario', 'historiales.usuario']);

        $capturista = $expediente->usuario;
        $institucionId = $capturista?->institucion_id;

        $entidad = (string)($expediente->entidad ?? '8300');
        $year = (int)$expediente->anio_ejercicio;

        $coincideAsignacion = false;
        $motivoNoCoincide = null;

        if (!$capturista) {
            $motivoNoCoincide = 'No se encontró el capturista asociado al expediente.';
        } elseif (!$institucionId) {
            $motivoNoCoincide = 'El capturista no tiene institución asignada.';
        } else {
            $coincideAsignacion = \App\Models\FaspAsignacionInstitucion::query()
                ->activas()
                ->where('institucion_id', $institucionId)
                ->where('year', $year)
                ->where('entidad', $entidad)
                ->where('nivel', 3)
                ->where('eje', (string)$expediente->eje)
                ->where('programa', (string)$expediente->programa)
                ->where('subprograma', (string)$expediente->subprograma)
                ->exists();

            if (! $coincideAsignacion) {
                $motivoNoCoincide = 'La estructura (Eje/Programa/Subprograma) no está asignada a la institución del capturista para ese año.';
            }
        }

        $institucionNombre = $capturista?->institucion?->nombre ?? null;

        $observacionSugerida = null;
        if (! $coincideAsignacion) {
            $instNombre = $institucionNombre ?? 'la institución del capturista';
            $observacionSugerida =
                "Expediente fuera de asignación FASP.\n".
                "Institución: {$instNombre}\n".
                "Año: {$expediente->anio_ejercicio} | Entidad: ".($expediente->entidad ?? '8300')."\n".
                "Eje/Programa/Subprograma: ".($expediente->eje ?? '-')."/".($expediente->programa ?? '-')."/".($expediente->subprograma ?? '-')."\n\n".
                "Acción solicitada: Ajustar el expediente a los subprogramas asignados a la institución o solicitar reasignación al validador.";
        }

        // === Asignaciones para mostrar en UI ===
        $asignacionesInstitucion = collect();
        $subprogramasAsignados = collect();
        $resumenAsignaciones = collect();

        if ($institucionId) {
            $asignacionesInstitucion = \App\Models\FaspAsignacionInstitucion::query()
                ->activas()
                ->where('institucion_id', $institucionId)
                ->where('year', $year)
                ->where('entidad', $entidad)
                ->where('nivel', 3)
                ->get(['eje','programa','subprograma']);

            // Subprogramas SOLO del eje/programa del expediente
            $subprogramasAsignados = $asignacionesInstitucion
                ->where('eje', (string)$expediente->eje)
                ->where('programa', (string)$expediente->programa)
                ->pluck('subprograma')
                ->filter()
                ->unique()
                ->sort()
                ->values();

            // Resumen completo: Eje/Programa -> subprogramas
            if ($asignacionesInstitucion->isNotEmpty()) {
                $resumenAsignaciones = $asignacionesInstitucion
                    ->groupBy(fn($a) => (string)$a->eje . '|' . (string)$a->programa)
                    ->map(function ($items) {
                        $first = $items->first();

                        return [
                            'eje' => (string)$first->eje,
                            'programa' => (string)$first->programa,
                            'subprogramas' => $items->pluck('subprograma')
                                ->filter()
                                ->unique()
                                ->sort()
                                ->values(),
                        ];
                    })
                    ->values()
                    ->sortBy(fn($x) => $x['eje'].'|'.$x['programa'])
                    ->values();
            }
        }

        return view('validador.expedientes.show', compact(
            'expediente',
            'coincideAsignacion',
            'motivoNoCoincide',
            'observacionSugerida',
            'asignacionesInstitucion',
            'subprogramasAsignados',
            'institucionNombre',
            'resumenAsignaciones'
        ));
    }

    /**
     * Decidir: aprobar o rechazar.
     */
    public function decidir(Request $request, Expediente $expediente)
    {
        abort_if(! $expediente->estaEnValidacion(), 403, 'Solo se pueden decidir expedientes en estado "En validación".');

        $user = Auth::user();

        // Recalcular coincidencia con asignación por institución del capturista
        $capturista = $expediente->usuario()->first();
        $institucionId = $capturista?->institucion_id;
        $entidad = (string)($expediente->entidad ?? '8300');
        $year = (int)$expediente->anio_ejercicio;

        $coincideAsignacion = false;
        if ($institucionId) {
            $coincideAsignacion = FaspAsignacionInstitucion::query()
                ->activas()
                ->where('institucion_id', $institucionId)
                ->where('year', $year)
                ->where('entidad', $entidad)
                ->where('nivel', 3)
                ->where('eje', (string)$expediente->eje)
                ->where('programa', (string)$expediente->programa)
                ->where('subprograma', (string)$expediente->subprograma)
                ->exists();
        }

        // Validación base
        $validated = $request->validate([
            'decision' => ['required', 'in:aprobar,rechazar'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ], [
            'decision.required' => 'Debes seleccionar una decisión (Aprobar o Rechazar).',
            'decision.in' => 'La decisión seleccionada no es válida.',
            'observaciones.max' => 'Las observaciones no pueden exceder 2000 caracteres.',
        ]);

        // Si rechaza, observaciones obligatorias
        if ($validated['decision'] === 'rechazar') {
            $request->validate([
                'observaciones' => ['required', 'string', 'min:10', 'max:2000'],
            ], [
                'observaciones.required' => 'Para rechazar el expediente, debes escribir observaciones (motivo del rechazo).',
                'observaciones.min' => 'Las observaciones deben tener al menos 10 caracteres.',
            ]);
        }

        // Regla fuerte: NO aprobar si está fuera de asignación
        if ($validated['decision'] === 'aprobar' && ! $coincideAsignacion) {
            return back()
                ->withInput()
                ->withErrors(['decision' => 'No se puede aprobar: el expediente está fuera de asignación FASP para la institución del capturista.']);
        }

        DB::transaction(function () use ($expediente, $user, $validated) {
            $estadoAnterior = $expediente->estatus;

            if ($validated['decision'] === 'aprobar') {
                $estadoNuevo = Expediente::ESTADO_APROBADO;
                $obs = $validated['observaciones'] ?? 'Expediente aprobado por el validador.';
            } else {
                $estadoNuevo = Expediente::ESTADO_RECHAZADO;
                $obs = $validated['observaciones']; // ya es obligatorio
            }

            $expediente->update(['estatus' => $estadoNuevo]);

            HistorialModificacion::create([
                'expediente_id'   => $expediente->id,
                'usuario_id'      => $user->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo'    => $estadoNuevo,
                'observaciones'   => $obs,
            ]);
        });

        return redirect()
            ->route('validador.expedientes.index')
            ->with('success', 'Decisión registrada correctamente.');
    }

    // ============================= HELPERS =============================

    private function validateExpediente(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            // Datos generales
            'nombre_proyecto' => 'required|string|max:255',
            'dependencia'     => 'required|string|max:255',
            // ya no se captura en create
            'tipo_recurso' => 'nullable|string|max:50',
            'anio_ejercicio'  => 'required|integer',

            // Estructura programática (OBLIGATORIA en este flujo)
            'entidad'         => 'nullable|string|max:10',
            'eje'             => 'required|string|max:10',
            'programa'        => 'required|string|max:10',
            'subprograma'     => 'required|string|max:10',
            'tema'            => 'nullable|string|max:255',
            'area_ejecutora'  => 'nullable|string|max:10',

            // Presupuesto
            'presupuestos'                             => 'nullable|array',
            'presupuestos.*.descripcion_concepto'      => 'nullable|string',
            'presupuestos.*.cantidad'                  => 'nullable|numeric',
            'presupuestos.*.unidad'                    => 'nullable|string|max:50',
            'presupuestos.*.precio_unitario'           => 'nullable|numeric',
            'presupuestos.*.subtotal'                  => 'nullable|numeric',
            'presupuestos.*.iva'                       => 'nullable|numeric',
            'presupuestos.*.total'                     => 'nullable|numeric',
            'presupuestos.*.partida'                   => 'nullable|string|max:100',

            // Nuevos campos presupuesto
            'presupuestos.*.capitulo'                  => 'nullable|string|max:10',
            'presupuestos.*.bien'                      => 'nullable|string|max:255',
            'presupuestos.*.persona'                   => 'nullable|string|max:255',
            'presupuestos.*.rlc'                       => 'nullable|string|max:10',
            'presupuestos.*.fasp_federal'              => 'nullable|numeric',
            'presupuestos.*.fasp_municipal'            => 'nullable|numeric',
            'presupuestos.*.fasp_subtotal'             => 'nullable|numeric',
            'presupuestos.*.est_estatal'               => 'nullable|numeric',
            'presupuestos.*.est_municipal'             => 'nullable|numeric',
            'presupuestos.*.est_subtotal'              => 'nullable|numeric',
            'presupuestos.*.total_financiamiento'      => 'nullable|numeric',

            //Catalogo
            'fasp_catalogo_id' => 'nullable|integer|exists:fasp_catalogo,id',
        ]);
    }

    private function guardarPresupuesto(Expediente $expediente, array $presupuestos): void
    {
        foreach ($presupuestos as $concepto) {
            $tieneDatos =
                !empty($concepto['descripcion_concepto']) ||
                !empty($concepto['cantidad']) ||
                !empty($concepto['precio_unitario']) ||
                !empty($concepto['fasp_federal']) ||
                !empty($concepto['est_estatal']);

            if (!$tieneDatos) continue;

            Presupuesto::create([
                'expediente_id'        => $expediente->id,
                'capitulo'             => $concepto['capitulo']             ?? null,
                'partida'              => $concepto['partida']              ?? null,
                'descripcion_concepto' => $concepto['descripcion_concepto'] ?? '',
                'bien'                 => $concepto['bien']                 ?? null,
                'cantidad'             => $concepto['cantidad']             ?? 0,
                'unidad'               => $concepto['unidad']               ?? null,
                'persona'              => $concepto['persona']              ?? null,
                'rlc'                  => $concepto['rlc']                  ?? null,
                'fasp_federal'         => $concepto['fasp_federal']         ?? 0,
                'fasp_municipal'       => $concepto['fasp_municipal']       ?? 0,
                'fasp_subtotal'        => $concepto['fasp_subtotal']        ?? 0,
                'est_estatal'          => $concepto['est_estatal']          ?? 0,
                'est_municipal'        => $concepto['est_municipal']        ?? 0,
                'est_subtotal'         => $concepto['est_subtotal']         ?? 0,
                'total_financiamiento' => $concepto['total_financiamiento'] ?? 0,
                'precio_unitario'      => $concepto['precio_unitario']      ?? 0,
                'subtotal'             => $concepto['subtotal']             ?? 0,
                'iva'                  => $concepto['iva']                  ?? 0,
                'total'                => $concepto['total']                ?? 0,
            ]);
        }
    }

    private function asignacionesInstitucion(int $institucionId, int $year, string $entidad)
    {
        return FaspAsignacionInstitucion::query()
            ->activas()
            ->where('institucion_id', $institucionId)
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->get(['eje','programa','subprograma']);
    }

    private function assertAsignacionInstitucion(
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

        abort_if(!$ok, 403, 'Tu institución no tiene asignado ese Eje/Programa/Subprograma.');
    }

    /**
 * Construye mapas de nombres desde fasp_catalogo.nombre:
 * - eje => nombre (nivel 1)
 * - eje|programa => nombre (nivel 2)
 * - eje|programa|subprograma => ['id'=>catalogo_id, 'nombre'=>...] (nivel 3)
 *
 * Usa parent_id para subir a programa/eje.
 */
    private function buildCatalogoNombreMaps(int $year, string $entidad, $asig): array
    {
        // Tomamos los combos asignados para reducir consulta
        $ejes = $asig->pluck('eje')->filter()->unique()->values()->all();

        $pairs = $asig->map(fn($x) => [(string)$x->eje, (string)$x->programa])
            ->unique(fn($p) => $p[0].'|'.$p[1])
            ->values()
            ->all();

        // Traemos solo subprogramas asignados en el catálogo (nivel 3)
        $q = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3);

        // filtrar por ejes
        if (count($ejes)) {
            $q->whereIn('eje', $ejes);
        }

        // filtrar por pares eje-programa
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

        foreach ($subs as $row) {
            $eje = (string)$row->eje;
            $programa = (string)$row->programa;
            $subprograma = (string)$row->subprograma;

            // Nombre subprograma (nivel 3)
            if ($eje !== '' && $programa !== '' && $subprograma !== '') {
                $k = "{$eje}|{$programa}|{$subprograma}";
                $mapNombresSubprograma[$k] = [
                    'id' => $row->id,
                    'nombre' => (string)($row->nombre ?? ''),
                ];
            }

            // Nombre programa (nivel 2) (vía parent)
            if ($row->parent) {
                $kp = "{$eje}|{$programa}";
                if (!isset($mapNombresPrograma[$kp])) {
                    $mapNombresPrograma[$kp] = (string)($row->parent->nombre ?? '');
                }
            }

            // Nombre eje (nivel 1) (vía parent->parent)
            if ($row->parent && $row->parent->parent) {
                if (!isset($mapNombresEje[$eje])) {
                    $mapNombresEje[$eje] = (string)($row->parent->parent->nombre ?? '');
                }
            }
        }

        return [$mapNombresEje, $mapNombresPrograma, $mapNombresSubprograma];
    }

    //Helper 1: nombres eje/programa/subprograma (niveles 1–3)
    private function buildNombreMapsNivel1a3(int $year, string $entidad, $asig): array
    {
        // Traer SOLO subprogramas asignados en catálogo (nivel 3)
        $q = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3);

        // Filtrar por pares eje+programa asignados
        $pairs = $asig->map(fn($x) => [(string)$x->eje, (string)$x->programa])
            ->unique(fn($p) => $p[0].'|'.$p[1])
            ->values()
            ->all();

        $q->where(function ($w) use ($pairs) {
            foreach ($pairs as $p) {
                $w->orWhere(function ($x) use ($p) {
                    $x->where('eje', $p[0])->where('programa', $p[1]);
                });
            }
        });

        // Requiere que FaspCatalogo tenga parent() y parent->parent
        $subs = $q->with([
                'parent:id,year,entidad,nivel,parent_id,eje,programa,nombre', // programa (nivel 2)
                'parent.parent:id,year,entidad,nivel,eje,nombre'              // eje (nivel 1)
            ])
            ->get(['id','eje','programa','subprograma','nombre','parent_id']);

        $mapEje = [];
        $mapProg = [];
        $mapSub = [];

        foreach ($subs as $row) {
            $eje = (string)$row->eje;
            $prog = (string)$row->programa;
            $sub = (string)$row->subprograma;

            $mapSub["{$eje}|{$prog}|{$sub}"] = [
                'id' => $row->id,
                'nombre' => (string)($row->nombre ?? ''),
            ];

            if ($row->parent) {
                $mapProg["{$eje}|{$prog}"] = (string)($row->parent->nombre ?? '');
            }

            if ($row->parent && $row->parent->parent) {
                $mapEje[$eje] = (string)($row->parent->parent->nombre ?? '');
            }
        }

        return [$mapEje, $mapProg, $mapSub];
    }

    //Helper 2: maps para Capítulo → Concepto → Partida Genérica → Bien (niveles 4–8)
    private function buildMapsNivel4a8(int $year, string $entidad, $asig): array
    {
        // Traemos TODO lo que cuelga de los subprogramas asignados (nivel 3) hacia abajo.
        // Para no explotar: filtramos por eje+programa+subprograma asignados.
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
            ->whereIn('nivel', [4,5,6,7,8]) // capitulo..bien
            ->where(function ($w) use ($triples) {
                foreach ($triples as $t) {
                    $w->orWhere(function ($x) use ($t) {
                        $x->where('eje', $t[0])
                        ->where('programa', $t[1])
                        ->where('subprograma', $t[2]);
                    });
                }
            });

        $rows = $q->get([
            'nivel','eje','programa','subprograma',
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

        foreach ($rows as $r) {
            $eje = (string)$r->eje;
            $prog = (string)$r->programa;
            $sub = (string)$r->subprograma;

            $cap = (string)$r->capitulo;
            $con = (string)$r->concepto;
            $pg  = (string)$r->partida_generica;
            $bien = (string)$r->bien;

            if ((int)$r->nivel === 4 && $cap !== '') {
                $capitulosPorEPS["{$eje}|{$prog}|{$sub}"][] = $cap;
                $mapNombresCapitulo[$cap] = $mapNombresCapitulo[$cap] ?? (string)($r->nombre ?? '');
            }

            if ((int)$r->nivel === 5 && $cap !== '' && $con !== '') {
                $conceptosPorEPSC["{$eje}|{$prog}|{$sub}|{$cap}"][] = $con;
                $mapNombresConcepto["{$eje}|{$prog}|{$sub}|{$cap}|{$con}"] = (string)($r->nombre ?? '');
            }

            if ((int)$r->nivel === 6 && $cap !== '' && $con !== '' && $pg !== '') {
                $partidasGenPorEPSCC["{$eje}|{$prog}|{$sub}|{$cap}|{$con}"][] = $pg;
                $mapNombresPartidaGenerica["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}"] = (string)($r->nombre ?? '');
            }

            // Nivel 7/8 depende de tu catálogo; como tú usarás "Bien" como partida específica:
            if (in_array((int)$r->nivel, [7,8], true) && $cap !== '' && $con !== '' && $pg !== '' && $bien !== '') {
                $bienesPorEPSCCP["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}"][] = $bien;
                $mapNombresBien["{$eje}|{$prog}|{$sub}|{$cap}|{$con}|{$pg}|{$bien}"] = (string)($r->nombre ?? '');
            }
        }

        // normalizar (unique+sort)
        foreach ($capitulosPorEPS as $k => $arr) $capitulosPorEPS[$k] = collect($arr)->unique()->sort()->values()->all();
        foreach ($conceptosPorEPSC as $k => $arr) $conceptosPorEPSC[$k] = collect($arr)->unique()->sort()->values()->all();
        foreach ($partidasGenPorEPSCC as $k => $arr) $partidasGenPorEPSCC[$k] = collect($arr)->unique()->sort()->values()->all();
        foreach ($bienesPorEPSCCP as $k => $arr) $bienesPorEPSCCP[$k] = collect($arr)->unique()->sort()->values()->all();

        return [
            $capitulosPorEPS,
            $conceptosPorEPSC,
            $partidasGenPorEPSCC,
            $bienesPorEPSCCP,
            $mapNombresCapitulo,
            $mapNombresConcepto,
            $mapNombresPartidaGenerica,
            $mapNombresBien,
        ];
    }

}
