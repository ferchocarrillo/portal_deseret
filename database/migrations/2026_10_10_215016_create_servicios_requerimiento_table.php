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
        Schema::create('servicio_requerimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('titulo', 150);
            $table->text('descripcion');
            $table->string('categoria', 100)->nullable();

            // Modalidad y Ubicación
            $table->enum('modalidad', ['presencial', 'remoto', 'hibrido'])->default('presencial');
            $table->string('pais', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('ciudad', 100)->nullable();

            // Presupuesto estimado / expectativa
            $table->enum('presupuesto_tipo', ['a_convenir', 'fijo', 'rango'])->default('a_convenir');
            $table->decimal('presupuesto_min', 12, 2)->nullable();
            $table->decimal('presupuesto_max', 12, 2)->nullable();
            $table->string('moneda', 3)->default('COP');

            // Urgencia / Tiempo
            $table->enum('urgencia', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->date('fecha_limite')->nullable();

            // Control y estados
            $table->enum('estado', ['abierto', 'en_proceso', 'resuelto', 'cancelado'])->default('abierto');
            $table->boolean('es_destacado')->default(true); // O mediante la tabla polimórfica de destacados
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios_requerimiento');
    }
};
