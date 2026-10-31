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
        Schema::create('divisiones_administrativas', function (Blueprint $table) {

            $table->id();

            /*
             * País al que pertenece directamente esta división.
             */
            $table->foreignId('pais_id')
                ->constrained('paises')
                ->cascadeOnDelete();

            /*
             * Permite construir una jerarquía ilimitada.
             *
             * Ejemplo Ecuador:
             *
             * Provincia
             *     └── Cantón
             *           └── Parroquia
             */
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('divisiones_administrativas')
                ->nullOnDelete();

            /*
             * Nombre visible.
             */
            $table->string('nombre', 150);

            /*
             * Código territorial oficial cuando exista.
             */
            $table->string('codigo', 50)
                ->nullable();

            /*
             * Tipo de división.
             *
             * Ejemplos:
             * departamento
             * provincia
             * estado
             * region
             * municipio
             * canton
             * distrito
             * comuna
             * parroquia
             */
            $table->string('tipo', 80);

            /*
             * Nivel dentro del árbol.
             *
             * 1 = división principal
             * 2 = segunda división
             * 3 = tercera división
             */
            $table->unsignedTinyInteger('nivel')
                ->default(1);

            /*
             * Texto que podrá utilizar Vue si necesita
             * presentar el tipo de división.
             */
            $table->string('label', 100);

            $table->boolean('activo')
                ->default(true);

            $table->unsignedInteger('orden')
                ->default(0);

            $table->timestamps();

            /*
             * Evita duplicar una misma división dentro
             * del mismo padre.
             */
            $table->unique(
                ['pais_id', 'parent_id', 'nombre'],
                'divisiones_pais_parent_nombre_unique'
            );

            $table->index([
                'pais_id',
                'parent_id',
                'activo',
            ]);

            $table->index([
                'pais_id',
                'nivel',
            ]);

            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisiones_administrativas');
    }
};
