<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfertaEmpleo extends Model
{
    use HasFactory;

    protected $table = 'ofertas_empleo';

    protected $fillable = [
        'codigo_global', // <-- Agregado a fillable

        // Publicador
        'user_id',

        // Empresa
        'empresa_id',

        // Información general
        'titulo',
        'descripcion',

        // Ubicación
        'pais',
        'departamento',
        'ciudad',
        'direccion',

        // Modalidad
        'modalidad',

        'latitud',
        'longitud',
        'radio_alcance_km',

        // Contrato
        'tipo_contrato',

        // Experiencia
        'nivel_experiencia',
        'experiencia_minima_meses',

        // Salario
        'tipo_salario',
        'salario_monto',
        'salario_minimo',
        'salario_maximo',
        'mostrar_salario',

        // Estado
        'estado',

        // Fechas
        'fecha_publicacion',
        'fecha_cierre',
    ];

    protected function casts(): array
    {
        return [
            'experiencia_minima_meses' => 'integer',
            'salario_monto' => 'decimal:2',
            'salario_minimo' => 'decimal:2',
            'salario_maximo' => 'decimal:2',
            'mostrar_salario' => 'boolean',
            'mostrar_direccion' => 'boolean',
            'fecha_publicacion' => 'datetime',
            'fecha_cierre' => 'datetime',
        ];
    }

    /**
     * Boot del modelo para autogenerar el código global antes de crear.
     */
    protected static function booted(): void
    {
        static::creating(function (OfertaEmpleo $oferta) {
            if (empty($oferta->codigo_global)) {
                $oferta->codigo_global = self::generarCodigoGlobal();
            }
        });
    }

    /**
     * Genera el consecutivo único con prefijo OFE (ej. OFE-000001).
     */
    public static function generarCodigoGlobal(): string
    {
        $ultimaOferta = self::query()->latest('id')->first();

        if (! $ultimaOferta || empty($ultimaOferta->codigo_global)) {
            $consecutivo = 1;
        } else {
            $numeroActual = (int) preg_replace('/[^0-9]/', '', $ultimaOferta->codigo_global);
            $consecutivo = $numeroActual + 1;
        }

        return sprintf('OFE-%06d', $consecutivo);
    }

    // ==========================================================
    // RELACIONES
    // ==========================================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'oferta_empleo_skills',
            'oferta_empleo_id',
            'skill_id'
        )
            ->withPivot(['nivel_requerido', 'es_obligatorio'])
            ->withTimestamps();
    }

    public function ofertaEmpleoSkills(): HasMany
    {
        return $this->hasMany(OfertaEmpleoSkill::class, 'oferta_empleo_id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(OfertaEmpleoPregunta::class, 'oferta_empleo_id')
            ->orderBy('orden');
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class, 'oferta_empleo_id');
    }
}
