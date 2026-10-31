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
        Schema::create('oferta_empleo_preguntas', function (Blueprint $table) {

            $table->id();


            // ==========================================================
            // OFERTA DE EMPLEO
            // ==========================================================

            $table->foreignId('oferta_empleo_id')
                ->constrained('ofertas_empleo')
                ->cascadeOnDelete();


            // ==========================================================
            // PREGUNTA
            // ==========================================================

            $table->text('pregunta');


            // ==========================================================
            // TIPO DE RESPUESTA
            // ==========================================================

            $table->string('tipo', 30);


            // ==========================================================
            // CONFIGURACIÓN
            // ==========================================================

            $table->boolean('obligatoria')
                ->default(true);

            $table->boolean('es_filtro')
                ->default(false);


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
                'oferta_empleo_id',
                'orden',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oferta_empleo_preguntas');
    }
};

