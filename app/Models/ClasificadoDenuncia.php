<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClasificadoDenuncia extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'clasificado_denuncias';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'clasificado_id',
        'user_id',
        'motivo',
        'descripcion',
        'estado',
        'respuesta_admin',
        'revisado_por',
        'revisada_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'revisada_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Clasificado denunciado
    |--------------------------------------------------------------------------
    */

    public function clasificado(): BelongsTo
    {
        return $this->belongsTo(
            Clasificado::class,
            'clasificado_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Usuario que realizó la denuncia
    |--------------------------------------------------------------------------
    */

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Administrador que revisó la denuncia
    |--------------------------------------------------------------------------
    */

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'revisado_por'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnRevision($query)
    {
        return $query->where('estado', 'en_revision');
    }

    public function scopeResueltas($query)
    {
        return $query->where('estado', 'resuelta');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function estaEnRevision(): bool
    {
        return $this->estado === 'en_revision';
    }

    public function estaResuelta(): bool
    {
        return $this->estado === 'resuelta';
    }

    public function estaRechazada(): bool
    {
        return $this->estado === 'rechazada';
    }
}
