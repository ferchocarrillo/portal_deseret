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
        Schema::create('comentarios', function (Blueprint $table) {
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

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ==========================================================
            // CONTENIDO
            // ==========================================================

            $table->text('contenido');

            // ==========================================================
            // MODERACIÓN
            // ==========================================================

            // activo
            // oculto
            // eliminado
            // reportado

            $table->string('estado', 20)
                ->default('activo')
                ->index();

            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
