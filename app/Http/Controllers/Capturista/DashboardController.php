<?php

namespace App\Http\Controllers\Capturista;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\FaspAsignacionInstitucion;
use App\Models\FaspCatalogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $year    = (int)($request->get('year', now()->year));
        $entidad = (string)($request->get('entidad', '8300'));

        if (!$user->institucion_id) {
            return view('capturista.dashboard', [
                'asignacionesCount' => 0,
                'asignacionesDisponibles' => collect(),
                'asignacionesTrabajadas' => collect(),
                'asignacionesCompartidas' => collect(),
                'expedientesCount' => 0,
            ])->with('error', 'Tu usuario no tiene institución asignada. Pide al admin asignarte una institución.');
        }

        $pad2 = fn($v) => str_pad((string)$v, 2, '0', STR_PAD_LEFT);

        // 1) Asignaciones activas de MI institución (nivel 3)
        $misAsignaciones = FaspAsignacionInstitucion::query()
            ->activas()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->where('institucion_id', (int)$user->institucion_id)
            ->get(['eje','programa','subprograma']);

        // Normalizar a 2 dígitos
        $misAsignaciones = $misAsignaciones->map(function ($a) use ($pad2) {
            $a->eje = $pad2($a->eje);
            $a->programa = $pad2($a->programa);
            $a->subprograma = $pad2($a->subprograma);
            return $a;
        });

        $asignacionesCount = $misAsignaciones
            ->unique(fn($a) => "{$a->eje}|{$a->programa}|{$a->subprograma}")
            ->count();

        // 2) Traer nombres del subprograma (nivel 3) para mostrar en dashboard
        $keys = $misAsignaciones->unique(fn($a) => "{$a->eje}|{$a->programa}|{$a->subprograma}")->values();

        $nombresSub = collect();
        if ($keys->isNotEmpty()) {
            $nombresSub = FaspCatalogo::query()
                ->where('year', $year)
                ->where('entidad', $entidad)
                ->where('nivel', 3)
                ->where(function ($w) use ($keys) {
                    foreach ($keys as $k) {
                        $w->orWhere(function ($q) use ($k) {
                            $q->where('eje', $k->eje)
                              ->where('programa', $k->programa)
                              ->where('subprograma', $k->subprograma);
                        });
                    }
                })
                ->get(['eje','programa','subprograma','nombre'])
                ->map(function ($r) use ($pad2) {
                    return [
                        'key' => $pad2($r->eje).'|'.$pad2($r->programa).'|'.$pad2($r->subprograma),
                        'nombre' => (string)($r->nombre ?? ''),
                    ];
                })
                ->pluck('nombre','key');
        }

        // 3) “En trabajo” = existe expediente para ese EPS (misma institución/año/entidad)
        $expedientesBase = Expediente::query()
            ->where('anio_ejercicio', $year)
            ->where('entidad', $entidad)
            ->where('institucion_id', (int)$user->institucion_id);

        $expedientesCount = (clone $expedientesBase)->count();

        // Traemos el primer expediente (para botón “Continuar”) por EPS
        $primerExpPorEPS = (clone $expedientesBase)
            ->orderByDesc('id')
            ->get(['id','eje','programa','subprograma'])
            ->map(function ($e) use ($pad2) {
                return [
                    'id' => $e->id,
                    'key' => $pad2($e->eje).'|'.$pad2($e->programa).'|'.$pad2($e->subprograma),
                ];
            })
            ->groupBy('key')
            ->map(fn($g) => $g->first()['id']);

        $asignacionesTrabajadas = [];
        $asignacionesDisponibles = [];

        foreach ($keys as $a) {
            $k = "{$a->eje}|{$a->programa}|{$a->subprograma}";
            $item = [
                'eje' => $a->eje,
                'programa' => $a->programa,
                'subprograma' => $a->subprograma,
                'subprograma_nombre' => $nombresSub[$k] ?? '',
                'expediente_id' => $primerExpPorEPS[$k] ?? null,
            ];

            if (!empty($primerExpPorEPS[$k])) {
                $asignacionesTrabajadas[] = $item;
            } else {
                $asignacionesDisponibles[] = $item;
            }
        }

        // 4) Compartidas = mismas llaves EPS donde hay más instituciones activas
        //    (mostrar nombres de las otras instituciones)
        $asignacionesCompartidas = [];
        if ($keys->isNotEmpty()) {
            $otras = FaspAsignacionInstitucion::query()
                ->activas()
                ->where('year', $year)
                ->where('entidad', $entidad)
                ->where('nivel', 3)
                ->where(function ($w) use ($keys) {
                    foreach ($keys as $k) {
                        $w->orWhere(function ($q) use ($k) {
                            $q->where('eje', $k->eje)
                              ->where('programa', $k->programa)
                              ->where('subprograma', $k->subprograma);
                        });
                    }
                })
                ->where('institucion_id', '!=', (int)$user->institucion_id)
                ->with('institucion:id,nombre')
                ->get(['eje','programa','subprograma','institucion_id']);

            $otrasMap = $otras->map(function ($r) use ($pad2) {
                $key = $pad2($r->eje).'|'.$pad2($r->programa).'|'.$pad2($r->subprograma);
                return [
                    'key' => $key,
                    'nombre' => (string)($r->institucion->nombre ?? ''),
                ];
            })->groupBy('key')
              ->map(fn($g) => $g->pluck('nombre')->filter()->unique()->values()->all());

            foreach ($keys as $a) {
                $k = "{$a->eje}|{$a->programa}|{$a->subprograma}";
                $otrasInst = $otrasMap[$k] ?? [];
                if (!empty($otrasInst)) {
                    $asignacionesCompartidas[] = [
                        'eje' => $a->eje,
                        'programa' => $a->programa,
                        'subprograma' => $a->subprograma,
                        'subprograma_nombre' => $nombresSub[$k] ?? '',
                        'otras_instituciones' => $otrasInst,
                    ];
                }
            }
        }

        return view('capturista.dashboard', [
            'asignacionesCount' => $asignacionesCount,
            'asignacionesDisponibles' => collect($asignacionesDisponibles),
            'asignacionesTrabajadas' => collect($asignacionesTrabajadas)->sortBy(fn($x) => $x['eje'].'|'.$x['programa'].'|'.$x['subprograma'])->values(),
            'asignacionesCompartidas' => collect($asignacionesCompartidas)->sortBy(fn($x) => $x['eje'].'|'.$x['programa'].'|'.$x['subprograma'])->values(),
            'expedientesCount' => $expedientesCount,
        ]);
    }
}