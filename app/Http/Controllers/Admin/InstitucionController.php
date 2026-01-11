<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstitucionController extends Controller
{
    public function index()
    {
        return view('admin.instituciones.index', [
            'instituciones' => Institucion::orderBy('orden')->orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.instituciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'siglas' => 'nullable|string|max:50',
        ]);

        $max = (int) Institucion::max('orden');
        $orden = $max + 1;

        Institucion::create([
            'nombre' => $request->nombre,
            'siglas' => $request->siglas,
            'orden'  => $orden,
        ]);

        return redirect()->route('admin.instituciones.index')
            ->with('success', 'Dependencia creada correctamente.');
    }

    public function edit(Institucion $institucion)
    {
        return view('admin.instituciones.edit', compact('institucion'));
    }

    public function update(Request $request, Institucion $institucion)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'siglas' => 'nullable|string|max:50',
        ]);

        $institucion->update($request->only('nombre', 'siglas'));

        return redirect()->route('admin.instituciones.index')
            ->with('success', 'Dependencia actualizada correctamente.');
    }

    public function destroy(Institucion $institucion)
    {
        $ordenBorrado = $institucion->orden;
        $institucion->delete();

        Institucion::where('orden', '>', $ordenBorrado)->decrement('orden');

        return redirect()->route('admin.instituciones.index')
            ->with('success', 'Dependencia eliminada correctamente.');
    }

    public function updateOrden(Request $request, Institucion $institucione)
    {
        $request->validate([
            'orden' => 'required|integer|min:0|max:9999',
        ]);

        $ocupado = Institucion::where('orden', $request->orden)
            ->where('id', '!=', $institucione->id)
            ->exists();

        if ($ocupado) {
            return back()
                ->withErrors(['orden' => 'El número de orden ya está ocupado. Elige otro.'])
                ->withInput();
        }

        $institucione->orden = (int) $request->orden;
        $institucione->save();

        return back()->with('success', 'Orden actualizado correctamente.');
    }

    public function updateOrdenBatch(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required','array','min:1'],
            'ids.*' => ['integer','exists:instituciones,id'],
        ]);

        $ids = array_values(array_unique($data['ids']));

        DB::transaction(function () use ($ids) {

            // Temporal para evitar choques con unique(orden)
            $max = (int) DB::table('instituciones')->max('orden');
            $tempBase = $max + 1000;

            // 1) Asignar temporales distintos
            foreach ($ids as $i => $id) {
                DB::table('instituciones')
                    ->where('id', $id)
                    ->update(['orden' => $tempBase + $i]);
            }

            foreach ($ids as $i => $id) {
                DB::table('instituciones')
                    ->where('id', $id)
                    ->update(['orden' => $i + 1]);
            }
        });

        return back()->with('success', 'Orden de dependencias guardado correctamente.');
    }
}
