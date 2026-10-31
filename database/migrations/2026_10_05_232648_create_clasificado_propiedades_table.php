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
        Schema::create('clasificado_propiedades', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Identificación
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con el clasificado
            |--------------------------------------------------------------------------
            |
            | Una publicación puede tener una sola ficha de propiedad.
            |
            */

            $table->foreignId('clasificado_id')
                ->unique()
                ->constrained('clasificados')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Características de la propiedad
            |--------------------------------------------------------------------------
            */

            $table->decimal('area_total', 12, 2)
                ->nullable();

            $table->decimal('area_construida', 12, 2)
                ->nullable();

            $table->unsignedTinyInteger('habitaciones')
                ->nullable();

            $table->unsignedTinyInteger('banos')
                ->nullable();

            $table->unsignedTinyInteger('garajes')
                ->nullable();

            $table->unsignedTinyInteger('pisos')
                ->nullable();

            $table->unsignedTinyInteger('piso')
                ->nullable();

            $table->boolean('tiene_ascensor')
                ->nullable();

            $table->boolean('tiene_parqueadero')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Información adicional
            |--------------------------------------------------------------------------
            */

            $table->string('estrato')
                ->nullable();

            $table->string('antiguedad')
                ->nullable();

            $table->boolean('remodelada')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Servicios y características especiales
            |--------------------------------------------------------------------------
            */

            $table->boolean('tiene_servicios_publicos')
                ->nullable();

            $table->boolean('tiene_agua')
                ->nullable();

            $table->boolean('tiene_energia')
                ->nullable();

            $table->boolean('tiene_gas')
                ->nullable();

            $table->boolean('tiene_alcantarillado')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Información jurídica
            |--------------------------------------------------------------------------
            */

            $table->boolean('papeles_al_dia')
                ->nullable();

            $table->boolean('libre_gravamen')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Observaciones específicas
            |--------------------------------------------------------------------------
            */

            $table->text('observaciones')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('area_total');
            $table->index('area_construida');
            $table->index('habitaciones');
            $table->index('banos');
            $table->index('garajes');
            $table->index('estrato');
            $table->index('papeles_al_dia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clasificado_propiedades');
    }
};
