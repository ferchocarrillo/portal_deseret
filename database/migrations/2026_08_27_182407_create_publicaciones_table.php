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
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();

            // ==========================================================
            // PROPIETARIO
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ==========================================================
            // TIPO DE PUBLICACIÓN
            // ==========================================================

            // empleo
            // servicio
            // curso
            // marketplace
            // emprendimiento
            $table->string('tipo', 50)->index();

            // ==========================================================
            // INFORMACIÓN GENERAL
            // ==========================================================

            $table->string('titulo', 255);

            $table->text('descripcion')->nullable();

            // ==========================================================
            // ESTADO
            // ==========================================================

            // pendiente
            // aprobada
            // rechazada
            // suspendida
            // finalizada

            $table->string('estado', 30)
                ->default('pendiente')
                ->index();

            // ==========================================================
            // VISIBILIDAD
            // ==========================================================

            $table->boolean('visible')
                ->default(false)
                ->index();

            // ==========================================================
            // VIGENCIA
            // ==========================================================

            $table->timestamp('fecha_publicacion')
                ->nullable()
                ->index();

            $table->timestamp('fecha_expiracion')
                ->nullable()
                ->index();

            // ==========================================================
            // REVISIÓN ADMINISTRATIVA
            // ==========================================================

            $table->foreignId('revisado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('fecha_revision')
                ->nullable();

            $table->text('observacion_revision')
                ->nullable();

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
        Schema::dropIfExists('publicaciones');
    }
};
