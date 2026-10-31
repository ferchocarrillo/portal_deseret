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
        Schema::create('denuncias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_unico')->unique(); // Ej: DEN-2026-XXXX
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Usuario que reporta

            // Módulo de origen (marketplace, clasificados, servicios, emprendimientos, empleos, etc.)
            $table->string('modulo');

            // Relación polimórfica opcional para apuntar directamente al ítem reportado
            $table->nullableMorphs('reportable');

            $table->string('motivo'); // Categoría de la denuncia
            $table->text('descripcion'); // Detalles del incidente
            $table->string('evidencia_path')->nullable(); // Ruta del archivo adjunto

            // Gestión, seguimiento y cierre de la incidencia
            $table->string('estado')->default('pendiente'); // pendiente, en_revision, resuelto, descartado
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete(); // Administrador a cargo
            $table->text('notas_internas')->nullable(); // Comentarios de seguimiento del admin
            $table->timestamp('fecha_cierre')->nullable(); // Fecha y hora de resolución o cierre

            $table->timestamps(); // Incluye created_at (fecha y hora exacta de reporte) y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};
