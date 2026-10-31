<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventoConversacion extends Model
{
    use HasFactory;

    protected $table = 'evento_conversaciones';

    protected $fillable = [
        'evento_id',
        'comprador_id',
        'organizador_id',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function comprador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    public function organizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(EventoMensaje::class, 'evento_conversacion_id');
    }
}
