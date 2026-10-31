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

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Cambiamos publicacion_id para que sea un entero simple sin llave foránea estricta a una sola tabla,
            // ya que los IDs provienen de tablas diferentes según el módulo.
            $table->unsignedBigInteger('publicacion_id');

            // Módulo de procedencia (ej. 'clasificados', 'marketplace', 'servicios', etc.)
            $table->string('modulo', 50);

            $table->timestamps();

            // Restricción única para evitar duplicados del mismo usuario en el mismo módulo
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
