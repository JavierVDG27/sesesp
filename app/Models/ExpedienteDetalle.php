<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteDetalle extends Model
{
    protected $table = 'expediente_detalles';

    protected $fillable = [
        'expediente_id',
        'titulo_documento',
        'subtitulo_documento',
        'fasp_texto',
        'ejercicio_fiscal_label',
        'anio_override',
        'logo_path',
        'eje_override',
        'programa_override',
        'subprograma_override',
        'introduccion',
        'marco_legal_json',
        'marco_legal','objeto','alcance','justificacion','requerimientos','criterios_aceptacion',
        'no_aplica_9','no_aplica_10','no_aplica_11','no_aplica_12','no_aplica_13','no_aplica_14','no_aplica_15','no_aplica_16',
        'responsable_subprograma_nombre','responsable_subprograma_cargo','titular_dependencia_nombre','titular_dependencia_cargo',
        'observaciones_finales','segunda_parte_completa',
    ];

    protected $casts = [
        'segunda_parte_completa' => 'boolean',
        'anio_override' => 'integer',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
