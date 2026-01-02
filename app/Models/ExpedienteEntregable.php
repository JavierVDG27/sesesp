<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteEntregable extends Model
{
    protected $table = 'expediente_entregables';

    protected $fillable = [
        'expediente_id','num','descripcion','cantidad','fecha_entrega',
        'responsable_validar','lugar_entrega','orden'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
