<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSkill extends Model
{
    use HasFactory;

    protected $table = 'user_skills';

    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [
        'user_id',
        'skill_id',
    ];

    // ==========================================================
    // RELACIÓN: USUARIO
    // ==========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    // ==========================================================
    // RELACIÓN: SKILL
    // ==========================================================

    public function skill(): BelongsTo
    {
        return $this->belongsTo(
            Skill::class,
            'skill_id'
        );
    }
    /**
     * Inscripciones realizadas por el usuario.
     */
    public function inscripcionesCursos(): HasMany
    {
        return $this->hasMany(InscripcionCurso::class, 'user_id');
    }
}
