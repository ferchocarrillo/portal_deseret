<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentarios';


    // ==========================================================
    // ESTADOS
    // ==========================================================

    public const ESTADO_ACTIVO = 'activo';

    public const ESTADO_OCULTO = 'oculto';

    public const ESTADO_ELIMINADO = 'eliminado';

    public const ESTADO_REPORTADO = 'reportado';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'publicacion_id',
        'user_id',
        'contenido',
        'estado',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
    // RELACIÓN: USUARIO
    // ==========================================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    // ==========================================================
    // MÉTODOS AUXILIARES
    // ==========================================================

    /**
     * Determina si el comentario está visible.
     */
    public function estaActivo(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }


    /**
     * Determina si el comentario está oculto.
     */
    public function estaOculto(): bool
    {
        return $this->estado === self::ESTADO_OCULTO;
    }


    /**
     * Determina si el comentario fue eliminado.
     */
    public function estaEliminado(): bool
    {
        return $this->estado === self::ESTADO_ELIMINADO;
    }


    /**
     * Determina si el comentario fue reportado.
     */
    public function estaReportado(): bool
    {
        return $this->estado === self::ESTADO_REPORTADO;
    }
}
