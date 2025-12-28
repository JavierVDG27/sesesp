<?php

namespace App\Http\Controllers\Validador;

use App\Http\Controllers\Controller;
use App\Models\FaspCatalogo;
use App\Models\FaspDistribucion;
use App\Models\FaspDistribucionLock;
use App\Models\Institucion;
use Illuminate\Http\Request;

class FaspDistribucionesController extends Controller
{
    private function limitesDesdeCatalogo(int $year, string $entidad, string $eje, string $programa, string $subprograma): array
    {
        $row = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->where('eje', $eje)
            ->where('programa', $programa)
            ->where('subprograma', $subprograma)
            ->first(['fed_federal','fed_municipal','est_estatal','est_municipal']);

        return [
            'fed_federal'   => (float)($row?->fed_federal ?? 0),
            'fed_municipal' => (float)($row?->fed_municipal ?? 0),
            'est_estatal'   => (float)($row?->est_estatal ?? 0),
            'est_municipal' => (float)($row?->est_municipal ?? 0),
        ];
    }

    private function locked(int $year, string $entidad, string $eje, string $programa, string $subprograma): bool
    {
        $lock = FaspDistribucionLock::query()
            ->key($year, $entidad, $eje, $programa, $subprograma)
            ->first();

        return $lock?->estaBloqueado() ?? false;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'entidad' => 'required|string|max:10',
            'eje' => 'required|string|max:10',
            'programa' => 'required|string|max:10',
            'subprograma' => 'required|string|max:10',

            'fuente' => 'required|in:fed_federal,fed_municipal,est_estatal,est_municipal',
            'descripcion' => 'nullable|string|max:255',
            'institucion_id' => 'nullable|exists:instituciones,id',
            'monto' => 'required|numeric|min:0.01',
        ]);

        $year = (int)$data['year'];
        $entidad = (string)$data['entidad'];
        $eje = (string)$data['eje'];
        $programa = (string)$data['programa'];
        $subprograma = (string)$data['subprograma'];
        $fuente = (string)$data['fuente'];
        $montoNuevo = (float)$data['monto'];

        if ($this->locked($year,$entidad,$eje,$programa,$subprograma)) {
            return back()->with('error', 'La distribución está bloqueada. Desbloquea para modificar.');
        }

        $limites = $this->limitesDesdeCatalogo($year,$entidad,$eje,$programa,$subprograma);

        $actual = (float) FaspDistribucion::query()
            ->key($year,$entidad,$eje,$programa,$subprograma)
            ->where('fuente', $fuente)
            ->sum('monto');

        $max = (float)($limites[$fuente] ?? 0);

        if (($actual + $montoNuevo) > ($max + 0.00001)) {
            $restante = max(0, $max - $actual);
            return back()->with('error', "Te estás pasando. Restante disponible en {$fuente}: $".number_format($restante,2));
        }

        FaspDistribucion::create([
            'year' => $year,
            'entidad' => $entidad,
            'nivel' => 3,
            'eje' => $eje,
            'programa' => $programa,
            'subprograma' => $subprograma,
            'fuente' => $fuente,
            'descripcion' => $data['descripcion'] ?? null,
            'institucion_id' => $data['institucion_id'] ?? null,
            'monto' => $montoNuevo,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Monto agregado a la distribución.');
    }

    public function destroy(FaspDistribucion $dist)
    {
        if ($this->locked((int)$dist->year, (string)$dist->entidad, (string)$dist->eje, (string)$dist->programa, (string)$dist->subprograma)) {
            return back()->with('error', 'La distribución está bloqueada.');
        }

        $dist->delete();
        return back()->with('success', 'Renglón eliminado.');
    }

    public function lock(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'entidad' => 'required|string|max:10',
            'eje' => 'required|string|max:10',
            'programa' => 'required|string|max:10',
            'subprograma' => 'required|string|max:10',
        ]);

        $year = (int)$data['year'];
        $entidad = (string)$data['entidad'];
        $eje = (string)$data['eje'];
        $programa = (string)$data['programa'];
        $sub = (string)$data['subprograma'];

        $limites = $this->limitesDesdeCatalogo($year,$entidad,$eje,$programa,$sub);

        $sumas = FaspDistribucion::query()
            ->key($year,$entidad,$eje,$programa,$sub)
            ->selectRaw("fuente, SUM(monto) as total")
            ->groupBy('fuente')
            ->pluck('total','fuente')
            ->toArray();

        foreach ($limites as $fuente => $max) {
            $t = (float)($sumas[$fuente] ?? 0);
            if ($t + 0.00001 < (float)$max) {
                return back()->with('error', "Aún falta distribuir en {$fuente}. Restante: $".number_format($max-$t,2));
            }
        }

        FaspDistribucionLock::updateOrCreate(
            ['year'=>$year,'entidad'=>$entidad,'nivel'=>3,'eje'=>$eje,'programa'=>$programa,'subprograma'=>$sub],
            ['locked_at'=>now(),'locked_by'=>auth()->id()]
        );

        return back()->with('success', 'Distribución bloqueada.');
    }

    public function unlock(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'entidad' => 'required|string|max:10',
            'eje' => 'required|string|max:10',
            'programa' => 'required|string|max:10',
            'subprograma' => 'required|string|max:10',
        ]);

        FaspDistribucionLock::query()
            ->key((int)$data['year'], (string)$data['entidad'], (string)$data['eje'], (string)$data['programa'], (string)$data['subprograma'])
            ->update(['locked_at'=>null,'locked_by'=>null]);

        return back()->with('success', 'Distribución desbloqueada.');
    }
    public function edit(FaspCatalogo $row)
    {
        abort_if((int)$row->nivel !== 3, 404, 'Solo subprogramas.');

        $year    = (int)$row->year;
        $entidad = (string)$row->entidad;

        $instituciones = Institucion::orderBy('nombre')->get(['id','nombre']);

        $dists = FaspDistribucion::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->where('eje', (string)$row->eje)
            ->where('programa', (string)$row->programa)
            ->where('subprograma', (string)$row->subprograma)
            ->with(['institucion:id,nombre','creador:id,nombres,apellido_paterno,apellido_materno'])
            ->orderBy('fuente')
            ->latest()
            ->get();

        $lock = FaspDistribucionLock::query()
            ->where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 3)
            ->where('eje', (string)$row->eje)
            ->where('programa', (string)$row->programa)
            ->where('subprograma', (string)$row->subprograma)
            ->first();

        $locked = (bool)($lock && $lock->locked_at);

        $sum = $dists->groupBy('fuente')->map(fn($x) => (float)$x->sum('monto'));

        $lim = [
            'fed_federal'   => (float)($row->fed_federal ?? 0),
            'fed_municipal' => (float)($row->fed_municipal ?? 0),
            'est_estatal'   => (float)($row->est_estatal ?? 0),
            'est_municipal' => (float)($row->est_municipal ?? 0),
        ];

        return view('validador.fasp_distribuciones.edit', compact(
            'row','instituciones','dists','lock','locked','sum','lim'
        ));
    }
}
