<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_informaciones_recepcion', function (Blueprint $table) {

            $table->id();

            // ==========================================================
            // USUARIO
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->unique();


            // ==========================================================
            // UBICACIÓN
            // ==========================================================

            $table->string('pais', 100)
                ->default('Colombia');

            $table->string('departamento', 100)
                ->nullable();

            $table->string('ciudad', 100)
                ->nullable();

            $table->string('codigo_postal', 10)
                ->nullable();

            $table->string('barrio', 150)
                ->nullable();


            // ==========================================================
            // DIRECCIÓN
            // ==========================================================

            $table->string('tipo_via', 50)
                ->nullable();

            $table->string('numero_via', 100)
                ->nullable();

            $table->string('complemento', 150)
                ->nullable();

            $table->string('complemento_2', 150)
                ->nullable();

            $table->string('referencia_direccion', 255)
                ->nullable();


            // ==========================================================
            // CONTACTO
            // ==========================================================

            $table->string('telefono_contacto', 30)
                ->nullable();

            $table->string('whatsapp', 30)
                ->nullable();

            $table->string('correo_contacto')
                ->nullable();


            // ==========================================================
            // PREFERENCIA DE CONTACTO
            // ==========================================================

            $table->string('medio_contacto_preferido', 30)
                ->nullable();


            // ==========================================================
            // RECEPCIÓN
            // ==========================================================

            $table->text('instrucciones_recepcion')
                ->nullable();


            // ==========================================================
            // PRIVACIDAD Y PERMISOS
            // ==========================================================

            $table->boolean('permite_contacto')
                ->default(true);

            $table->boolean('permite_compartir_direccion')
                ->default(false);


            // ==========================================================
            // CONFIRMACIÓN DE DATOS
            // ==========================================================

            $table->timestamp('datos_confirmados_at')
                ->nullable();


            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_informaciones_recepcion');
    }
};

