<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteError extends Model
{
    use HasFactory;

    protected $table = 'reportes_errores';

    protected $fillable = [
        'codigo_ticket',
        'user_id',
        'modulo',
        'titulo',
        'severidad',
        'descripcion',
        'pasos_reproducir',
        'url_afectada',
        'captura_path',
        'estado',
        'notas_solucion',
        'resuelto_por',
        'resuelto_el',
    ];

    protected $casts = [
        'resuelto_el' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resuelto_por');
    }
}
