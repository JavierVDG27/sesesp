<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use Illuminate\Http\Request;

class InstitucionController extends Controller
{
    public function index()
    {
        return view('admin.instituciones.index', [
            'instituciones' => Institucion::orderBy('orden')->orderBy('nombre')->paginate(10),
        ]);
    }

    public function create()
    {

        // Obtener el último orden usado (máximo)
        $ultimoOrden = Institucion::max('orden');

        // Sugerir el siguiente número
        $sugerido = is_null($ultimoOrden) ? 1 : $ultimoOrden + 1;

        return view('admin.instituciones.create', compact('sugerido'));
    }

    public function store(Request $request)
    {
        $request->validate([
        'nombre' => 'required|string|max:255',
        'siglas' => 'nullable|string|max:50',
        'orden'  => 'nullable|integer|min:0|max:9999',
    ]);

    $orden = $request->orden ?? (Institucion::max('orden') + 1);

    if (Institucion::where('orden', $orden)->exists()) {
        return back()
            ->withErrors(['orden' => 'El número de orden ya está ocupado.'])
            ->withInput();
    }

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
            'orden'  => 'required|integer|min:0|max:9999',
        ]);

        // Validación de orden único (excluyendo la misma institución)
        $ocupado = Institucion::where('orden', $request->orden)
            ->where('id', '!=', $institucion->id)
            ->exists();

        if ($ocupado) {
            return back()
                ->withErrors(['orden' => 'El número de orden ya está ocupado. Elige otro.'])
                ->withInput();
        }

        $institucion->update($request->only('nombre', 'siglas', 'orden'));

        return redirect()->route('admin.instituciones.index')
            ->with('success', 'Dependencia actualizada correctamente.');
    }

    public function destroy(Institucion $institucion)
    {
        $institucion->delete();

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
}
