<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'codigo_global',
        'user_id',
        'titulo',
        'slug',
        'descripcion',
        'imagen',
        'tipo',
        'url_externa',
        'es_gratuito',
        'precio',
        'modalidad',
        'pais',
        'departamento',
        'ciudad',
        'direccion',

        // NUEVOS CAMPOS GEOESPACIALES
        'mostrar_direccion',
        'latitud',
        'longitud',
        'radio_alcance_km',

        'enlace_virtual',
        'frecuencia_clases',
        'duracion_sesion_minutos',
        'intensidad_horaria',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite_inscripcion',
        'publicado_desde',
        'publicado_hasta',
        'cupos_ilimitados',
        'cantidad_cupos',
        'permite_lista_espera',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'es_gratuito' => 'boolean',
            'precio' => 'decimal:2',
            'frecuencia_clases' => 'integer',
            'duracion_sesion_minutos' => 'integer',
            'intensidad_horaria' => 'integer',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'fecha_limite_inscripcion' => 'datetime',
            'publicado_desde' => 'datetime',
            'publicado_hasta' => 'datetime',
            'cupos_ilimitados' => 'boolean',
            'cantidad_cupos' => 'integer',
            'permite_lista_espera' => 'boolean',
            'mostrar_direccion' => 'boolean', // Cast a booleano
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Curso $curso) {
            if (empty($curso->codigo_global)) {
                $curso->codigo_global = self::generarCodigoGlobal();
            }
        });
    }

    public static function generarCodigoGlobal(): string
    {
        $ultimoCurso = self::query()->latest('id')->first();

        if (! $ultimoCurso || empty($ultimoCurso->codigo_global)) {
            $consecutivo = 1;
        } else {
            $numeroActual = (int) preg_replace('/[^0-9]/', '', $ultimoCurso->codigo_global);
            $consecutivo = $numeroActual + 1;
        }

        return sprintf('CUR-%06d', $consecutivo);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionCurso::class, 'curso_id');
    }
}
