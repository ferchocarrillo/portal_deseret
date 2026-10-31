<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactoServicio extends Model
{
    use HasFactory;

    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'contactos_servicios';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'servicio_id',
        'user_id',
        'canal',
    ];


    // ==========================================================
    // RELACIONES
    // ==========================================================

    /**
     * Servicio al que se intentó contactar.
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    /**
     * Usuario que inició el contacto.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Reseñas asociadas a este intento de contacto.
     *
     * Puede tener una reseña de atención y otra
     * del servicio recibido.
     */
    public function resenas(): HasMany
    {
        return $this->hasMany(
            ResenaServicio::class,
            'contacto_servicio_id'
        );
    }
}
