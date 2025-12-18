<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subdependencia extends Model
{
    protected $fillable = ['institucion_id', 'nombre', 'siglas', 'orden'];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'subdependencia_id');
    }

}
