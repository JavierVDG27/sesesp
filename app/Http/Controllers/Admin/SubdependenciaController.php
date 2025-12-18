<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\Subdependencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubdependenciaController extends Controller
{
    public function index()
    {
        $subdependencias = Subdependencia::query()
            ->with('institucion')
            ->select('subdependencias.*')
            ->selectSub(function ($q) {
                $q->from('subdependencias as s2')
                    ->select('s2.id')
                    ->whereColumn('s2.institucion_id', 'subdependencias.institucion_id')
                    ->whereColumn('s2.orden', '<', 'subdependencias.orden')
                    ->orderBy('s2.orden', 'desc')
                    ->limit(1);
            }, 'prev_id')
            ->selectSub(function ($q) {
                $q->from('subdependencias as s2')
                    ->select('s2.id')
                    ->whereColumn('s2.institucion_id', 'subdependencias.institucion_id')
                    ->whereColumn('s2.orden', '>', 'subdependencias.orden')
                    ->orderBy('s2.orden', 'asc')
                    ->limit(1);
            }, 'next_id')
            ->orderBy('institucion_id')
            ->orderBy('orden')
            ->paginate(10);

        return view('admin.subdependencias.index', compact('subdependencias'));
    }


    public function create()
    {
        return view('admin.subdependencias.create', [
            'instituciones' => Institucion::orderBy('nombre')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'institucion_id' => 'required|exists:instituciones,id',
            'nombre' => 'required|string|max:255',
            'siglas' => 'nullable|string|max:50',
        ]);

        $institucionId = (int) $request->institucion_id;

        $maxOrden = Subdependencia::where('institucion_id', $institucionId)->max('orden');
        $orden = ($maxOrden ?? 0) + 1;

        Subdependencia::create([
            'institucion_id' => $institucionId,
            'nombre' => $request->nombre,
            'siglas' => $request->siglas,
            'orden' => $orden,
        ]);

        return redirect()->route('admin.subdependencias.index')
            ->with('success', 'Subdependencia creada correctamente.');
    }

    public function edit(Subdependencia $subdependencia)
    {
        return view('admin.subdependencias.edit', [
            'subdependencia' => $subdependencia,
            'instituciones' => Institucion::orderBy('nombre')->get()
        ]);
    }

    public function update(Request $request, Subdependencia $subdependencia)
    {
        $request->validate([
            'institucion_id' => 'required|exists:instituciones,id',
            'nombre' => 'required|string|max:255',
            'siglas' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $subdependencia) {
            $oldInstitucion = $subdependencia->institucion_id;
            $newInstitucion = (int) $request->institucion_id;

            // si cambia de institución: lo “saco” del orden anterior y lo agrego al final del nuevo
            if ($oldInstitucion !== $newInstitucion) {
                $oldOrden = $subdependencia->orden;

                // compactar los que estaban abajo en la institución vieja
                Subdependencia::where('institucion_id', $oldInstitucion)
                    ->where('orden', '>', $oldOrden)
                    ->decrement('orden');

                $maxOrden = Subdependencia::where('institucion_id', $newInstitucion)->max('orden');
                $subdependencia->orden = ($maxOrden ?? 0) + 1;
                $subdependencia->institucion_id = $newInstitucion;
            }

            $subdependencia->nombre = $request->nombre;
            $subdependencia->siglas = $request->siglas;
            $subdependencia->save();
        });

        return redirect()->route('admin.subdependencias.index')
            ->with('success', 'Subdependencia actualizada correctamente.');
    }

    public function destroy(Subdependencia $subdependencia)
    {
        DB::transaction(function () use ($subdependencia) {
            $inst = $subdependencia->institucion_id;
            $ord = $subdependencia->orden;

            $subdependencia->delete();

            // compactar orden en esa institución
            Subdependencia::where('institucion_id', $inst)
                ->where('orden', '>', $ord)
                ->decrement('orden');
        });

        return redirect()->route('admin.subdependencias.index')
            ->with('success', 'Subdependencia eliminada correctamente.');
    }

    // =========================
    // MOVER ARRIBA / ABAJO
    // =========================
    public function moveUp(Subdependencia $subdependencia)
    {
        DB::transaction(function () use ($subdependencia) {
            $prev = Subdependencia::where('institucion_id', $subdependencia->institucion_id)
                ->where('orden', '<', $subdependencia->orden)
                ->orderBy('orden', 'desc')
                ->lockForUpdate()
                ->first();

            if (!$prev) return;

            $currentOrden = $subdependencia->orden;
            $prevOrden    = $prev->orden;

            // usar valor temporal que no choque (0 funciona si orden empieza en 1)
            $subdependencia->orden = 0;
            $subdependencia->save();

            $prev->orden = $currentOrden;
            $prev->save();

            $subdependencia->orden = $prevOrden;
            $subdependencia->save();
        });

        return back()->with('success', 'Orden actualizado.');
    }

    public function moveDown(Subdependencia $subdependencia)
    {
        DB::transaction(function () use ($subdependencia) {
            $next = Subdependencia::where('institucion_id', $subdependencia->institucion_id)
                ->where('orden', '>', $subdependencia->orden)
                ->orderBy('orden', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$next) return;

            $currentOrden = $subdependencia->orden;
            $nextOrden    = $next->orden;

            // valor temporal
            $subdependencia->orden = 0;
            $subdependencia->save();

            $next->orden = $currentOrden;
            $next->save();

            $subdependencia->orden = $nextOrden;
            $subdependencia->save();
        });

        return back()->with('success', 'Orden actualizado.');
    }
}
