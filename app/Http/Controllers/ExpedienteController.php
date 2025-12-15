<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Presupuesto;
use App\Models\HistorialModificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpedienteController extends Controller
{
    /**
     * Listado de expedientes del usuario autenticado.
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
     * Mostrar formulario para crear un nuevo expediente.
     */
    public function create()
    {
        return view('expedientes.create');
    }

    /**
     * Guardar un nuevo expediente en la BD.
     */
    public function store(Request $request)
    {
        $validated = $this->validateExpediente($request);

        return DB::transaction(function () use ($validated) {

            $expedienteData = collect($validated)->only([
                'nombre_proyecto',
                'dependencia',
                'tipo_recurso',
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
     * Formulario para editar un expediente.
     */
    public function edit(Expediente $expediente)
    {
        if ($expediente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este expediente.');
        }

        $expediente->load(['presupuestos', 'historiales.usuario']);

        return view('expedientes.edit', compact('expediente'));
    }

    /**
     * Actualizar expediente existente.
     */
    public function update(Request $request, Expediente $expediente)
    {
        if ($expediente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para actualizar este expediente.');
        }

        // 🔒 Bloqueo backend por estatus
        if (in_array($expediente->estatus, [Expediente::ESTADO_EN_VALIDACION, Expediente::ESTADO_APROBADO])) {
            return redirect()
                ->route('expedientes.edit', $expediente)
                ->with('error', 'No puedes modificar este expediente porque está en validación o aprobado.');
        }

        $validated = $this->validateExpediente($request, true);

        return DB::transaction(function () use ($validated, $expediente) {

            $expData = collect($validated)->only([
                'nombre_proyecto',
                'dependencia',
                'tipo_recurso',
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

    // ---------------------------- Flujo de validación ------------------------

    /**
     * Enviar un expediente a estado "en_validacion".
     */
    public function enviarValidacion(Expediente $expediente)
    {
        $user = Auth::user();

        if ($expediente->user_id !== $user->id) {
            abort(403, 'No tienes permiso para enviar este expediente.');
        }

        if (! $expediente->esBorrador() && ! $expediente->estaRechazado()) {
            return redirect()
                ->route('expedientes.edit', $expediente)
                ->with('error', 'Solo se pueden enviar expedientes en estado Borrador o Rechazado.');
        }

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
     * Lista de expedientes en estado "en_validacion" para el validador.
     */
    public function listaEnValidacion()
    {
        $expedientes = Expediente::conEstado(Expediente::ESTADO_EN_VALIDACION)
            ->with('usuario')
            ->latest()
            ->paginate(10);

        return view('validador.expedientes.index', compact('expedientes'));
    }

    /**
     * Mostrar el detalle de un expediente para revisión del validador.
     */
    public function mostrarParaRevision(Expediente $expediente)
    {
        if (! $expediente->estaEnValidacion()) {
            abort(403, 'Solo se pueden revisar expedientes en estado "En validación".');
        }

        $expediente->load(['presupuestos', 'usuario', 'historiales.usuario']);

        return view('validador.expedientes.show', compact('expediente'));
    }

    /**
     * Decisión del validador sobre un expediente: aprobar o rechazar.
     */
    public function decidir(Request $request, Expediente $expediente)
    {
        if (! $expediente->estaEnValidacion()) {
            abort(403, 'Solo se pueden decidir expedientes en estado "En validación".');
        }

        $user = Auth::user();

        // 1) Validación con mensajes amigables
        $validated = $request->validate([
            'decision' => ['required', 'in:aprobar,rechazar'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ], [
            'decision.required' => 'Debes seleccionar una decisión (Aprobar o Rechazar).',
            'decision.in' => 'La decisión seleccionada no es válida.',
            'observaciones.max' => 'Las observaciones no pueden exceder 2000 caracteres.',
        ]);

        // 2) Regla: si rechaza, observaciones obligatorias (con mensaje claro)
        if ($validated['decision'] === 'rechazar') {
            $request->validate([
                'observaciones' => ['required', 'string', 'min:10', 'max:2000'],
            ], [
                'observaciones.required' => 'Para rechazar el expediente, debes escribir observaciones (motivo del rechazo).',
                'observaciones.min' => 'Las observaciones deben tener al menos 10 caracteres para que el capturista entienda qué corregir.',
            ]);
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
            $expediente->save();

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


    // ---------------------------- Destroy ------------------------

    public function destroy(Expediente $expediente)
    {
        if ($expediente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar este expediente.');
        }

        $expediente->presupuestos()->delete();
        $expediente->delete();

        return redirect()
            ->route('expedientes.index')
            ->with('success', 'Expediente eliminado correctamente.');
    }

    // ============================ Helpers ============================

    private function validateExpediente(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            // Datos generales
            'nombre_proyecto' => 'required|string|max:255',
            'dependencia'     => 'required|string|max:255',
            'tipo_recurso'    => 'required|string|max:50',
            'anio_ejercicio'  => 'required|integer',

            // Estructura programática
            'entidad'         => 'nullable|string|max:10',
            'eje'             => 'nullable|string|max:5',
            'programa'        => 'nullable|string|max:5',
            'subprograma'     => 'nullable|string|max:5',
            'tema'            => 'nullable|string|max:255',
            'area_ejecutora'  => 'nullable|string|max:5',

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
        ]);
    }

    private function guardarPresupuesto(Expediente $expediente, array $presupuestos): void
    {
        foreach ($presupuestos as $concepto) {
            $tieneDatos =
                !empty($concepto['descripcion_concepto']) ||
                !empty($concepto['cantidad']) ||
                !empty($concepto['precio_unitario']);

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
}
