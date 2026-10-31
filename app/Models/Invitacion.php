<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitacion extends Model
{
    use HasFactory;

    protected $table = 'invitaciones';

    // ==========================================================
    // ESTADOS
    // ==========================================================

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_REGISTRADA = 'registrada';

    public const ESTADO_VALIDADA = 'validada';

    public const ESTADO_RECHAZADA = 'rechazada';

    public const ESTADO_CANCELADA = 'cancelada';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'publicacion_id',
        'user_id',
        'usuario_invitado_id',
        'codigo',
        'estado',
        'fecha_registro',
        'fecha_validacion',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected $casts = [
        'fecha_registro' => 'datetime',
        'fecha_validacion' => 'datetime',
    ];


    // ==========================================================
    // RELACIÓN: PUBLICACIÓN
    // ==========================================================

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(
            Publicacion::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: USUARIO QUE INVITA
    // ==========================================================

    public function invitador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    // ==========================================================
    // RELACIÓN: USUARIO INVITADO
    // ==========================================================

    public function invitado(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_invitado_id'
        );
    }


    // ==========================================================
    // MÉTODOS AUXILIARES
    // ==========================================================

    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }


    public function estaRegistrada(): bool
    {
        return $this->estado === self::ESTADO_REGISTRADA;
    }


    public function estaValidada(): bool
    {
        return $this->estado === self::ESTADO_VALIDADA;
    }


    public function fueRechazada(): bool
    {
        return $this->estado === self::ESTADO_RECHAZADA;
    }


    public function fueCancelada(): bool
    {
        return $this->estado === self::ESTADO_CANCELADA;
    }
}
