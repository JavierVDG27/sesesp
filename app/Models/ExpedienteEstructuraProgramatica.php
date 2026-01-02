<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteEstructuraProgramatica extends Model
{
    protected $table = 'expediente_estructura_programatica';

    protected $fillable = [
        'expediente_id','programa','subprograma','partida_bien_servicio','costo',
        'unidad_medida','meta_cantidad','aportacion','orden'
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'meta_cantidad' => 'decimal:2',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
