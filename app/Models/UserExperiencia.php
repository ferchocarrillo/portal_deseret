<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserExperiencia extends Model
{
    use HasFactory;

    protected $table = 'user_experiencias';

    /**
     * Campos asignables.
     */
    protected $fillable = [
        'user_id',
        'empresa',
        'cargo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'actualmente_trabaja',
        'sin_experiencia',
    ];

    /**
     * Conversión de tipos.
     */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'actualmente_trabaja' => 'boolean',
            'sin_experiencia' => 'boolean',
        ];
    }

    /**
     * Relación: usuario propietario de la experiencia.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
