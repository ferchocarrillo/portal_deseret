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
        Schema::create('marketplace_videos', function (Blueprint $table) {
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

            /*
            |--------------------------------------------------------------------------
            | Tamaño del archivo
            |--------------------------------------------------------------------------
            |
            | Se almacena en bytes.
            | Límite establecido: 50 MB.
            |
            */

            $table->unsignedBigInteger('tamano')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Duración
            |--------------------------------------------------------------------------
            |
            | Se almacena en segundos.
            | Límite establecido: 30 segundos.
            |
            */

            $table->unsignedSmallInteger('duracion')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Una publicación solamente puede tener un video
            |--------------------------------------------------------------------------
            */

            $table->unique('publicacion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_videos');
    }
};
