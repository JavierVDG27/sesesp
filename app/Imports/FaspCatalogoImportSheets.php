<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FaspCatalogoImportSheets implements WithMultipleSheets
{
    public function __construct(private int $year) {}

    public function sheets(): array
    {
        return [
            0 => new FaspCatalogoImport($this->year), // solo primera hoja
        ];
    }
}
