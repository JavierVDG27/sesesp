<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FaspAsignacion extends Model
{
    use HasFactory;

    protected $table = 'fasp_asignaciones';

    protected $fillable = [
        'year','entidad','nivel',
        'eje','programa','subprograma',
        'user_id','assigned_by','active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'year'   => 'integer',
        'nivel'  => 'integer',
    ];

    public function capturista()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignador()
    {
        return $this->belongsTo(User::class, 'assigned_by');
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
