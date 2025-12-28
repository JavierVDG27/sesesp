<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FaspAsignacionInstitucion extends Model
{
    use HasFactory;

    protected $table = 'fasp_asignaciones_institucion';

    protected $fillable = [
        'year','entidad','nivel',
        'eje','programa','subprograma',
        'institucion_id','assigned_by','active',
    ];

    protected $casts = [
        'year' => 'integer',
        'nivel' => 'integer',
        'active' => 'boolean',
    ];

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Institucion::class, 'institucion_id');
    }

    public function asignador()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }

    public function scopeActivas($q)
    {
        return $q->where('active', true);
    }

    public function scopeYearEntidad($q, int $year, string $entidad = '8300')
    {
        return $q->where('year', $year)->where('entidad', $entidad);
    }
}
