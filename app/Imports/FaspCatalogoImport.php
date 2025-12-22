<?php

namespace App\Imports;

use App\Models\FaspCatalogo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class FaspCatalogoImport implements ToModel, WithHeadingRow, WithStartRow, WithChunkReading, WithBatchInserts
{
    protected int $year;

    /** Evita duplicados dentro del mismo archivo */
    protected array $seen = [];

    /** Validación de encabezados solo 1 vez */
    protected bool $validatedHeaders = false;

    /** Para “fill-down” (cuando el excel deja celdas vacías por merges) */
    protected array $last = [
        'eje' => null,
        'programa' => null,
        'subprograma' => null,
        'capitulo' => null,
        'concepto' => null,
        'partida' => null,
    ];

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function headingRow(): int { return 73; }
    public function startRow(): int { return 77; }

    public function chunkSize(): int { return 500; }
    public function batchSize(): int { return 500; }

    public function model(array $row)
    {
        // Validar encabezados
        if (!$this->validatedHeaders) {
            $required = [
                'eje', 'programa', 'subprograma',
                'capitulo', 'concepto', 'partida_generica', 'bien',
                'programas_con_prioridad_nacional_y_subprogramas',

                // Montos (según tu excel real):
                'origen_de_los_recursos', // N77 Federal (FASP)
                14,                      // O77 Municipal (FASP)
                16,                      // Q77 Estatal
                17,                      // R77 Municipal (Estatal)
            ];

            foreach ($required as $key) {
                if (!array_key_exists($key, $row)) {
                    throw new \Exception("El Excel no trae la columna requerida: {$key}. Revisa encabezados (fila 73) o el formato.");
                }
            }

            $this->validatedHeaders = true;
        }

        // Fin de tabla: fila totalmente vacía
        if ($this->rowIsEmpty($row)) {
            return null;
        }

        // Captura “cruda”
        $rawEje      = $this->code($row['eje'] ?? null, 2);
        $rawPrograma = $this->code($row['programa'] ?? null, 2);
        $rawSubprog  = $this->code($row['subprograma'] ?? null, 2);

        $rawCapitulo = $this->code($row['capitulo'] ?? null, null);
        $rawConcepto = $this->code($row['concepto'] ?? null, null);
        $rawPartida  = $this->code($row['partida_generica'] ?? $row['partida_generica_'] ?? null, null);
        $rawBien     = $this->code($row['bien'] ?? null, null);

        // Reset limpio cuando cambia EJE (antes del fill-down)
        if ($rawEje !== null && $rawEje !== $this->last['eje']) {
            $this->resetBelow('eje');
        }

        // Fill-down: si viene vacío por merges, hereda el último valor
        $eje      = $rawEje      ?: $this->last['eje'];
        $programa = $rawPrograma ?: (($rawSubprog || $rawCapitulo || $rawConcepto || $rawPartida || $rawBien) ? $this->last['programa'] : null);
        $subprog  = $rawSubprog  ?: (($rawCapitulo || $rawConcepto || $rawPartida || $rawBien) ? $this->last['subprograma'] : null);
        $capitulo = $rawCapitulo ?: (($rawConcepto || $rawPartida || $rawBien) ? $this->last['capitulo'] : null);
        $concepto = $rawConcepto ?: (($rawPartida || $rawBien) ? $this->last['concepto'] : null);
        $partida  = $rawPartida  ?: ($rawBien ? $this->last['partida'] : null);
        $bien     = $rawBien;

        //Actualiza “last” y resetea inferiores cuando cambia un nivel
        $this->touchLast('eje', $eje);
        $this->touchLast('programa', $programa);
        $this->touchLast('subprograma', $subprog);
        $this->touchLast('capitulo', $capitulo);
        $this->touchLast('concepto', $concepto);
        $this->touchLast('partida', $partida);

        //Nivel jerárquico
        $nivel = $this->nivel($eje, $programa, $subprog, $capitulo, $concepto, $partida, $bien);
        if (!$nivel) return null;

        //Dedupe por llave lógica (evita violar unique)
        $dedupeKey = implode('|', [
            $this->year, '8300',
            $eje, $programa, $subprog,
            $capitulo, $concepto, $partida, $bien
        ]);

        if (isset($this->seen[$dedupeKey])) {
            return null;
        }
        $this->seen[$dedupeKey] = true;

        // Nombre
        $nombre = trim((string)(
            $row['programas_con_prioridad_nacional_y_subprogramas']
                ?? $row['programas_con_prioridad_nacional_y_subprogramas_']
                ?? ''
        ));

        // Montos (N,O,Q,R)
        $fedFed = $this->money($row['origen_de_los_recursos'] ?? 0); // N
        $fedMun = $this->money($row[14] ?? 0);                      // O
        $estEst = $this->money($row[16] ?? 0);                      // Q
        $estMun = $this->money($row[17] ?? 0);                      // R

        return new FaspCatalogo([
            'year' => $this->year,
            'entidad' => '8300',
            'nivel' => $nivel,

            'eje' => $eje,
            'programa' => $programa,
            'subprograma' => $subprog,
            'capitulo' => $capitulo,
            'concepto' => $concepto,
            'partida_generica' => $partida,
            'bien' => $bien,

            // En chunks/batch: parent_id se reconstruye después con FaspTreeService
            'parent_id' => null,

            'nombre' => $nombre !== '' ? $nombre : null,
            'fed_federal' => $fedFed,
            'fed_municipal' => $fedMun,
            'est_estatal' => $estEst,
            'est_municipal' => $estMun,
        ]);
    }

    private function resetBelow(string $level): void
    {
        // cuando cambia un nivel, lo de abajo se resetea
        if ($level === 'eje') {
            $this->last['programa'] = null;
            $this->last['subprograma'] = null;
            $this->last['capitulo'] = null;
            $this->last['concepto'] = null;
            $this->last['partida'] = null;
        } elseif ($level === 'programa') {
            $this->last['subprograma'] = null;
            $this->last['capitulo'] = null;
            $this->last['concepto'] = null;
            $this->last['partida'] = null;
        } elseif ($level === 'subprograma') {
            $this->last['capitulo'] = null;
            $this->last['concepto'] = null;
            $this->last['partida'] = null;
        } elseif ($level === 'capitulo') {
            $this->last['concepto'] = null;
            $this->last['partida'] = null;
        } elseif ($level === 'concepto') {
            $this->last['partida'] = null;
        }
    }

    private function touchLast(string $level, ?string $value): void
    {
        if ($value === null) return;

        // si cambia, resetea inferiores
        if (($this->last[$level] ?? null) !== $value) {
            $this->resetBelow($level);
        }

        $this->last[$level] = $value;
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $v) {
            if ($v !== null && $v !== '') return false;
        }
        return true;
    }

    private function nivel($eje, $programa, $subprog, $capitulo, $concepto, $partida, $bien): ?int
    {
        if ($bien) return 7;
        if ($partida) return 6;
        if ($concepto) return 5;
        if ($capitulo) return 4;
        if ($subprog) return 3;
        if ($programa) return 2;
        if ($eje) return 1;
        return null;
    }

    private function code($v, ?int $pad): ?string
    {
        if ($v === null) return null;

        if (is_numeric($v)) $v = (string)(int)$v;
        else $v = trim((string)$v);

        if ($v === '') return null;

        return $pad ? str_pad($v, $pad, '0', STR_PAD_LEFT) : $v;
    }

    private function money($v): float
    {
        if ($v === null || $v === '') return 0.0;
        $s = str_replace([',', ' '], '', (string)$v);
        return is_numeric($s) ? (float)$s : 0.0;
    }
}
