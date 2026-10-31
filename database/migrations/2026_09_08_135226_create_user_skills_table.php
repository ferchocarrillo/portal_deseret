<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_skills', function (Blueprint $table) {

            $table->id();


            // ==========================================================
            // USUARIO
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();


            // ==========================================================
            // SKILL
            // ==========================================================

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();


            // ==========================================================
            // NIVEL DE DOMINIO
            // ==========================================================

            $table->string('nivel', 20)
                ->default('Intermedio');


            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();


            // ==========================================================
            // REGLA: UNA SKILL POR USUARIO
            // ==========================================================

            $table->unique([
                'user_id',
                'skill_id'
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('user_skills');
    }
};
