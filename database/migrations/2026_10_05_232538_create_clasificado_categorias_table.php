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
        Schema::create('clasificado_categorias', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Categoría padre
            |--------------------------------------------------------------------------
            |
            | Permite construir categorías jerárquicas:
            |
            | Vehículos
            | ├── Automóviles
            | ├── Motocicletas
            | └── Maquinaria
            |
            | Propiedades
            | ├── Vivienda
            | ├── Terrenos
            | └── Comerciales
            |
            */
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('clasificado_categorias')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Información de la categoría
            |--------------------------------------------------------------------------
            */

            $table->string('nombre');

            $table->string('slug')
                ->unique();

            $table->text('descripcion')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            $table->boolean('activo')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Orden
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('orden')
                ->default(0);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('parent_id');
            $table->index(['activo', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clasificado_categorias');
    }
};
