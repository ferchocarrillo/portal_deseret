<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class EmprendimientoConversacion extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'emprendimiento_conversaciones';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'emprendimiento_id',
        'usuario_iniciador_id',
        'usuario_receptor_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'emprendimiento_id' => 'integer',
        'usuario_iniciador_id' => 'integer',
        'usuario_receptor_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Emprendimiento al que pertenece la conversación.
     */
    public function emprendimiento(): BelongsTo
    {
        return $this->belongsTo(
            Emprendimiento::class,
            'emprendimiento_id'
        );
    }

    /**
     * Usuario que inició la conversación.
     */
    public function usuarioIniciador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_iniciador_id'
        );
    }

    /**
     * Usuario propietario/receptor de la conversación.
     */
    public function usuarioReceptor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_receptor_id'
        );
    }

    /**
     * Mensajes de la conversación.
     */
    public function mensajes(): HasMany
    {
        return $this->hasMany(
            EmprendimientoMensaje::class,
            'conversacion_id'
        );
    }
}
