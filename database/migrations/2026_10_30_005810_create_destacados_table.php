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
        Schema::create('destacados', function (Blueprint $table) {
            $table->id();

            // ==========================================================
            // RELACIÓN POLIMÓRFICA (Soporta Servicios, Requerimientos, Emprendimientos, etc.)
            // ==========================================================
            $table->morphs('destacable'); // Crea 'destacable_type' y 'destacable_id' con su respectivo índice

            // ==========================================================
            // MÓDULO Y CATEGORÍA PARA EL BANCO DE IMÁGENES
            // ==========================================================
            $table->string('modulo', 50)->index(); // Ej: Servicios, Emprendimientos, Marketplace
            $table->string('categoria_slug', 100)->nullable(); // Ej: construccion-y-hogar (para el banco de imágenes)

            // ==========================================================
            // TIPO DE DESTAQUE Y REGLA
            // ==========================================================
            $table->string('tipo', 30)->index(); // manual, automatico, invitaciones
            $table->string('regla', 100)->nullable(); // administrador, promocion, etc.

            // ==========================================================
            // VIGENCIA Y ESTADO
            // ==========================================================
            $table->timestamp('fecha_inicio')->nullable()->index();
            $table->timestamp('fecha_fin')->nullable()->index();
            $table->boolean('activo')->default(true)->index();

            // ==========================================================
            // ADMINISTRADOR QUE DESTACÓ
            // ==========================================================
            $table->foreignId('destacado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ==========================================================
            // FECHAS Y MANTENIMIENTO
            // ==========================================================
            $table->timestamps();

            // Índice compuesto para optimizar búsquedas rápidas en el Dashboard
            $table->index(['activo', 'fecha_inicio', 'fecha_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destacados');
    }
};