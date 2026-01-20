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

                $sheet = $event->sheet->getDelegate();

                // 1) Insertar 76 filas arriba para que el header quede en la 77
                $sheet->insertNewRowBefore(1, 76);

                // 2) Leyenda / recordatorio antes de los encabezados
                //    Usamos la fila 76 para que se vea justo arriba del header.
                $sheet->mergeCells('A76:O76');
                $sheet->setCellValue(
                    'A76',
                    'IMPORTANTE: Los datos del catálogo se capturan a partir de la fila 78. ' .
                    'La fila 77 debe contener exactamente los encabezados de la plantilla.'
                );
                $sheet->getStyle('A76')->getFont()->setBold(true);
                $sheet->getStyle('A76')->getFont()->setSize(10);
                $sheet->getStyle('A76')->getAlignment()->setHorizontal('center');

                // 3) Escribir encabezados EXACTOS en la fila 77 (A77 en adelante)
                $headers = [
                    'EJE','PROGRAMA','SUBPROGRAMA','CAPITULO','CONCEPTO','PARTIDA GENERICA','BIEN',
                    'NOMBRE',
                    'FED. FEDERAL','FED. MUNICIPAL','EST. ESTATAL','EST. MUNICIPAL',
                    'UNIDAD DE MEDIDA','CANTIDAD','RLCF'
                ];

                $col = 'A';
                foreach ($headers as $h) {
                    $sheet->setCellValue($col.'77', $h);
                    $col++;
                }

                // 4) Estilo de encabezados
                $event->sheet->getStyle('A77:O77')->getFont()->setBold(true);

                //    el usuario verá directamente la leyenda (76), encabezados (77) y datos (78+)
                for ($row = 1; $row <= 75; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(0); // 0 = hidden
                }

                // 6) Fijar fila 77 como encabezado congelado y datos desde 78
                $event->sheet->freezePane('A78');

                // Ajustar el ancho de columnas típicas
                $sheet->getColumnDimension('A')->setWidth(8);   // EJE
                $sheet->getColumnDimension('B')->setWidth(12);  // PROGRAMA
                $sheet->getColumnDimension('C')->setWidth(14);  // SUBPROGRAMA
                $sheet->getColumnDimension('D')->setWidth(10);  // CAPITULO
                $sheet->getColumnDimension('E')->setWidth(10);  // CONCEPTO
                $sheet->getColumnDimension('F')->setWidth(16);  // PARTIDA GENERICA
                $sheet->getColumnDimension('G')->setWidth(10);  // BIEN
                $sheet->getColumnDimension('H')->setWidth(60);  // NOMBRE
                $sheet->getColumnDimension('I')->setWidth(14);  // FED. FEDERAL
                $sheet->getColumnDimension('J')->setWidth(14);  // FED. MUNICIPAL
                $sheet->getColumnDimension('K')->setWidth(14);  // EST. ESTATAL
                $sheet->getColumnDimension('L')->setWidth(14);  // EST. MUNICIPAL
                $sheet->getColumnDimension('M')->setWidth(16);  // UNIDAD DE MEDIDA
                $sheet->getColumnDimension('N')->setWidth(12);  // CANTIDAD
                $sheet->getColumnDimension('O')->setWidth(14);  // RLCF

                // 7) Autofiltro sobre encabezados
                $event->sheet->setAutoFilter('A77:O77');
            }
        ];
    }

}