<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Denuncia extends Model
{
    use HasFactory;

    protected $table = 'denuncias';

    protected $fillable = [
        'codigo_unico',
        'user_id',
        'modulo',
        'reportable_type',
        'reportable_id',
        'motivo',
        'descripcion',
        'evidencia_path',
        'estado',
        'admin_id',
        'notas_internas',
        'fecha_cierre',
    ];

    protected $casts = [
        'fecha_cierre' => 'datetime',
    ];

    /**
     * Usuario que realizó la denuncia.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Administrador encargado de gestionar y cerrar la incidencia.
     */
    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relación polimórfica para apuntar al elemento reportado (Producto, Servicio, etc.).
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
