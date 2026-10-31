<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MarketplacePublicacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marketplace_publicaciones';

    protected $fillable = [
        'codigo_global',
        'user_id',
        'categoria_id',
        'subcategoria_id',
        'titulo',
        'slug',
        'descripcion',
        'precio',
        'es_gratis',
        'estado_producto',
        'estado_publicacion',
        'pais_id',
        'division_administrativa_id',
        'ciudad_id',
        'tipo_entrega',
        'rango_cobertura', // <--- Agregado
        'fecha_publicacion',
    ];

    /**
     * Boot del modelo para autogenerar el código global antes del INSERT.
     */
    protected static function booted(): void
    {
        static::creating(function (MarketplacePublicacion $publicacion) {
            if (empty($publicacion->codigo_global)) {
                $publicacion->codigo_global = self::generarCodigoGlobal();
            }
        });
    }

    /**
     * Genera el consecutivo único con prefijo MKT (ej. MKT-000001).
     */
    public static function generarCodigoGlobal(): string
    {
        $ultimaPublicacion = self::query()->latest('id')->first();

        if (! $ultimaPublicacion || empty($ultimaPublicacion->codigo_global)) {
            $consecutivo = 1;
        } else {
            $numeroActual = (int) preg_replace('/[^0-9]/', '', $ultimaPublicacion->codigo_global);
            $consecutivo = $numeroActual + 1;
        }

        return sprintf('MKT-%06d', $consecutivo);
    }

    // ==========================================================
    // RELACIONES DE USUARIO
    // ==========================================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ==========================================================
    // FAVORITOS (RESUELVE EL ERROR)
    // ==========================================================

    public function favoritos(): HasMany
    {
        return $this->hasMany(MarketplaceFavorito::class, 'publicacion_id');
    }

    // ==========================================================
    // RELACIONES DE CATEGORÍA
    // ==========================================================

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(MarketplaceCategoria::class, 'categoria_id');
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(MarketplaceSubcategoria::class, 'subcategoria_id');
    }

    // ==========================================================
    // MULTIMEDIA Y OPINIONES
    // ==========================================================

    public function imagenes(): HasMany
    {
        return $this->hasMany(MarketplaceImagen::class, 'publicacion_id');
    }

    public function video(): HasOne
    {
        return $this->hasOne(MarketplaceVideo::class, 'publicacion_id');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(MarketplaceCalificacion::class, 'publicacion_id');
    }

    // ==========================================================
    // DESTACADOS (POLIMÓRFICA)
    // ==========================================================

    public function destacado(): MorphOne
    {
        return $this->morphOne(Destacado::class, 'destacable');
    }

    // ==========================================================
    // UBICACIÓN
    // ==========================================================

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function divisionAdministrativa(): BelongsTo
    {
        return $this->belongsTo(DivisionAdministrativa::class, 'division_administrativa_id');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(DivisionAdministrativa::class, 'division_administrativa_id');
    }

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }
}
