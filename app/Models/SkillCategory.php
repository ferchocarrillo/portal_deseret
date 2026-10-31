<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillCategory extends Model
{
    use HasFactory;

    protected $table = 'skill_categories';

    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
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
    // RELACIÓN: SUBCATEGORÍAS
    // ==========================================================

    public function subcategories(): HasMany
    {
        return $this->hasMany(
            SkillSubcategory::class,
            'skill_category_id'
        );
    }
}
