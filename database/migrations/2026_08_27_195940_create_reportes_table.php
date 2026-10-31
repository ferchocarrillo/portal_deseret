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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();

            // ==========================================================
            // PUBLICACIÓN REPORTADA
            // ==========================================================

            $table->foreignId('publicacion_id')
                ->constrained('publicaciones')
                ->cascadeOnDelete();

            // ==========================================================
            // USUARIO QUE REALIZA EL REPORTE
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ==========================================================
            // MOTIVO DEL REPORTE
            // ==========================================================

            $table->string('motivo', 100);

            // Explicación adicional proporcionada por el usuario.
            $table->text('descripcion')->nullable();

            // ==========================================================
            // ESTADO DEL REPORTE
            // ==========================================================

            // pendiente
            // en_revision
            // confirmado
            // descartado
            // cerrado

            $table->string('estado', 30)
                ->default('pendiente')
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

            // ==========================================================
            // ÍNDICES
            // ==========================================================

            $table->index([
                'publicacion_id',
                'estado',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
