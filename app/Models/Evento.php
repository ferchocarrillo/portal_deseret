<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'user_id',
        'pais_id',
        'division_administrativa_id',
        'ciudad_id',
        'titulo',
        'descripcion',
        'modalidad',
        'ubicacion',
        'enlace_virtual',
        'fecha_inicio',
        'fecha_fin',
        'es_gratuito',
        'precio',
        'precio_preventa',
        'fecha_limite_preventa',
        'precios_localidades',
        'capacidad',
        'imagen',
        'estado',
        'es_destacado',
        'rango_cobertura', // 👈 Añadido
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio'          => 'datetime',
            'fecha_fin'             => 'datetime',
            'fecha_limite_preventa' => 'datetime',
            'es_gratuito'           => 'boolean',
            'es_destacado'          => 'boolean',
            'precio'                => 'decimal:2',
            'precio_preventa'       => 'decimal:2',
            'precios_localidades'   => 'array', // Maneja el JSON de zonas y precios por ubicación
            'capacidad'             => 'integer',
        ];
    }

    /**
     * Relaciones
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id'); // Asegúrate de que tu modelo de ciudad sea Ciudad (o Ubicacion)
    }

    public function divisionAdministrativa(): BelongsTo
    {
        return $this->belongsTo(DivisionAdministrativa::class, 'division_administrativa_id');
    }

    /**
     * Scopes de consulta
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeDestacados($query)
    {
        return $query->where('es_destacado', true);
    }

    /**
     * Accessor inteligente para determinar si la preventa sigue activa
     */
    public function getPreventaActivaAttribute(): bool
    {
        if ($this->es_gratuito || !$this->precio_preventa) {
            return false;
        }

        if (!$this->fecha_limite_preventa) {
            return true; // Si hay precio preventa sin fecha límite, se asume activa
        }

        return now()->lte($this->fecha_limite_preventa);
    }

    /**
     * Obtiene el precio vigente actual (compara preventa vs precio normal)
     */
    public function getPrecioVigenteAttribute()
    {
        if ($this->es_gratuito) {
            return 0;
        }

        return $this->preventa_activa ? $this->precio_preventa : $this->precio;
    }

    /**
     * Conversaciones asociadas al evento.
     */
    public function conversaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventoConversacion::class, 'evento_id');
    }
}
