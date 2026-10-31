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
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();

            // USUARIO
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // PUBLICACIÓN
            $table->foreignId('publicacion_id')
                ->constrained('publicaciones')
                ->cascadeOnDelete();

            // MÓDULO DE PROCEDENCIA (Obligatorio, cada módulo envía el suyo)
            $table->string('modulo', 50);

            // FECHAS
            $table->timestamps();

            // RESTRICCIÓN: Un usuario no puede duplicar el favorito de una misma publicación en el mismo módulo
            $table->unique([
                'user_id',
                'publicacion_id',
                'modulo',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};
