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
        Schema::create('marketplace_imagenes', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con la publicación
            |--------------------------------------------------------------------------
            */

            $table->foreignId('publicacion_id')
                ->constrained('marketplace_publicaciones')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Información del archivo
            |--------------------------------------------------------------------------
            */

            $table->string('ruta');
            $table->string('nombre_original')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamano')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Orden de las imágenes
            |--------------------------------------------------------------------------
            |
            | Permite determinar qué imagen aparece primero.
            |
            */

            $table->unsignedTinyInteger('orden')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Imagen principal
            |--------------------------------------------------------------------------
            */

            $table->boolean('es_principal')->default(false);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('publicacion_id');
            $table->index(['publicacion_id', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_imagenes');
    }
};
