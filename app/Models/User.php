<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

protected $fillable = [
    'nombres',
    'apellido_paterno',
    'apellido_materno',
    'email',
    'password',
    'role_id',
    'institucion_id',
    'subdependencia_id',
    'activo',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Institucion::class, 'institucion_id');
    }

    public function subdependencia()
    {
        return $this->belongsTo(Subdependencia::class, 'subdependencia_id');
    }

    public function faspAsignaciones()
    {
        return $this->hasMany(\App\Models\FaspAsignacion::class, 'user_id');
    }
}
