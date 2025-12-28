<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaspDistribucionLock extends Model
{
    protected $table = 'fasp_distribucion_locks';

    protected $fillable = [
        'year','entidad','nivel','eje','programa','subprograma','locked_at','locked_by'
    ];

    protected $casts = [
        'year' => 'integer',
        'nivel' => 'integer',
        'locked_at' => 'datetime',
    ];

    public function scopeKey($q, $year, $entidad, $eje, $programa, $subprograma)
    {
        return $q->where('year', (int)$year)
            ->where('entidad', (string)$entidad)
            ->where('nivel', 3)
            ->where('eje', (string)$eje)
            ->where('programa', (string)$programa)
            ->where('subprograma', (string)$subprograma);
    }

    public function estaBloqueado(): bool
    {
        return !is_null($this->locked_at);
    }
}
