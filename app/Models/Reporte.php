<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    // ==========================================================
    // ESTADOS
    // ==========================================================

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_EN_REVISION = 'en_revision';

    public const ESTADO_CONFIRMADO = 'confirmado';

    public const ESTADO_DESCARTADO = 'descartado';

    public const ESTADO_CERRADO = 'cerrado';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'publicacion_id',
        'user_id',
        'motivo',
        'descripcion',
        'estado',
        'revisado_por',
        'fecha_revision',
        'observacion_revision',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected $casts = [
        'fecha_revision' => 'datetime',
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
    // RELACIÓN: USUARIO QUE REPORTÓ
    // ==========================================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    // ==========================================================
    // RELACIÓN: ADMINISTRADOR QUE REVISÓ
    // ==========================================================

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'revisado_por'
        );
    }


    // ==========================================================
    // MÉTODOS AUXILIARES
    // ==========================================================

    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }


    public function estaEnRevision(): bool
    {
        return $this->estado === self::ESTADO_EN_REVISION;
    }


    public function fueConfirmado(): bool
    {
        return $this->estado === self::ESTADO_CONFIRMADO;
    }


    public function fueDescartado(): bool
    {
        return $this->estado === self::ESTADO_DESCARTADO;
    }
}
