<?php

namespace App\Services;

use App\Models\FaspCatalogo;

class FaspTreeService
{
    public function rebuildParents(int $year, string $entidad = '8300'): void
    {
        $rows = FaspCatalogo::where('year', $year)->where('entidad', $entidad)
            ->orderBy('eje')->orderBy('programa')->orderBy('subprograma')
            ->orderBy('capitulo')->orderBy('concepto')->orderBy('partida_generica')->orderBy('bien')
            ->get();

        $stack = [];

        foreach ($rows as $r) {
            $parentId = $r->nivel > 1 ? ($stack[$r->nivel - 1] ?? null) : null;

            if ($r->parent_id !== $parentId) {
                $r->parent_id = $parentId;
                $r->save();
            }

            $stack[$r->nivel] = $r->id;

            for ($i = $r->nivel + 1; $i <= 7; $i++) {
                unset($stack[$i]);
            }
        }
    }
}
