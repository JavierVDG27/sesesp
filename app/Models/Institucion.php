<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';

    protected $fillable = ['nombre', 'siglas', 'orden'];

    public function subdependencias()
    {
        return $this->hasMany(\App\Models\Subdependencia::class)
            ->orderBy('orden'); 
    }

    public function users()
    {
        return $this->hasMany(User::class, 'institucion_id');
    }

}
