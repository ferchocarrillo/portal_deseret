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
        Schema::create('paises', function (Blueprint $table) {

            $table->id();

            $table->string('nombre', 150);

            $table->string('codigo_iso2', 2)
                ->unique();

            $table->string('codigo_iso3', 3)
                ->unique();

            $table->string('codigo_telefonico', 10)
                ->nullable();

            /*
             * Nombre del primer nivel administrativo.
             *
             * Ejemplos:
             * Departamento
             * Provincia
             * Región
             * Estado
             */
            $table->string('division_label', 100);

            /*
             * Nombre genérico utilizado para el último
             * nivel que mostraremos al usuario.
             *
             * Ejemplos:
             * Municipio / Ciudad
             * Cantón / Ciudad
             * Comuna
             * Distrito
             */
            $table->string('localidad_label', 100);

            /*
             * Permite determinar si el país está disponible
             * actualmente en Portal Deseret.
             */
            $table->boolean('activo')
                ->default(true);

            /*
             * Permite controlar el orden en el selector.
             *
             * Colombia tendrá 1 para aparecer primero.
             */
            $table->unsignedInteger('orden')
                ->default(0);

            $table->timestamps();

            $table->index('activo');
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paises');
    }
};
