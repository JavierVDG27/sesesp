<?php

namespace App\Http\Controllers\Validador;

use App\Http\Controllers\Controller;
use App\Models\FaspCatalogo;
use App\Models\FaspAsignacionInstitucion;
use App\Models\Institucion;
use Illuminate\Http\Request;

class FaspAsignacionesInstitucionController extends Controller
{
   public function index(Request $request)
    {
        $year    = (int)($request->get('year', now()->year));
        $entidad = (string)($request->get('entidad', '8300'));
        $eje     = $request->get('eje');
        $programa= $request->get('programa');

        // Catálogo (nivel 3 = subprograma) + filtros
        $q = \App\Models\FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->with([
                'parent:id,year,entidad,nivel,parent_id,eje,programa,nombre', // programa
                'parent.parent:id,year,entidad,nivel,eje,nombre'              // eje
            ])
            ->select([
                'id','year','entidad','nivel','parent_id',
                'eje','programa','subprograma','nombre',
                'fed_federal','fed_municipal','est_estatal','est_municipal'
            ]);

        if ($eje)      $q->where('eje', $eje);
        if ($programa) $q->where('programa', $programa);

        $rows = $q->orderBy('eje')->orderBy('programa')->orderBy('subprograma')
            ->paginate(25)
            ->withQueryString();

        // Instituciones para el select
        $instituciones = \App\Models\Institucion::orderBy('nombre')->get(['id','nombre']);

        // Asignaciones activas (multi-institución) agrupadas por key
        $asignaciones = \App\Models\FaspAsignacionInstitucion::query()
            ->activas()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->with(['institucion:id,nombre'])
            ->get(['id','eje','programa','subprograma','institucion_id']);

        $map = $asignaciones->groupBy(fn($a) => "{$a->eje}|{$a->programa}|{$a->subprograma}");

        // ===== RESUMEN DE DISTRIBUCIONES (para mostrar Parcial/Completa/Excede) =====
        $distribSum = \App\Models\FaspDistribucion::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->selectRaw('eje, programa, subprograma, fuente, SUM(monto) as total')
            ->groupBy('eje','programa','subprograma','fuente')
            ->get()
            ->groupBy(fn($x) => "{$x->eje}|{$x->programa}|{$x->subprograma}")
            ->map(function ($items) {
                // devuelvo tipo array/colección de fuente => total
                return $items->pluck('total', 'fuente')->map(fn($v) => (float)$v);
            });

        // ===== LOCKS SOLO SI ESTÁ BLOQUEADO (locked_at NOT NULL) =====
        $locks = \App\Models\FaspDistribucionLock::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->whereNotNull('locked_at')
            ->get()
            ->keyBy(fn($l) => "{$l->eje}|{$l->programa}|{$l->subprograma}");

        // Para filtros
        $ejes = \App\Models\FaspCatalogo::where('year', $year)->where('entidad', $entidad)
            ->where('nivel', 3)
            ->select('eje')->distinct()->orderBy('eje')->pluck('eje');

        $programas = collect();
        if ($eje) {
            $programas = \App\Models\FaspCatalogo::where('year', $year)->where('entidad', $entidad)
                ->where('nivel', 3)->where('eje', $eje)
                ->select('programa')->distinct()->orderBy('programa')->pluck('programa');
        }

        return view('validador.fasp_asignaciones_institucion.index', compact(
            'year','entidad','eje','programa',
            'rows','instituciones','map','ejes','programas',
            'distribSum','locks'
        ));
    }

    public function asignar(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'entidad' => 'required|string|max:10',
            'eje' => 'required|string|max:10',
            'programa' => 'required|string|max:10',
            'subprograma' => 'required|string|max:10',
            'institucion_id' => 'required|exists:instituciones,id',
        ]);

        FaspAsignacionInstitucion::updateOrCreate(
            [
                'year' => (int)$data['year'],
                'entidad' => (string)$data['entidad'],
                'nivel' => 3,
                'eje' => (string)$data['eje'],
                'programa' => (string)$data['programa'],
                'subprograma' => (string)$data['subprograma'],
                'institucion_id' => (int)$data['institucion_id'],
            ],
            [
                'assigned_by' => auth()->id(),
                'active' => true,
            ]
        );

        return back()->with('success', 'Asignación guardada.');
    }

    public function quitar(FaspAsignacionInstitucion $asignacion)
    {
        $asignacion->update(['active' => false]);
        return back()->with('success', 'Asignación removida.');
    }
}
