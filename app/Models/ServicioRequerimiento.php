<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Builder;

class ServicioRequerimiento extends Model
{
    use HasFactory;

    protected $table = 'servicio_requerimientos';

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'categoria',
        'modalidad',
        'pais',
        'departamento',
        'ciudad',
        'presupuesto_tipo',
        'presupuesto_min',
        'presupuesto_max',
        'moneda',
        'urgencia',
        'fecha_limite',
        'estado',
        'es_destacado',
    ];

    protected function casts(): array
    {
        return [
            'presupuesto_min' => 'decimal:2',
            'presupuesto_max' => 'decimal:2',
            'fecha_limite'    => 'date',
            'es_destacado'    => 'boolean',
        ];
    }

    // ==========================================================
    // RELACIONES
    // ==========================================================

    /**
     * Usuario que publicó el requerimiento.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con la tabla polimórfica de destacados (si aplica en el sistema).
     */
    public function destacado(): MorphOne
    {
        return $this->morphOne(Destacado::class, 'destacable');
    }

    // ==========================================================
    // SCOPES DE CONSULTA
    // ==========================================================

    /**
     * Scope para filtrar requerimientos destacados.
     */
    public function scopeDestacados($query)
    {
        return $query->where('es_destacado', true);
    }

    /**
     * Scope para filtrar requerimientos abiertos.
     */
    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'abierto');
    }
}
