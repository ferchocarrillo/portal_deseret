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
        Schema::create('invitaciones', function (Blueprint $table) {
            $table->id();

            // ==========================================================
            // PUBLICACIÓN
            // ==========================================================

            $table->foreignId('publicacion_id')
                ->constrained('publicaciones')
                ->cascadeOnDelete();

            // ==========================================================
            // USUARIO QUE INVITA
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ==========================================================
            // USUARIO INVITADO
            // ==========================================================

            // Será null mientras la persona solamente haya visitado
            // el enlace y todavía no tenga una cuenta.

            $table->foreignId('usuario_invitado_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ==========================================================
            // CÓDIGO DE INVITACIÓN
            // ==========================================================

            // Código único que identifica el enlace compartido.

            $table->string('codigo', 100)
                ->unique();

            // ==========================================================
            // ESTADO
            // ==========================================================

            // pendiente
            // registrada
            // validada
            // rechazada
            // cancelada

            $table->string('estado', 30)
                ->default('pendiente')
                ->index();

            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamp('fecha_registro')
                ->nullable();

            $table->timestamp('fecha_validacion')
                ->nullable();

            $table->timestamps();

            // ==========================================================
            // ÍNDICES
            // ==========================================================

            $table->index([
                'publicacion_id',
                'estado',
            ]);

            $table->index([
                'user_id',
                'estado',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitaciones');
    }
};
