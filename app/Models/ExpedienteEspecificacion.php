<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteEspecificacion extends Model
{
    protected $table = 'expediente_especificaciones';

    protected $fillable = [
        'expediente_id','partida','titulo_producto','descripcion_tecnica',
        'cantidad','unidad_medida','precio_unitario','importe_sin_iva','orden'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'importe_sin_iva' => 'decimal:2',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
