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
        Schema::create('marketplace_favoritos', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Usuario que guarda la publicación
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Publicación guardada
            |--------------------------------------------------------------------------
            */

            $table->foreignId('publicacion_id')
                ->constrained('marketplace_publicaciones')
                ->cascadeOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Un usuario no puede guardar dos veces la misma publicación
            |--------------------------------------------------------------------------
            */

            $table->unique(['user_id', 'publicacion_id']);

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('user_id');
            $table->index('publicacion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_favoritos');
    }
};
