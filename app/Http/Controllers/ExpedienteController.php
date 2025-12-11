<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Presupuesto;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    /**
     * Listado de expedientes del usuario autenticado.
     */
    public function index()
    {
        $user = auth()->user();

        $expedientes = Expediente::where('user_id', $user->id)
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
        $validated = $request->validate([
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
            'presupuestos'                        => 'nullable|array',
            'presupuestos.*.descripcion_concepto' => 'nullable|string',
            'presupuestos.*.cantidad'             => 'nullable|numeric',
            'presupuestos.*.unidad'               => 'nullable|string|max:50',
            'presupuestos.*.precio_unitario'      => 'nullable|numeric',
            'presupuestos.*.subtotal'             => 'nullable|numeric',
            'presupuestos.*.iva'                  => 'nullable|numeric',
            'presupuestos.*.total'                => 'nullable|numeric',
            'presupuestos.*.partida'              => 'nullable|string|max:100',
            // NUEVOS CAMPOS DEL PRESUPUESTO
            'presupuestos.*.capitulo'        => 'nullable|string|max:10',
            'presupuestos.*.bien'            => 'nullable|string|max:255',
            'presupuestos.*.persona'         => 'nullable|string|max:255',
            'presupuestos.*.rlc'             => 'nullable|string|max:10',
            'presupuestos.*.fasp_federal'    => 'nullable|numeric',
            'presupuestos.*.fasp_municipal'  => 'nullable|numeric',
            'presupuestos.*.fasp_subtotal'   => 'nullable|numeric',
            'presupuestos.*.est_estatal'     => 'nullable|numeric',
            'presupuestos.*.est_municipal'   => 'nullable|numeric',
            'presupuestos.*.est_subtotal'    => 'nullable|numeric',
            'presupuestos.*.total_financiamiento' => 'nullable|numeric',

        ]);

        // Datos del expediente
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
        $expedienteData['estatus'] = 'borrador';

        $expediente = Expediente::create($expedienteData);

        // Guardar conceptos de presupuesto
        $presupuestos = $validated['presupuestos'] ?? [];

        foreach ($presupuestos as $concepto) {
            $tieneDatos =
                !empty($concepto['descripcion_concepto']) ||
                !empty($concepto['cantidad']) ||
                !empty($concepto['precio_unitario']);

            if (!$tieneDatos) {
                continue;
            }

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

        return redirect()
            ->route('expedientes.edit', $expediente)
            ->with('success', 'Expediente creado en borrador y presupuesto guardado.');
    }

    /**
     * Formulario para editar un expediente.
     */
    public function edit(Expediente $expediente)
    {
        // Opcional pero recomendable: asegurar que el expediente es del usuario
        if ($expediente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este expediente.');
        }

        $expediente->load('presupuestos');

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

        $validated = $request->validate([
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
            'presupuestos'                        => 'nullable|array',
            'presupuestos.*.descripcion_concepto' => 'nullable|string',
            'presupuestos.*.cantidad'             => 'nullable|numeric',
            'presupuestos.*.unidad'               => 'nullable|string|max:50',
            'presupuestos.*.precio_unitario'      => 'nullable|numeric',
            'presupuestos.*.subtotal'             => 'nullable|numeric',
            'presupuestos.*.iva'                  => 'nullable|numeric',
            'presupuestos.*.total'                => 'nullable|numeric',
            'presupuestos.*.partida'              => 'nullable|string|max:100',
            // NUEVOS CAMPOS DEL PRESUPUESTO
            'presupuestos.*.capitulo'        => 'nullable|string|max:10',
            'presupuestos.*.bien'            => 'nullable|string|max:255',
            'presupuestos.*.persona'         => 'nullable|string|max:255',
            'presupuestos.*.rlc'             => 'nullable|string|max:10',
            'presupuestos.*.fasp_federal'    => 'nullable|numeric',
            'presupuestos.*.fasp_municipal'  => 'nullable|numeric',
            'presupuestos.*.fasp_subtotal'   => 'nullable|numeric',
            'presupuestos.*.est_estatal'     => 'nullable|numeric',
            'presupuestos.*.est_municipal'   => 'nullable|numeric',
            'presupuestos.*.est_subtotal'    => 'nullable|numeric',
            'presupuestos.*.total_financiamiento' => 'nullable|numeric',
        ]);

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
        $expediente->presupuestos()->delete();

        $presupuestos = $validated['presupuestos'] ?? [];

        foreach ($presupuestos as $concepto) {
            $tieneDatos =
                !empty($concepto['descripcion_concepto']) ||
                !empty($concepto['cantidad']) ||
                !empty($concepto['precio_unitario']);

            if (!$tieneDatos) {
                continue;
            }

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

        return redirect()
            ->route('expedientes.edit', $expediente)
            ->with('success', 'Expediente actualizado correctamente.');
    }

    /**
     * Eliminar expediente (y su presupuesto asociado).
     */
    public function destroy(Expediente $expediente)
    {
        if ($expediente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar este expediente.');
        }

        // Eliminar presupuestos asociados
        $expediente->presupuestos()->delete();

        // Eliminar expediente
        $expediente->delete();

        return redirect()
            ->route('expedientes.index')
            ->with('success', 'Expediente eliminado correctamente.');
    }
}
