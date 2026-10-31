<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oferta_empleo_skills', function (Blueprint $table) {

            $table->id();


            // ==========================================================
            // OFERTA DE EMPLEO
            // ==========================================================

            $table->foreignId('oferta_empleo_id')
                ->constrained('ofertas_empleo')
                ->cascadeOnDelete();


            // ==========================================================
            // SKILL
            // ==========================================================

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();


            // ==========================================================
            // NIVEL REQUERIDO
            // ==========================================================

            $table->string('nivel_requerido', 30)
                ->nullable();


            // ==========================================================
            // REQUISITO
            // ==========================================================

            $table->boolean('es_obligatorio')
                ->default(true);


            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();


            // ==========================================================
            // REGLA:
            // UNA SKILL NO PUEDE REPETIRSE EN LA MISMA OFERTA
            // ==========================================================

            $table->unique([
                'oferta_empleo_id',
                'skill_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oferta_empleo_skills');
    }
};


