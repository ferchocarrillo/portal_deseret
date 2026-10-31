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
        Schema::create('postulacion_respuestas', function (Blueprint $table) {

            $table->id();


            // ==========================================================
            // POSTULACIÓN
            // ==========================================================

            $table->foreignId('postulacion_id')
                ->constrained('postulaciones')
                ->cascadeOnDelete();


            // ==========================================================
            // PREGUNTA
            // ==========================================================

            $table->foreignId('pregunta_id')
                ->constrained('oferta_empleo_preguntas')
                ->cascadeOnDelete();


            // ==========================================================
            // RESPUESTA
            // ==========================================================

            $table->longText('respuesta')
                ->nullable();


            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();


            // ==========================================================
            // REGLA:
            // UNA RESPUESTA POR PREGUNTA EN CADA POSTULACIÓN
            // ==========================================================

            $table->unique([
                'postulacion_id',
                'pregunta_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulacion_respuestas');
    }
};

