<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoMensaje extends Model
{
    use HasFactory;

    protected $table = 'evento_mensajes';

    protected $fillable = [
        'evento_conversacion_id',
        'user_id',
        'cuerpo',
        'leido_at',
    ];

    protected function casts(): array
    {
        return [
            'leido_at' => 'datetime',
        ];
    }

    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(EventoConversacion::class, 'evento_conversacion_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
