<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    // ==========================================================
    // BANCO DE SKILLS
    // ==========================================================

    public function index(): JsonResponse
    {
        $categories = SkillCategory::query()
            ->where('is_active', true)
            ->with([
                'subcategories' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->with([
                            'skills' => function ($query) {
                                $query
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->orderBy('name');
                            }
                        ]);
                }
            ])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'categories' => $categories,
            'maxSkills' => 25,
        ]);
    }
    // ==========================================================
    // CREAR NUEVA SKILL
    // ==========================================================

    public function createSkill(Request $request): JsonResponse
    {
        // ======================================================
        // VALIDACIÓN
        // ======================================================

        $validated = $request->validate([

            'skill_subcategory_id' => [
                'required',
                'integer',
                Rule::exists('skill_subcategories', 'id')
                    ->where(function ($query) {
                        $query->where('is_active', true);
                    }),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);


        // ======================================================
        // LIMPIAR NOMBRE
        // ======================================================

        $name = trim($validated['name']);


        // ======================================================
        // GENERAR SLUG
        // ======================================================

        $slug = \Illuminate\Support\Str::slug($name);


        // ======================================================
        // VERIFICAR QUE NO EXISTA EN LA SUBCATEGORÍA
        // ======================================================

        $existe = Skill::query()
            ->where('skill_subcategory_id', $validated['skill_subcategory_id'])
            ->where(function ($query) use ($name, $slug) {
                $query
                    ->where('name', $name)
                    ->orWhere('slug', $slug);
            })
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Esta skill ya existe dentro de la subcategoría seleccionada.',
            ], 422);
        }


        // ======================================================
        // CREAR SKILL
        // ======================================================

        $skill = Skill::create([
            'skill_subcategory_id' => $validated['skill_subcategory_id'],
            'name' => $name,
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ]);


        // ======================================================
        // RESPUESTA
        // ======================================================

        return response()->json([
            'success' => true,
            'message' => 'La skill fue creada correctamente.',
            'skill' => $skill->load('subcategory'),
        ], 201);
    }

    // ==========================================================
    // GUARDAR SKILLS DEL USUARIO
    // ==========================================================

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // ======================================================
        // VALIDACIÓN
        // ======================================================

        $validated = $request->validate([

            'skills' => [
                'required',
                'array',
                'max:25',
            ],

            'skills.*.skill_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('skills', 'id')
                    ->where(function ($query) {
                        $query->where('is_active', true);
                    }),
            ],

            'skills.*.nivel' => [
                'required',
                'string',
                Rule::in([
                    'Básico',
                    'Intermedio',
                    'Avanzado',
                    'Experto',
                ]),
            ],

        ]);


        // ======================================================
        // GUARDAR RELACIONES
        // ======================================================

        DB::transaction(function () use ($user, $validated) {

            $skills = collect($validated['skills'])
                ->mapWithKeys(function ($skill) {

                    return [
                        $skill['skill_id'] => [
                            'nivel' => $skill['nivel'],
                        ],
                    ];
                })
                ->toArray();


            $user->skills()->sync($skills);
        });


        // ======================================================
        // RESPUESTA
        // ======================================================

        return response()->json([
            'success' => true,
            'message' => 'Skills actualizadas correctamente.',
        ]);
    }


    // ==========================================================
    // ELIMINAR SKILL DEL USUARIO
    // ==========================================================

    public function destroy(string $id): JsonResponse
    {
        $user = request()->user();

        // ======================================================
        // VALIDAR QUE LA SKILL EXISTA
        // ======================================================

        $skill = Skill::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();


        // ======================================================
        // ELIMINAR RELACIÓN
        // ======================================================

        $user->skills()->detach($skill->id);


        // ======================================================
        // RESPUESTA
        // ======================================================

        return response()->json([
            'success' => true,
            'message' => 'Skill eliminada correctamente.',
        ]);
    }


    
}
