<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Models\User;
use App\Models\Favorito;
use App\Models\Reaccion;
use App\Models\Comentario;
use App\Models\Calificacion;
use App\Models\Interaccion;
use App\Models\Reporte;
use App\Models\Destacado;
use App\Models\Invitacion;

class Publicacion extends Model
{
    use HasFactory;

    protected $table = 'publicaciones';


    // ==========================================================
    // ESTADOS
    // ==========================================================

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_APROBADA = 'aprobada';

    public const ESTADO_RECHAZADA = 'rechazada';

    public const ESTADO_SUSPENDIDA = 'suspendida';

    public const ESTADO_FINALIZADA = 'finalizada';


    // ==========================================================
    // TIPOS DE PUBLICACIÓN
    // ==========================================================

    public const TIPO_EMPLEO = 'empleo';

    public const TIPO_SERVICIO = 'servicio';

    public const TIPO_CURSO = 'curso';

    public const TIPO_MARKETPLACE = 'marketplace';

    public const TIPO_EMPRENDIMIENTO = 'emprendimiento';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [

        'user_id',

        'tipo',

        'titulo',

        'descripcion',

        'estado',

        'visible',

        'fecha_publicacion',

        'fecha_expiracion',

        'revisado_por',

        'fecha_revision',

        'observacion_revision',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected $casts = [

        'visible' => 'boolean',

        'fecha_publicacion' => 'datetime',

        'fecha_expiracion' => 'datetime',

        'fecha_revision' => 'datetime',
    ];


    // ==========================================================
    // RELACIÓN: PROPIETARIO
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
    // RELACIÓN: FAVORITOS
    // ==========================================================

    public function favoritos(): HasMany
    {
        return $this->hasMany(
            Favorito::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: REACCIONES
    // ==========================================================

    public function reacciones(): HasMany
    {
        return $this->hasMany(
            Reaccion::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: COMENTARIOS
    // ==========================================================

    public function comentarios(): HasMany
    {
        return $this->hasMany(
            Comentario::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: CALIFICACIONES
    // ==========================================================

    public function calificaciones(): MorphMany
    {
        return $this->morphMany(
            Calificacion::class,
            'calificable'
        );
    }


    // ==========================================================
    // RELACIÓN: INTERACCIONES
    // ==========================================================

    public function interacciones(): HasMany
    {
        return $this->hasMany(
            Interaccion::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: REPORTES
    // ==========================================================

    public function reportes(): HasMany
    {
        return $this->hasMany(
            Reporte::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: DESTACADOS
    // ==========================================================

    public function destacados(): HasMany
    {
        return $this->hasMany(
            Destacado::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: INVITACIONES
    // ==========================================================

    public function invitaciones(): HasMany
    {
        return $this->hasMany(
            Invitacion::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // SCOPES
    // ==========================================================

    /**
     * Publicaciones aprobadas y visibles.
     */
    public function scopePublicadas($query)
    {
        return $query
            ->where('estado', self::ESTADO_APROBADA)
            ->where('visible', true);
    }


    /**
     * Publicaciones pendientes de revisión.
     */
    public function scopePendientes($query)
    {
        return $query->where(
            'estado',
            self::ESTADO_PENDIENTE
        );
    }


    /**
     * Publicaciones de un tipo determinado.
     */
    public function scopeDelTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }


    // ==========================================================
    // MÉTODOS AUXILIARES
    // ==========================================================

    /**
     * Determina si la publicación está publicada.
     */
    public function estaPublicada(): bool
    {
        return $this->estado === self::ESTADO_APROBADA
            && $this->visible === true;
    }


    /**
     * Determina si la publicación está pendiente.
     */
    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }


    /**
     * Determina si la publicación está destacada actualmente.
     */
    public function estaDestacada(): bool
    {
        return $this->destacados()
            ->where('activo', true)
            ->where('fecha_inicio', '<=', now())
            ->where(function ($query) {
                $query->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now());
            })
            ->exists();
    }
}
