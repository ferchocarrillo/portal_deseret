<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInfraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'moderator_id',
        'rule_id',
        'detail',
        'strike_level',
        'read_at',
    ];

    protected $casts = [
        'rule_id' => 'integer',
        'strike_level' => 'integer',
        'read_at' => 'datetime',
    ];

    /**
     * Usuario que cometió la infracción.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Moderador o administrador que registró la sanción.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Scope para consultar únicamente infracciones que el usuario no ha reconocido.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Obtiene los datos de la regla infringida desde el archivo de configuración.
     */
    public function getRuleAttribute(): ?array
    {
        return config("community_rules.rules.{$this->rule_id}");
    }

    /**
     * Obtiene el texto de la consecuencia según el nivel de strike.
     */
    public function getConsequenceAttribute(): ?string
    {
        return config("community_rules.consequences.{$this->strike_level}");
    }
}
