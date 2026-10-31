<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'skills';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [
        'skill_subcategory_id',
        'name',
        'slug',
        'description',
        'is_active',
        'is_featured',
        'sort_order',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }


    // ==========================================================
    // RELACIÓN: SUBCATEGORÍA
    // ==========================================================

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(
            SkillSubcategory::class,
            'skill_subcategory_id'
        );
    }


    // ==========================================================
    // RELACIÓN: USUARIOS
    // ==========================================================

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_skills',
            'skill_id',
            'user_id'
        )->withTimestamps();
    }


    // ==========================================================
    // RELACIÓN: OFERTAS DE EMPLEO
    // ==========================================================

    public function ofertasEmpleo(): BelongsToMany
    {
        return $this->belongsToMany(
            OfertaEmpleo::class,
            'oferta_empleo_skills',
            'skill_id',
            'oferta_empleo_id'
        )
            ->withPivot([
                'nivel_requerido',
                'es_obligatorio',
            ])
            ->withTimestamps();
    }
}
