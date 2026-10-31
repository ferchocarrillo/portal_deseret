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
        Schema::create('ciudades', function (Blueprint $table) {

            $table->id();

            /*
             * País.
             */
            $table->foreignId('pais_id')
                ->constrained('paises')
                ->cascadeOnDelete();

            /*
             * División administrativa inmediata
             * a la que pertenece la ciudad/localidad.
             */
            $table->foreignId('division_id')
                ->constrained('divisiones_administrativas')
                ->cascadeOnDelete();

            /*
             * Nombre de la ciudad/localidad.
             */
            $table->string('nombre', 150);

            /*
             * Código oficial cuando exista.
             */
            $table->string('codigo', 50)
                ->nullable();

            /*
             * Tipo de localidad.
             *
             * Ejemplos:
             * ciudad
             * municipio
             * distrito
             * comuna
             * localidad
             * parroquia
             */
            $table->string('tipo', 80)
                ->nullable();

            /*
             * Coordenadas opcionales.
             *
             * No son necesarias para el selector,
             * pero nos dejan la puerta abierta para
             * mapas y búsquedas geográficas futuras.
             */
            $table->decimal('latitud', 10, 7)
                ->nullable();

            $table->decimal('longitud', 10, 7)
                ->nullable();

            $table->boolean('activo')
                ->default(true);

            $table->unsignedInteger('orden')
                ->default(0);

            $table->timestamps();

            $table->unique(
                ['division_id', 'nombre'],
                'ciudades_division_nombre_unique'
            );

            $table->index([
                'pais_id',
                'division_id',
                'activo',
            ]);

            $table->index('nombre');
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudades');
    }
};
