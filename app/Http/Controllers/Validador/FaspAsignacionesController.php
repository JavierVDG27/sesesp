<?php

namespace App\Http\Controllers\Validador;

use App\Http\Controllers\Controller;
use App\Models\FaspAsignacion;
use App\Models\FaspCatalogo;
use App\Models\User;
use Illuminate\Http\Request;

class FaspAsignacionesController extends Controller
{
    public function index(Request $request)
    {
        $year = (int)($request->get('year', now()->year));
        $entidad = $request->get('entidad', '8300');

        // Capturistas activos
        $capturistas = User::whereHas('role', fn($q) => $q->where('name', 'capturista'))
            ->where('activo', true)
            ->orderBy('nombres')
            ->get();

        // Ejes disponibles
        $ejes = FaspCatalogo::where('year', $year)->where('entidad', $entidad)
            ->select('eje')->distinct()->orderBy('eje')->pluck('eje');

        // Asignaciones actuales
        $asignaciones = FaspAsignacion::with(['capturista','asignador'])
            ->yearEntidad($year, $entidad)
            ->activas()
            ->orderBy('eje')->orderBy('programa')->orderBy('subprograma')
            ->get();

        return view('validador.fasp_asignaciones.index', compact(
            'year','entidad','capturistas','ejes','asignaciones'
        ));
    }

    /**
     * Asigna:
     * - modo=subprograma: eje+programa + subprogramas[]
     * - modo=programa: eje+programa (asigna TODOS los subprogramas del programa)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'entidad' => 'required|string|max:10',

            'user_id' => 'required|exists:users,id',

            'eje' => 'required|string|max:10',
            'programa' => 'required|string|max:10',

            'modo' => 'required|in:programa,subprograma',
            'subprogramas' => 'array',
            'subprogramas.*' => 'string|max:10',
        ]);

        // asegurar que el usuario sea capturista
        $capturista = User::with('role')->findOrFail($data['user_id']);
        abort_if(($capturista->role->name ?? null) !== 'capturista', 422, 'El usuario seleccionado no es capturista.');

        $year = (int)$data['year'];
        $entidad = $data['entidad'];
        $eje = $data['eje'];
        $programa = $data['programa'];

        // resolver subprogramas objetivo
        if ($data['modo'] === 'programa') {
            $subprogramas = FaspCatalogo::where('year', $year)->where('entidad', $entidad)
                ->where('eje', $eje)->where('programa', $programa)
                ->select('subprograma')->distinct()->orderBy('subprograma')->pluck('subprograma')
                ->filter(fn($v) => !is_null($v) && $v !== '')
                ->values()
                ->all();
        } else {
            $subprogramas = collect($data['subprogramas'] ?? [])
                ->filter(fn($v) => !is_null($v) && $v !== '')
                ->unique()
                ->values()
                ->all();
        }

        if (count($subprogramas) === 0) {
            return back()->with('error', 'No hay subprogramas para asignar (revisa el catálogo o selecciona subprogramas).');
        }

        foreach ($subprogramas as $sub) {
            // upsert lógico: si existe, reactivar
            FaspAsignacion::updateOrCreate(
                [
                    'year' => $year,
                    'entidad' => $entidad,
                    'nivel' => 3,
                    'eje' => $eje,
                    'programa' => $programa,
                    'subprograma' => $sub,
                    'user_id' => $capturista->id,
                ],
                [
                    'assigned_by' => auth()->id(),
                    'active' => true,
                ]
            );
        }

        return back()->with('success', 'Asignación guardada correctamente.');
    }

    public function destroy(FaspAsignacion $asignacion)
    {
        // “desasignar” sin borrar: active=false
        $asignacion->update(['active' => false]);

        return back()->with('success', 'Asignación removida.');
    }

    /**
     * Endpoint JSON para selects dependientes (programas/subprogramas)
     */
    public function opciones(Request $request)
    {
        $year = (int)$request->get('year', now()->year);
        $entidad = $request->get('entidad', '8300');
        $eje = $request->get('eje');
        $programa = $request->get('programa');

        if ($eje && !$programa) {
            $programas = FaspCatalogo::where('year', $year)->where('entidad', $entidad)
                ->where('eje', $eje)
                ->select('programa')->distinct()->orderBy('programa')->pluck('programa');

            return response()->json(['programas' => $programas]);
        }

        if ($eje && $programa) {
            $subprogramas = FaspCatalogo::where('year', $year)->where('entidad', $entidad)
                ->where('eje', $eje)->where('programa', $programa)
                ->select('subprograma')->distinct()->orderBy('subprograma')->pluck('subprograma');

            return response()->json(['subprogramas' => $subprogramas]);
        }

        return response()->json([]);
    }
}
