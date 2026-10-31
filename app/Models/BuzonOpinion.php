<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuzonOpinion extends Model
{
    use HasFactory;

    protected $table = 'buzon_opiniones';

    protected $fillable = [
        'user_id',
        'tipo',
        'modulo',
        'asunto',
        'mensaje',
        'leido',
        'leido_por',
        'leido_el',
    ];

    protected $casts = [
        'leido'    => 'boolean',
        'leido_el' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leido_por');
    }
}
