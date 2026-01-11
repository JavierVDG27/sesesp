<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaspCatalogo;

class DashboardController extends Controller
{
    public function index()
    {
        $year = request('year', now()->year);

        $rows = FaspCatalogo::where('year', $year)
            ->where('entidad', '8300')
            ->orderBy('eje')
            ->orderBy('programa')
            ->orderBy('subprograma')
            ->orderBy('capitulo')
            ->orderBy('concepto')
            ->orderBy('partida_generica')
            ->orderBy('bien')
            ->get();

        return view('admin.dashboard', compact('rows', 'year'));
    }
}
