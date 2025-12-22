<?php

namespace App\Services;

use App\Models\FaspCatalogo;

class FaspRollupService
{
    public function recalcularYearEntidad(int $year, string $entidad = '8300'): void
    {
        // del nivel 6 hacia el 1 (padres)
        for ($nivel = 6; $nivel >= 1; $nivel--) {

            $parents = FaspCatalogo::where('year', $year)
                ->where('entidad', $entidad)
                ->where('nivel', $nivel)
                ->get();

            foreach ($parents as $p) {
                $children = FaspCatalogo::where('parent_id', $p->id)->get();

                $p->fed_federal   = (float) $children->sum('fed_federal');
                $p->fed_municipal = (float) $children->sum('fed_municipal');
                $p->est_estatal   = (float) $children->sum('est_estatal');
                $p->est_municipal = (float) $children->sum('est_municipal');

                // si tienes columnas calculadas en BD, aquí las actualizas
                // $p->fed_subtotal = $p->fed_federal + $p->fed_municipal;
                // $p->est_subtotal = $p->est_estatal + $p->est_municipal;
                // $p->fin_total    = $p->fed_subtotal + $p->est_subtotal;

                $p->save();
            }
        }
    }
}
