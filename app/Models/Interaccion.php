<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaccion extends Model
{
    use HasFactory;

    protected $table = 'interacciones';

    // ==========================================================
    // TIPOS DE INTERACCIÓN
    // ==========================================================

    public const TIPO_VIEW = 'view';

    public const TIPO_CLICK = 'click';

    public const TIPO_SHARE = 'share';

    public const TIPO_CONTACT = 'contact';

    public const TIPO_FAVORITE = 'favorite';

    public const TIPO_APPLY = 'apply';

    public const TIPO_PURCHASE = 'purchase';

    public const TIPO_ENROLL = 'enroll';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'publicacion_id',
        'user_id',
        'tipo',
        'visitor_id',
    ];


    // ==========================================================
    // RELACIÓN: PUBLICACIÓN
    // ==========================================================

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(
            Publicacion::class,
            'publicacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: USUARIO
    // ==========================================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
