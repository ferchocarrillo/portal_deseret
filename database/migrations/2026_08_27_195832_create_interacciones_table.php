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
        Schema::create('interacciones', function (Blueprint $table) {
            $table->id();

            // ==========================================================
            // PUBLICACIÓN
            // ==========================================================

            $table->foreignId('publicacion_id')
                ->constrained('publicaciones')
                ->cascadeOnDelete();

            // ==========================================================
            // USUARIO
            // ==========================================================

            // Puede ser null porque una persona no autenticada
            // también puede generar determinadas interacciones,
            // como una visualización.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ==========================================================
            // TIPO DE INTERACCIÓN
            // ==========================================================

            // view      = visualización
            // click     = clic
            // share     = compartir
            // contact   = contacto
            // favorite  = favorito
            // apply     = postulación
            // purchase  = compra
            // enroll    = inscripción

            $table->string('tipo', 30)->index();

            // ==========================================================
            // INFORMACIÓN TÉCNICA
            // ==========================================================

            // Identificador anónimo para reconocer actividad
            // de visitantes que no han iniciado sesión.
            $table->string('visitor_id', 100)
                ->nullable()
                ->index();

            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();

            // ==========================================================
            // ÍNDICES
            // ==========================================================

            $table->index([
                'publicacion_id',
                'tipo',
                'created_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interacciones');
    }
};
