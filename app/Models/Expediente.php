<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Expediente extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'nombre_proyecto',
        'dependencia',
        'tipo_recurso',
        'anio_ejercicio',
        'entidad',
        'eje',
        'programa',
        'subprograma',
        'tema',
        'area_ejecutora',
        'estatus',
        'user_id',
    ];

    public function descripcionProyecto()
    {
        return $this->hasOne(DescripcionProyecto::class);
    }

    public function metas()
    {
        return $this->hasMany(MetaIndicadorActividad::class);
    }

    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class);
    }

    public function adjuntos()
    {
        return $this->hasMany(DocumentoAdjunto::class);
    }

    public function historial()
    {
        return $this->hasMany(HistorialExpediente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

