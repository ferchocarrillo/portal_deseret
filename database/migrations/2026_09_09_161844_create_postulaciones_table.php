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
        Schema::create('postulaciones', function (Blueprint $table) {

            $table->id();


            // ==========================================================
            // OFERTA DE EMPLEO
            // ==========================================================

            $table->foreignId('oferta_empleo_id')
                ->constrained('ofertas_empleo')
                ->cascadeOnDelete();


            // ==========================================================
            // USUARIO / POSTULANTE
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();


            // ==========================================================
            // ESTADO DE LA POSTULACIÓN
            // ==========================================================

            $table->string('estado', 30)
                ->default('postulado')
                ->index();


            // ==========================================================
            // OBSERVACIONES
            // ==========================================================

            $table->text('observaciones')
                ->nullable();


            // ==========================================================
            // FECHA DE POSTULACIÓN
            // ==========================================================

            $table->timestamp('fecha_postulacion');


            // ==========================================================
            // FECHAS DEL REGISTRO
            // ==========================================================

            $table->timestamps();


            // ==========================================================
            // REGLA:
            // UN USUARIO SOLO PUEDE POSTULARSE UNA VEZ
            // A LA MISMA OFERTA
            // ==========================================================

            $table->unique([
                'oferta_empleo_id',
                'user_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};


