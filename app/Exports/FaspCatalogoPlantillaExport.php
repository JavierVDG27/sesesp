<?php

namespace App\Exports;

use App\Models\FaspCatalogo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class FaspCatalogoPlantillaExport implements FromCollection, WithEvents, WithTitle
{
    public function __construct(
        public int $year,
        public string $entidad = '8300'
    ) {}

    public function title(): string
    {
        return 'CATALOGO';
    }

    public function collection(): Collection
    {
        // Trae todo el catálogo del año
        $rows = FaspCatalogo::query()
            ->where('year', $this->year)
            ->where('entidad', $this->entidad)
            ->orderBy('nivel')
            ->orderBy('eje')
            ->orderBy('programa')
            ->orderBy('subprograma')
            ->orderByRaw('CAST(capitulo AS UNSIGNED), capitulo')
            ->orderByRaw('CAST(concepto AS UNSIGNED), concepto')
            ->orderByRaw('CAST(partida_generica AS UNSIGNED), partida_generica')
            ->orderByRaw('CAST(bien AS UNSIGNED), bien')
            ->get();

        // OJO: aquí debes acomodar EXACTAMENTE las columnas como tu import las lee.
        // Como no me pasaste todavía tu ImportSheets, dejo una estructura típica.
        // Ajustas los keys/orden a tu plantilla real.
        return $rows->map(function ($r) {
            return [
                'EJE' => $r->eje,
                'PROGRAMA' => $r->programa,
                'SUBPROGRAMA' => $r->subprograma,
                'CAPITULO' => $r->capitulo,
                'CONCEPTO' => $r->concepto,
                'PARTIDA_GENERICA' => $r->partida_generica,
                'BIEN' => $r->bien,
                'NOMBRE' => $r->nombre,
                'FED_FEDERAL' => $r->fed_federal,
                'FED_MUNICIPAL' => $r->fed_municipal,
                'EST_ESTATAL' => $r->est_estatal,
                'EST_MUNICIPAL' => $r->est_municipal,
                'UNIDAD_MEDIDA' => $r->unidad_medida,
                'CANTIDAD' => $r->cantidad,
                'RLCF' => $r->rlcf,
            ];
        });
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // 1) Insertar 76 filas arriba para que el header quede en la 77
                $event->sheet->getDelegate()->insertNewRowBefore(1, 76);

                // 2) Escribir encabezados EXACTOS en la fila 77 (A77 en adelante)
                // Ajusta textos/orden exactamente como tu plantilla real.
                $headers = [
                    'EJE','PROGRAMA','SUBPROGRAMA','CAPITULO','CONCEPTO','PARTIDA GENERICA','BIEN',
                    'NOMBRE',
                    'FED. FEDERAL','FED. MUNICIPAL','EST. ESTATAL','EST. MUNICIPAL',
                    'UNIDAD DE MEDIDA','CANTIDAD','RLCF'
                ];

                $col = 'A';
                foreach ($headers as $h) {
                    $event->sheet->setCellValue($col.'77', $h);
                    $col++;
                }

                // 3) Un poquito de formato para que se vea como plantilla
                $event->sheet->getStyle('A77:O77')->getFont()->setBold(true);
                $event->sheet->freezePane('A78');
                $event->sheet->setAutoFilter('A77:O77');
            }
        ];
    }
}