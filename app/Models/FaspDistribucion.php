<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaspDistribucion extends Model
{
    protected $table = 'fasp_distribuciones';

    protected $fillable = [
        'year','entidad','nivel','eje','programa','subprograma',
        'fuente','descripcion','institucion_id','monto','created_by'
    ];

    protected $casts = [
        'year' => 'integer',
        'nivel' => 'integer',
        'monto' => 'decimal:2',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeKey($q, $year, $entidad, $eje, $programa, $subprograma)
    {
        return $q->where('year', (int)$year)
            ->where('entidad', (string)$entidad)
            ->where('nivel', 3)
            ->where('eje', (string)$eje)
            ->where('programa', (string)$programa)
            ->where('subprograma', (string)$subprograma);
    }
}
