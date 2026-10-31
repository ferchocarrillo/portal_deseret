<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Emprendimiento extends Model
{
    use HasFactory;

    protected $table = 'emprendimientos';

    protected $fillable = [
        'codigo_global', // <-- 1. Añadido para evitar el error NOT NULL
        'user_id',
        'categoria_id',
        'subcategoria_id',

        'nombre',
        'descripcion',
        'produccion_actual',
        'proyeccion',

        'necesidad',
        'propuesta_colaboracion',
        'tipo_colaboracion',

        'youtube_video_id',

        'activo',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'categoria_id' => 'integer',
        'subcategoria_id' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Boot del modelo para autogenerar el código global antes de crear.
     */
    protected static function booted(): void
    {
        static::creating(function (Emprendimiento $emprendimiento) {
            if (empty($emprendimiento->codigo_global)) {
                $emprendimiento->codigo_global = self::generarCodigoGlobal();
            }
        });
    }

    /**
     * Genera el consecutivo único con prefijo EMP (ej. EMP-000001).
     */
    public static function generarCodigoGlobal(): string
    {
        $ultimoEmprendimiento = self::query()->latest('id')->first();

        if (! $ultimoEmprendimiento || empty($ultimoEmprendimiento->codigo_global)) {
            $consecutivo = 1;
        } else {
            $numeroActual = (int) preg_replace('/[^0-9]/', '', $ultimoEmprendimiento->codigo_global);
            $consecutivo = $numeroActual + 1;
        }

        return sprintf('EMP-%06d', $consecutivo);
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(EmprendimientoCategoria::class, 'categoria_id');
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(EmprendimientoSubcategoria::class, 'subcategoria_id');
    }

    public function conversaciones(): HasMany
    {
        return $this->hasMany(EmprendimientoConversacion::class, 'emprendimiento_id');
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(EmprendimientoFavorito::class, 'emprendimiento_id');
    }

    public function denuncias(): HasMany
    {
        return $this->hasMany(EmprendimientoDenuncia::class, 'emprendimiento_id');
    }
}
