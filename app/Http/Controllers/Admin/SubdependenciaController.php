<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\Subdependencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubdependenciaController extends Controller
{
    public function index(Request $request)
    {
        $institucionId = $request->integer('institucion_id');

        $instituciones = \App\Models\Institucion::orderBy('orden')->orderBy('nombre')->get();

        $q = Subdependencia::query()
            ->with('institucion')
            ->orderBy('orden');

        if ($institucionId) {
            $q->where('institucion_id', $institucionId);
        } else {
            $q->orderBy('institucion_id');
        }

        $subdependencias = $institucionId
            ? $q->get()
            : $q->paginate(10);

        return view('admin.subdependencias.index', compact('subdependencias', 'instituciones', 'institucionId'));
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
            //'siglas' => 'nullable|string|max:50',
        ]);

        $institucionId = (int) $request->institucion_id;

        $maxOrden = Subdependencia::where('institucion_id', $institucionId)->max('orden');
        $orden = ($maxOrden ?? 0) + 1;

        Subdependencia::create([
            'institucion_id' => $institucionId,
            'nombre' => $request->nombre,
            //'siglas' => $request->siglas,
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
            //'siglas' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $subdependencia) {
            $oldInstitucion = $subdependencia->institucion_id;
            $newInstitucion = (int) $request->institucion_id;

            if ($oldInstitucion !== $newInstitucion) {
                $oldOrden = $subdependencia->orden;

                Subdependencia::where('institucion_id', $oldInstitucion)
                    ->where('orden', '>', $oldOrden)
                    ->decrement('orden');

                $maxOrden = Subdependencia::where('institucion_id', $newInstitucion)->max('orden');
                $subdependencia->orden = ($maxOrden ?? 0) + 1;
                $subdependencia->institucion_id = $newInstitucion;
            }

            $subdependencia->nombre = $request->nombre;
            //$subdependencia->siglas = $request->siglas;
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

            $subdependencia->orden = 0;
            $subdependencia->save();

            $next->orden = $currentOrden;
            $next->save();

            $subdependencia->orden = $nextOrden;
            $subdependencia->save();
        });

        return back()->with('success', 'Orden actualizado.');
    }

    public function updateOrdenBatch(Request $request)
    {
        $data = $request->validate([
            'institucion_id' => ['required','integer','exists:instituciones,id'],
            'items' => ['required','array','min:1'],
            'items.*.id' => ['required','integer','exists:subdependencias,id'],
            'items.*.orden' => ['required','integer','min:1','max:9999'],
        ]);

        $instId = (int) $data['institucion_id'];

        DB::transaction(function () use ($data, $instId) {

            // asegurar que todos pertenezcan a esa institución
            $ids = collect($data['items'])->pluck('id')->values()->all();

            $count = Subdependencia::whereIn('id', $ids)
                ->where('institucion_id', $instId)
                ->lockForUpdate()
                ->count();

            if ($count !== count($ids)) {
                abort(422, 'Hay subdependencias que no pertenecen a la institución seleccionada.');
            }

            // temporales POSITIVOS (orden es unsigned) para evitar choque con unique(institucion_id, orden)
            $max = (int) Subdependencia::where('institucion_id', $instId)->max('orden');
            $tempBase = $max + 1000;

            foreach ($ids as $i => $id) {
                DB::table('subdependencias')->where('id', $id)->update(['orden' => $tempBase + $i]);
            }

            foreach ($data['items'] as $row) {
                DB::table('subdependencias')->where('id', $row['id'])->update(['orden' => (int) $row['orden']]);
            }
        });

        return back()->with('success', 'Orden de subdependencias guardado correctamente.');
    }

}
