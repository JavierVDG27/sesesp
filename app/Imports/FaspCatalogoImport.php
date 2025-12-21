<?php

namespace App\Imports;

use App\Models\FaspCatalogo;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class FaspCatalogoImport implements ToCollection
{
  public function collection(Collection $rows)
  {
    $stack = []; // [nivel => FaspCatalogo]

    foreach ($rows as $i => $row) {
      // Si tienes encabezados, aquí brincas las primeras filas
      // if ($i === 0) continue;

      $year = (int)($row[0] ?? 0);
      $entidad = trim((string)($row[1] ?? '8300')) ?: '8300';

      // Ajusta índices según tu Excel real:
      $eje = $this->norm($row[2] ?? null, 2);
      $programa = $this->norm($row[3] ?? null, 2);
      $subprograma = $this->norm($row[4] ?? null, 2);
      $capitulo = $this->norm($row[5] ?? null, null);
      $concepto = $this->norm($row[6] ?? null, null);
      $partida = $this->norm($row[7] ?? null, null);
      $bien = $this->norm($row[8] ?? null, null);

      $nombre = trim((string)($row[9] ?? ''));

      // Montos (capturados) según tu ejemplo:
      $fed_federal   = $this->money($row[10] ?? 0);
      $fed_municipal = $this->money($row[11] ?? 0);
      $est_estatal   = $this->money($row[13] ?? 0);
      $est_municipal = $this->money($row[14] ?? 0);

      // Unidad/cantidad/etc (ajusta índices reales)
      $unidad = trim((string)($row[18] ?? '')) ?: null;
      $cantidad = $row[19] ?? null;
      $persona2 = trim((string)($row[20] ?? '')) ?: null;
      $rlcf = trim((string)($row[21] ?? '')) ?: null;

      $nivel = $this->nivel($eje,$programa,$subprograma,$capitulo,$concepto,$partida,$bien);
      if ($nivel === null) continue;

      $parentId = $nivel > 1 ? ($stack[$nivel-1]->id ?? null) : null;

      $attrs = [
        'year'=>$year, 'entidad'=>$entidad, 'nivel'=>$nivel,
        'eje'=>$eje, 'programa'=>$programa, 'subprograma'=>$subprograma,
        'capitulo'=>$capitulo, 'concepto'=>$concepto,
        'partida_generica'=>$partida, 'bien'=>$bien,
      ];

      $node = FaspCatalogo::updateOrCreate($attrs, [
        'parent_id'=>$parentId,
        'nombre'=>$nombre,
        'fed_federal'=>$fed_federal,
        'fed_municipal'=>$fed_municipal,
        'est_estatal'=>$est_estatal,
        'est_municipal'=>$est_municipal,
        'unidad_medida'=>$unidad,
        'cantidad'=>$cantidad !== null ? (float)$cantidad : null,
        'persona_cantidad2'=>$persona2,
        'rlcf'=>$rlcf,
      ]);

      // Actualiza pila: este nivel es el “último” visto
      $stack[$nivel] = $node;

      // Limpia niveles más profundos si cambia rama
      for ($l = $nivel+1; $l <= 7; $l++) unset($stack[$l]);
    }
  }

  private function nivel($eje,$programa,$subprograma,$capitulo,$concepto,$partida,$bien): ?int
  {
    if ($bien) return 7;
    if ($partida) return 6;
    if ($concepto) return 5;
    if ($capitulo) return 4;
    if ($subprograma) return 3;
    if ($programa) return 2;
    if ($eje) return 1;
    return null;
  }

  private function norm($v, ?int $pad)
  {
    $v = trim((string)$v);
    if ($v === '') return null;
    return $pad ? str_pad($v, $pad, '0', STR_PAD_LEFT) : $v;
  }

  private function money($v): float
  {
    $s = str_replace([',',' '], '', (string)$v);
    if ($s === '') return 0.0;
    return (float)$s;
  }
}
