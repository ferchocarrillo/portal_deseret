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
        Schema::create('oferta_empleo_pregunta_opciones', function (Blueprint $table) {

            $table->id();


            // ==========================================================
            // PREGUNTA
            // ==========================================================

            $table->foreignId('pregunta_id')
                ->constrained('oferta_empleo_preguntas')
                ->cascadeOnDelete();


            // ==========================================================
            // OPCIÓN
            // ==========================================================

            $table->string('opcion');


            // ==========================================================
            // ORDEN
            // ==========================================================

            $table->unsignedInteger('orden')
                ->default(0);


            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();


            // ==========================================================
            // ÍNDICE
            // ==========================================================

            $table->index([
                'pregunta_id',
                'orden',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oferta_empleo_pregunta_opciones');
    }
};

