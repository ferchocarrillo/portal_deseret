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
        Schema::create('user_experiencias', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Información de la experiencia laboral
            $table->string('empresa')->nullable();

            $table->string('cargo')->nullable();

            $table->text('descripcion')->nullable();

            $table->date('fecha_inicio')->nullable();

            $table->date('fecha_fin')->nullable();

            $table->boolean('actualmente_trabaja')
                ->default(false);

            // El usuario declara que no tiene experiencia laboral
            $table->boolean('sin_experiencia')
                ->default(false);

            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_experiencias');
    }
};
