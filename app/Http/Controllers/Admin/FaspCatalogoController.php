<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaspCatalogo;
use App\Services\FaspRollupService;
use App\Services\FaspTreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
        $rows = $q->orderBy('eje')
            ->orderBy('programa')
            ->orderBy('subprograma')
            ->orderByRaw('CAST(capitulo AS UNSIGNED), capitulo')
            ->orderByRaw('CAST(concepto AS UNSIGNED), concepto')
            ->orderByRaw('CAST(partida_generica AS UNSIGNED), partida_generica')
            ->orderByRaw('CAST(bien AS UNSIGNED), bien')
            ->get();

        // ======================
        // 2) Summary total del año (raíces nivel 1)
        // ======================
        $rootRows = FaspCatalogo::where('year', $year)
            ->where('entidad', $entidad)
            ->where('nivel', 1)
            ->get();

        $summary = [
            'count' => $rows->count(),
            'total_fed_federal'   => (float) $rootRows->sum('fed_federal'),
            'total_fed_municipal' => (float) $rootRows->sum('fed_municipal'),
            'total_est_estatal'   => (float) $rootRows->sum('est_estatal'),
            'total_est_municipal' => (float) $rootRows->sum('est_municipal'),
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

            FaspCatalogo::where('year', $year)->where('entidad', $entidad)->delete();

            Excel::import(new \App\Imports\FaspCatalogoImportSheets($year), $request->file('archivo'));

            $tree->rebuildParents($year, $entidad);
            $rollup->recalcularYearEntidad($year, $entidad);
        });

        return redirect()
            ->route('admin.fasp.index', ['year' => $year])
            ->with('success', 'Catálogo FASP importado correctamente.');
    }

    public function destroyByYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
        ]);

        $year = (int) $request->year;
        $entidad = '8300';

        FaspCatalogo::where('year', $year)->where('entidad', $entidad)->delete();

        return redirect()
            ->route('admin.fasp.index', ['year' => $year])
            ->with('success', "Catálogo FASP del año {$year} eliminado. Ya puedes cargar uno nuevo.");
    }
}