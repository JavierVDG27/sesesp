<?php

namespace App\Services;

use App\Models\FaspCatalogo;
use Illuminate\Support\Facades\DB;

class FaspRollupService
{
  public function recalcularYearEntidad(int $year, string $entidad='8300'): void
  {
    // de nivel 7 hacia 1
    for ($nivel = 7; $nivel >= 1; $nivel--) {

      if ($nivel === 7) {
        // En BIEN: calculado = capturado
        FaspCatalogo::where('year',$year)->where('entidad',$entidad)->where('nivel',7)
          ->update([
            'calc_fed_federal' => DB::raw('fed_federal'),
            'calc_fed_municipal' => DB::raw('fed_municipal'),
            'calc_est_estatal' => DB::raw('est_estatal'),
            'calc_est_municipal' => DB::raw('est_municipal'),
          ]);
        continue;
      }

      // En padres: suma de hijos DIRECTOS (si tu excel siempre es jerárquico por parent_id)
      // (Si necesitaras descendientes, se hace recursivo, pero con parent_id en escalera esto funciona)
      $parents = FaspCatalogo::select('id')
        ->where('year',$year)->where('entidad',$entidad)->where('nivel',$nivel)
        ->pluck('id');

      foreach ($parents as $pid) {
        $sum = FaspCatalogo::where('parent_id',$pid)->selectRaw('
            COALESCE(SUM(calc_fed_federal),0) as a,
            COALESCE(SUM(calc_fed_municipal),0) as b,
            COALESCE(SUM(calc_est_estatal),0) as c,
            COALESCE(SUM(calc_est_municipal),0) as d
        ')->first();

        FaspCatalogo::where('id',$pid)->update([
          'calc_fed_federal' => $sum->a,
          'calc_fed_municipal' => $sum->b,
          'calc_est_estatal' => $sum->c,
          'calc_est_municipal' => $sum->d,
        ]);
      }
    }
  }
}
