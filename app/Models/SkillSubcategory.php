<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillSubcategory extends Model
{
    use HasFactory;

    protected $table = 'skill_subcategories';

    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [
        'skill_category_id',
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ==========================================================
    // RELACIÓN: CATEGORÍA
    // ==========================================================

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            SkillCategory::class,
            'skill_category_id'
        );
    }

    // ==========================================================
    // RELACIÓN: SKILLS
    // ==========================================================

    public function skills(): HasMany
    {
        return $this->hasMany(
            Skill::class,
            'skill_subcategory_id'
        );
    }
}
