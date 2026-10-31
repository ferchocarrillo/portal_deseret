<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicioMensaje extends Model
{
    use HasFactory;

    protected $table = 'servicios_mensajes';

    protected $fillable = [
        'conversacion_id',
        'user_id',
        'mensaje',
        'leido_at',
        'editado_at',
    ];

    protected function casts(): array
    {
        return [
            'leido_at' => 'datetime',
            'editado_at' => 'datetime',
        ];
    }

    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(ServicioConversacion::class, 'conversacion_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
