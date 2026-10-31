<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicioConversacion extends Model
{
    use HasFactory;

    protected $table = 'servicios_conversaciones';

    protected $fillable = [
        'servicio_id',
        'usuario_interesado_id',
        'propietario_id',
        'ultimo_mensaje_at',
    ];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function interesado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_interesado_id');
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }

    public function mensajes(): HasMany
    {
        // 'conversacion_id' coincide con la migración de servicios_mensajes
        return $this->hasMany(ServicioMensaje::class, 'conversacion_id');
    }
}
