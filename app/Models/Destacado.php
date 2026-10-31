<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Destacado extends Model
{
    use HasFactory;

    protected $table = 'destacados';

    // ==========================================================
    // TIPOS DE DESTAQUE
    // ==========================================================

    public const TIPO_MANUAL = 'manual';

    public const TIPO_AUTOMATICO = 'automatico';

    public const TIPO_INVITACIONES = 'invitaciones';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'destacable_id',
        'destacable_type',
        'modulo',
        'categoria_slug',
        'tipo',
        'regla',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'destacado_por',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activo' => 'boolean',
    ];


    // ==========================================================
    // RELACIÓN POLIMÓRFICA: DESTACABLE (Servicios, Requerimientos, Emprendimientos, etc.)
    // ==========================================================

    public function destacable(): MorphTo
    {
        return $this->morphTo();
    }


    // ==========================================================
    // RELACIÓN: ADMINISTRADOR
    // ==========================================================

    public function administrador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'destacado_por'
        );
    }


    // ==========================================================
    // MÉTODOS AUXILIARES
    // ==========================================================

    /**
     * Determina si el destaque está actualmente activo.
     */
    public function estaActivo(): bool
    {
        if (!$this->activo) {
            return false;
        }

        if (
            $this->fecha_fin !== null &&
            $this->fecha_fin->isPast()
        ) {
            return false;
        }

        return $this->fecha_inicio === null || $this->fecha_inicio->isPast();
    }


    /**
     * Determina si fue realizado manualmente.
     */
    public function esManual(): bool
    {
        return $this->tipo === self::TIPO_MANUAL;
    }


    /**
     * Determina si fue generado automáticamente.
     */
    public function esAutomatico(): bool
    {
        return $this->tipo === self::TIPO_AUTOMATICO;
    }


    /**
     * Determina si fue obtenido mediante invitaciones.
     */
    public function esPorInvitaciones(): bool
    {
        return $this->tipo === self::TIPO_INVITACIONES;
    }
}
