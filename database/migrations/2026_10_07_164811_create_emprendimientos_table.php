<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprendimientos', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | IDENTIFICADOR GLOBAL
            |--------------------------------------------------------------------------
            */

            $table->string('codigo_global', 20)
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | PROPIETARIO
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | CLASIFICACIÓN
            |--------------------------------------------------------------------------
            */

            $table->foreignId('categoria_id')
                ->constrained('emprendimiento_categorias')
                ->restrictOnDelete();

            $table->foreignId('subcategoria_id')
                ->nullable()
                ->constrained('emprendimiento_subcategorias')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | INFORMACIÓN GENERAL
            |--------------------------------------------------------------------------
            */

            $table->string('nombre');

            $table->text('descripcion');

            $table->text('produccion_actual')
                ->nullable();

            $table->text('proyeccion')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | NECESIDAD Y PROPUESTA
            |--------------------------------------------------------------------------
            */

            $table->text('necesidad')
                ->nullable();

            $table->text('propuesta_colaboracion')
                ->nullable();

            $table->string('tipo_colaboracion')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | VIDEO DE YOUTUBE
            |--------------------------------------------------------------------------
            */

            $table->string('youtube_video_id');

            /*
            |--------------------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------------------
            */

            $table->boolean('activo')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | FECHAS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index([
                'user_id',
                'activo',
            ]);

            $table->index([
                'categoria_id',
                'activo',
            ]);

            $table->index([
                'subcategoria_id',
                'activo',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprendimientos');
    }
};
