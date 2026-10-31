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
        Schema::create('reacciones', function (Blueprint $table) {
            $table->id();

            // ==========================================================
            // USUARIO
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ==========================================================
            // PUBLICACIÓN
            // ==========================================================

            $table->foreignId('publicacion_id')
                ->constrained('publicaciones')
                ->cascadeOnDelete();

            // ==========================================================
            // TIPO DE REACCIÓN
            // ==========================================================

            // like
            // dislike

            $table->string('tipo', 20);

            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();

            // ==========================================================
            // RESTRICCIÓN
            // ==========================================================

            // Un usuario solamente puede tener una reacción
            // sobre una misma publicación.

            $table->unique([
                'user_id',
                'publicacion_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reacciones');
    }
};
