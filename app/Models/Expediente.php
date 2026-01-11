<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Presupuesto;
use App\Models\User;
use App\Models\HistorialModificacion;

class Expediente extends Model
{
    use HasFactory;

    // Estados
    public const ESTADO_BORRADOR      = 'borrador';
    public const ESTADO_EN_VALIDACION = 'en validacion';
    public const ESTADO_APROBADO      = 'aprobado';
    public const ESTADO_RECHAZADO     = 'rechazado';

    public const ESTADO_PENDIENTE_FIRMA = 'pendiente_firma';
    public const ESTADO_FIRMADO         = 'firmado';

    protected $fillable = [
        'folio',
        'nombre_proyecto',
        'tipo_recurso',
        'anio_ejercicio',
        'estatus',

        'user_id',
        'institucion_id',

        'entidad',
        'eje',
        'programa',
        'subprograma',

        'capitulo',
        'concepto',
        'partida_generica',

        'tema',
        'area_ejecutora',

        'bienes',

        // PDF firmado
        'pdf_firmado_path',
        'pdf_firmado_usuario_id',
        'pdf_firmado_at',
    ];

    protected $casts = [
        'bienes' => 'array',
        'pdf_firmado_at' => 'datetime',
    ];

    // ===================== Relaciones =====================

    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function historiales()
    {
        // Ordenados del más reciente al más viejo
        return $this->hasMany(HistorialModificacion::class)->latest();
    }

    //Lista del area ejecutora para el expediente
    public function areaEjecutora()
    {
        return $this->belongsTo(\App\Models\Institucion::class, 'area_ejecutora');
    }

    // ===================== Scopes =====================

    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeConEstado($query, $estado)
    {
        return $query->where('estatus', $estado);
    }

    // ===================== Helpers de estado =====================

    public function esBorrador(): bool
    {
        return $this->estatus === self::ESTADO_BORRADOR;
    }

    public function estaEnValidacion(): bool
    {
        return $this->estatus === self::ESTADO_EN_VALIDACION;
    }

    public function estaAprobado(): bool
    {
        return $this->estatus === self::ESTADO_APROBADO;
    }

    public function estaRechazado(): bool
    {
        return $this->estatus === self::ESTADO_RECHAZADO;
    }

    // ===================== Observaciones de rechazado =====================

    public function ultimaObservacionRechazo(): ?string
    {
        return $this->historiales()
            ->where('estado_nuevo', self::ESTADO_RECHAZADO)
            ->latest()
            ->value('observaciones');
    }

    public function fechaUltimoRechazo(): ?\Illuminate\Support\Carbon
    {
        return $this->historiales()
            ->where('estado_nuevo', self::ESTADO_RECHAZADO)
            ->latest()
            ->value('created_at');
    }

    /**
     * Permite enviar/re-enviar a validación solo si:
     */
    public function puedeEnviarValidacion(): bool
    {
        if ($this->esBorrador()) return true;

        if ($this->estaRechazado()) {
            $rechazoAt = $this->fechaUltimoRechazo();
            if (!$rechazoAt) return true;

            // Si se editó después del rechazo, ya puede reenviar
            return $this->updated_at && $this->updated_at->gt($rechazoAt);
        }

        return false;
    }

    public function ultimaObservacionRechazoCorta(int $len = 120): ?string
    {
        $obs = $this->ultimaObservacionRechazo();
        return $obs ? Str::limit($obs, $len) : null;
    }

    // ===================== Expediente segunda parte =====================
    public function detalle()
    {
        return $this->hasOne(\App\Models\ExpedienteDetalle::class);
    }

    public function estructuraProgramatica()
    {
        return $this->hasMany(\App\Models\ExpedienteEstructuraProgramatica::class)->orderBy('orden');
    }

    public function especificaciones()
    {
        return $this->hasMany(\App\Models\ExpedienteEspecificacion::class)->orderBy('orden');
    }

    public function entregables()
    {
        return $this->hasMany(\App\Models\ExpedienteEntregable::class)->orderBy('orden');
    }
}