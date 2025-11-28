<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';

    protected $fillable = ['nombre', 'siglas'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
