<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialModificacion extends Model
{
    use HasFactory;

    protected $table = 'historial_modificaciones';

    protected $fillable = [
        'expediente_id',
        'usuario_id',
        'estado_anterior',
        'estado_nuevo',
        'observaciones',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
