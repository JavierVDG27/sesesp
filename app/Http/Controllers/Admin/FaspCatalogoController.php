<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaspCatalogo;
use App\Services\FaspRollupService;
use App\Services\FaspTreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use App\Models\FaspAsignacion;
use App\Models\FaspAsignacionInstitucion;


class FaspCatalogoController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $entidad = '8300';

        // ======================
        // 1) Query principal (con filtros)
        // ======================
        $q = FaspCatalogo::query()
            ->where('year', $year)
            ->where('entidad', $entidad);

        foreach (['eje','programa','subprograma','capitulo','concepto','partida_generica','bien'] as $field) {
            $val = $request->input($field);
            if ($val !== null && $val !== '') {
                $q->where($field, $val);
            }
        }

        // Orden numérico real para evitar “saltos” por orden lexicográfico
        $rows = collect();

        if ($request->filled('eje')) {
            $rows = $q->orderBy('eje')
                ->orderBy('programa')
                ->orderBy('subprograma')
                ->orderByRaw('CAST(capitulo AS UNSIGNED), capitulo')
                ->orderByRaw('CAST(concepto AS UNSIGNED), concepto')
                ->orderByRaw('CAST(partida_generica AS UNSIGNED), partida_generica')
                ->orderByRaw('CAST(bien AS UNSIGNED), bien')
                ->get();
        } else {
            // solo raíces para que no se vea “vacío”
            $rows = FaspCatalogo::where('year',$year)->where('entidad',$entidad)
                ->where('nivel',1)->orderBy('eje')->get();
        }

        // ======================
        // 2) Summary total del año (raíces nivel 1)
        // ======================
        $summary = [
            'count' => (clone $q)->count(), // o FaspCatalogo::where(...)->count()
            'total_fed_federal' => (float) FaspCatalogo::where('year',$year)->where('entidad',$entidad)->where('nivel',1)->sum('fed_federal'),
            'total_fed_municipal' => (float) FaspCatalogo::where('year',$year)->where('entidad',$entidad)->where('nivel',1)->sum('fed_municipal'),
            'total_est_estatal' => (float) FaspCatalogo::where('year',$year)->where('entidad',$entidad)->where('nivel',1)->sum('est_estatal'),
            'total_est_municipal' => (float) FaspCatalogo::where('year',$year)->where('entidad',$entidad)->where('nivel',1)->sum('est_municipal'),
        ];


        // ======================
        // 3) Listas para filtros (dependientes)
        // ======================
        $base = FaspCatalogo::where('year', $year)->where('entidad', $entidad);

        $ejes = (clone $base)
            ->whereNotNull('eje')
            ->distinct()->orderBy('eje')
            ->pluck('eje');

        $programas = (clone $base)
            ->when($request->filled('eje'), fn($qq) => $qq->where('eje', $request->eje))
            ->whereNotNull('programa')
            ->distinct()->orderBy('programa')
            ->pluck('programa');

        $subprogramas = (clone $base)
            ->when($request->filled('eje'), fn($qq) => $qq->where('eje', $request->eje))
            ->when($request->filled('programa'), fn($qq) => $qq->where('programa', $request->programa))
            ->whereNotNull('subprograma')
            ->distinct()->orderBy('subprograma')
            ->pluck('subprograma');

        $capitulos = (clone $base)
            ->when($request->filled('eje'), fn($qq) => $qq->where('eje', $request->eje))
            ->when($request->filled('programa'), fn($qq) => $qq->where('programa', $request->programa))
            ->when($request->filled('subprograma'), fn($qq) => $qq->where('subprograma', $request->subprograma))
            ->whereNotNull('capitulo')
            ->distinct()
            ->orderByRaw('CAST(capitulo AS UNSIGNED), capitulo')
            ->pluck('capitulo');

        $conceptos = (clone $base)
            ->when($request->filled('eje'), fn($qq) => $qq->where('eje', $request->eje))
            ->when($request->filled('programa'), fn($qq) => $qq->where('programa', $request->programa))
            ->when($request->filled('subprograma'), fn($qq) => $qq->where('subprograma', $request->subprograma))
            ->when($request->filled('capitulo'), fn($qq) => $qq->where('capitulo', $request->capitulo))
            ->whereNotNull('concepto')
            ->distinct()
            ->orderByRaw('CAST(concepto AS UNSIGNED), concepto')
            ->pluck('concepto');

        $partidas = (clone $base)
            ->when($request->filled('eje'), fn($qq) => $qq->where('eje', $request->eje))
            ->when($request->filled('programa'), fn($qq) => $qq->where('programa', $request->programa))
            ->when($request->filled('subprograma'), fn($qq) => $qq->where('subprograma', $request->subprograma))
            ->when($request->filled('capitulo'), fn($qq) => $qq->where('capitulo', $request->capitulo))
            ->when($request->filled('concepto'), fn($qq) => $qq->where('concepto', $request->concepto))
            ->whereNotNull('partida_generica')
            ->distinct()
            ->orderByRaw('CAST(partida_generica AS UNSIGNED), partida_generica')
            ->pluck('partida_generica');

        $bienes = (clone $base)
            ->when($request->filled('eje'), fn($qq) => $qq->where('eje', $request->eje))
            ->when($request->filled('programa'), fn($qq) => $qq->where('programa', $request->programa))
            ->when($request->filled('subprograma'), fn($qq) => $qq->where('subprograma', $request->subprograma))
            ->when($request->filled('capitulo'), fn($qq) => $qq->where('capitulo', $request->capitulo))
            ->when($request->filled('concepto'), fn($qq) => $qq->where('concepto', $request->concepto))
            ->when($request->filled('partida_generica'), fn($qq) => $qq->where('partida_generica', $request->partida_generica))
            ->whereNotNull('bien')
            ->distinct()
            ->orderByRaw('CAST(bien AS UNSIGNED), bien')
            ->pluck('bien');

        return view('admin.fasp.index', compact(
            'rows',
            'year',
            'summary',
            'ejes',
            'programas',
            'subprogramas',
            'capitulos',
            'conceptos',
            'partidas',
            'bienes'
        ));
    }

    public function import(Request $request, FaspTreeService $tree, FaspRollupService $rollup)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls',
            'year' => 'required|integer',
        ]);

        $year = (int) $request->year;
        $entidad = '8300';

        DB::transaction(function () use ($request, $year, $entidad, $tree, $rollup) {

            // ✅ Desactivar asignaciones relacionadas (para que NO "revivan" al reimportar)
            FaspAsignacionInstitucion::where('year', $year)
                ->where('entidad', $entidad)
                ->update(['active' => false]);

            FaspAsignacion::where('year', $year)
                ->where('entidad', $entidad)
                ->update(['active' => false]);

            // borrar catálogo
            FaspCatalogo::where('year', $year)->where('entidad', $entidad)->delete();

            // importar
            Excel::import(new \App\Imports\FaspCatalogoImportSheets($year), $request->file('archivo'));

            // reconstruir y recalcular
            $tree->rebuildParents($year, $entidad);
            $rollup->recalcularYearEntidad($year, $entidad);
        });

        return redirect()
            ->route('admin.fasp.index', ['year' => $year])
            ->with('success', 'Catálogo FASP importado correctamente. (Asignaciones previas desactivadas)');
    }

    public function destroyByYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
        ]);

        $year = (int) $request->year;
        $entidad = '8300';

        DB::transaction(function () use ($year, $entidad) {

            // ✅ Desactivar asignaciones del año/entidad
            FaspAsignacionInstitucion::where('year', $year)
                ->where('entidad', $entidad)
                ->update(['active' => false]);

            FaspAsignacion::where('year', $year)
                ->where('entidad', $entidad)
                ->update(['active' => false]);

            // Borrar catálogo
            FaspCatalogo::where('year', $year)
                ->where('entidad', $entidad)
                ->delete();
        });

        return redirect()
            ->route('admin.fasp.index', ['year' => $year])
            ->with('success', "Catálogo FASP del año {$year} eliminado. Asignaciones desactivadas para ese año.");
    }



    public function store(Request $request, FaspTreeService $tree, FaspRollupService $rollup)
    {
        $entidad = '8300';

    $data = $request->validate([
        'year' => ['required','integer'],
        'parent_id' => ['required','integer', Rule::exists((new FaspCatalogo)->getTable(),'id')],
        'parent_nivel' => ['required','integer','min:1','max:7'],

        'eje' => 'nullable|string|max:2',
        'programa' => 'nullable|string|max:2',
        'subprograma' => 'nullable|string|max:2',
        'capitulo' => 'nullable|string|max:10',
        'concepto' => 'nullable|string|max:10',
        'partida_generica' => 'nullable|string|max:10',

        'child_codigo' => ['required','string','max:10'],
        'nombre' => ['required','string','max:500'],

        // para BIEN se volverán obligatorios en validación condicional
        'fed_federal' => 'nullable|numeric|min:0',
        'fed_municipal' => 'nullable|numeric|min:0',
        'est_estatal' => 'nullable|numeric|min:0',
        'est_municipal' => 'nullable|numeric|min:0',

        'unidad_medida' => 'nullable|string|max:80',
        'cantidad' => 'nullable|numeric|min:0',
        'rlcf' => 'nullable|string|max:50',
    ]);

    $year = (int) $data['year'];
    $entidad = '8300';
    $parentNivel = (int)$data['parent_nivel'];
    $childNivel = $parentNivel + 1;

    if ($childNivel === 7) {
        // validación extra para BIEN
        if (!isset($data['unidad_medida']) || trim((string)$data['unidad_medida']) === '') {
            return back()->withErrors(['unidad_medida' => 'La unidad de medida es obligatoria para un BIEN.'])->withInput();
        }
        if (!isset($data['cantidad']) || $data['cantidad'] === '') {
            return back()->withErrors(['cantidad' => 'La cantidad es obligatoria para un BIEN.'])->withInput();
        }
    }



        if ($parentNivel >= 7) {
            return back()->withErrors(['No se puede agregar hijo a un nivel BIEN (7).']);
        }

        // normaliza el código (ej: eje/programa 2 dígitos; bien/partida/concepto/capitulo sin pad)
        $childCode = trim($data['child_codigo']);
        if (in_array($childNivel, [2,3], true)) {
            $childCode = str_pad((string)(int)$childCode, 2, '0', STR_PAD_LEFT);
        } elseif ($childNivel === 1) {
            $childCode = str_pad((string)(int)$childCode, 2, '0', STR_PAD_LEFT);
        } else {
            // capitulo/concepto/partida/bien: solo limpia
            $childCode = preg_replace('/\s+/', '', $childCode);
        }

        DB::transaction(function () use ($data, $year, $entidad, $childNivel, $childCode, $tree, $rollup) {

            $payload = [
                'year' => $year,
                'entidad' => $entidad,
                'nivel' => $childNivel,
                'parent_id' => (int)$data['parent_id'],
                'nombre' => $data['nombre'],

                'eje' => $data['eje'] ?? null,
                'programa' => $data['programa'] ?? null,
                'subprograma' => $data['subprograma'] ?? null,
                'capitulo' => $data['capitulo'] ?? null,
                'concepto' => $data['concepto'] ?? null,
                'partida_generica' => $data['partida_generica'] ?? null,
                'bien' => null,
            ];

            // asignar el código al campo correcto según el nivel del hijo
            if ($childNivel === 1) $payload['eje'] = $childCode;
            if ($childNivel === 2) $payload['programa'] = $childCode;
            if ($childNivel === 3) $payload['subprograma'] = $childCode;
            if ($childNivel === 4) $payload['capitulo'] = $childCode;
            if ($childNivel === 5) $payload['concepto'] = $childCode;
            if ($childNivel === 6) $payload['partida_generica'] = $childCode;
            if ($childNivel === 7) $payload['bien'] = $childCode;

            // opcionales
            $payload['fed_federal'] = (float)($data['fed_federal'] ?? 0);
            $payload['fed_municipal'] = (float)($data['fed_municipal'] ?? 0);
            $payload['est_estatal'] = (float)($data['est_estatal'] ?? 0);
            $payload['est_municipal'] = (float)($data['est_municipal'] ?? 0);
            $payload['unidad_medida'] = $data['unidad_medida'] ?? null;
            $payload['cantidad'] = $data['cantidad'] ?? null;
            $payload['rlcf'] = $data['rlcf'] ?? null;

            // evitar duplicados por combinación de claves
            $exists = FaspCatalogo::where('year',$year)->where('entidad',$entidad)
                ->where('eje',$payload['eje'])
                ->where('programa',$payload['programa'])
                ->where('subprograma',$payload['subprograma'])
                ->where('capitulo',$payload['capitulo'])
                ->where('concepto',$payload['concepto'])
                ->where('partida_generica',$payload['partida_generica'])
                ->where('bien',$payload['bien'])
                ->exists();

            if ($exists) {
                throw new \Exception('Ese hijo ya existe con ese código en esa rama.');
            }

            FaspCatalogo::create($payload);

            // mantener árbol y totales correctos
            $tree->rebuildParents($year, $entidad);
            $rollup->recalcularYearEntidad($year, $entidad);
        });

        return back()->with('success', 'Hijo agregado correctamente.');
    }

    public function update(Request $request, FaspCatalogo $row, FaspTreeService $tree, FaspRollupService $rollup)
    {
        $entidad = '8300';

        $data = $request->validate([
            'nombre' => 'nullable|string|max:500',
            'fed_federal' => 'nullable|numeric|min:0',
            'fed_municipal' => 'nullable|numeric|min:0',
            'est_estatal' => 'nullable|numeric|min:0',
            'est_municipal' => 'nullable|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:80',
            'cantidad' => 'nullable|numeric|min:0',
            'rlcf' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($row, $data, $entidad, $tree, $rollup) {
            $row->update($data);
            $tree->rebuildParents((int)$row->year, $entidad);
            $rollup->recalcularYearEntidad((int)$row->year, $entidad);
        });

        return redirect()->route('admin.fasp.index', ['year'=>$row->year, 'eje'=>$row->eje])
            ->with('success','Registro actualizado.');
    }

    public function destroy(FaspCatalogo $row, FaspTreeService $tree, FaspRollupService $rollup)
    {
        $entidad = '8300';
        $year = (int) $row->year;
        $eje = $row->eje;

        DB::transaction(function () use ($row, $year, $entidad, $tree, $rollup) {

            // si tiene hijos, no borrar (para no romper el árbol)
            if (FaspCatalogo::where('parent_id', $row->id)->exists()) {
                throw new \Exception('No puedes eliminar este registro porque tiene hijos.');
            }

            $row->delete();

            $tree->rebuildParents($year, $entidad);
            $rollup->recalcularYearEntidad($year, $entidad);
        });

        return redirect()->route('admin.fasp.index', ['year'=>$year, 'eje'=>$eje])
            ->with('success', 'Registro eliminado.');
    }

    // helpers dentro del controller (privados)
    private function nivelFromData(array $d): int
    {
        if (!empty($d['bien'])) return 7;
        if (!empty($d['partida_generica'])) return 6;
        if (!empty($d['concepto'])) return 5;
        if (!empty($d['capitulo'])) return 4;
        if (!empty($d['subprograma'])) return 3;
        if (!empty($d['programa'])) return 2;
        return 1;
    }

    private function descendantIds(int $id): array
    {
        // Simple: usa parent_id para encontrar descendientes (requiere que rebuildParents ya exista)
        $all = FaspCatalogo::select('id','parent_id')->get();
        $children = [];
        foreach ($all as $r) {
            $children[$r->parent_id ?? 0][] = $r->id;
        }

        $stack = [$id];
        $out = [];
        while ($stack) {
            $cur = array_pop($stack);
            $out[] = $cur;
            foreach (($children[$cur] ?? []) as $ch) $stack[] = $ch;
        }
        return $out;
    }

}