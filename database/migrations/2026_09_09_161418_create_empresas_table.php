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
        Schema::create('empresas', function (Blueprint $table) {

            $table->id();


            // ==========================================================
            // IDENTIFICACIÓN
            // ==========================================================

            $table->string('razon_social');

            $table->string('nombre_comercial')
                ->nullable();

            $table->string('nit', 30)
                ->nullable()
                ->unique();


            // ==========================================================
            // CONTACTO CORPORATIVO
            // ==========================================================

            $table->string('correo_corporativo');

            $table->string('telefono', 30)
                ->nullable();


            // ==========================================================
            // UBICACIÓN
            // ==========================================================

            $table->string('pais', 100)
                ->default('Colombia');

            $table->string('departamento', 100)
                ->nullable();

            $table->string('ciudad', 100)
                ->nullable();

            $table->string('codigo_postal', 20)
                ->nullable();

            $table->string('direccion')
                ->nullable();


            // ==========================================================
            // INFORMACIÓN ADICIONAL
            // ==========================================================

            $table->string('sitio_web')
                ->nullable();

            $table->text('descripcion')
                ->nullable();


            // ==========================================================
            // ESTADO
            // ==========================================================

            $table->boolean('activa')
                ->default(true)
                ->index();

            $table->boolean('verificada')
                ->default(false)
                ->index();

            $table->timestamp('verificada_at')
                ->nullable();


            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};

