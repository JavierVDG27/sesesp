<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presupuesto extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediente_id',
        'capitulo',
        'partida',               // PARTIDA GENERICA
        'descripcion_concepto',  // CONCEPTO
        'bien',
        'cantidad',
        'unidad',                // UNIDAD DE MEDIDA
        'persona',
        'rlc',

        // Origen de los recursos
        'fasp_federal',
        'fasp_municipal',
        'fasp_subtotal',
        'est_estatal',
        'est_municipal',
        'est_subtotal',
        'total_financiamiento',
        'precio_unitario',
        'subtotal',
        'iva',
        'total',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
