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
        Schema::create('clasificado_vehiculos', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Clasificado
            |--------------------------------------------------------------------------
            */

            $table->foreignId('clasificado_id')
                ->unique()
                ->constrained('clasificados')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Información del vehículo
            |--------------------------------------------------------------------------
            */

            $table->string('marca')
                ->nullable();

            $table->string('modelo')
                ->nullable();

            $table->unsignedSmallInteger('anio')
                ->nullable();

            $table->unsignedInteger('kilometraje')
                ->nullable();

            $table->unsignedInteger('cilindraje')
                ->nullable();

            $table->string('combustible')
                ->nullable();

            $table->string('transmision')
                ->nullable();

            $table->string('color')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Información adicional
            |--------------------------------------------------------------------------
            */

            $table->string('placa')
                ->nullable();

            $table->boolean('unico_propietario')
                ->nullable();

            $table->boolean('documentacion_al_dia')
                ->nullable();

            $table->text('observaciones')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('marca');
            $table->index('modelo');
            $table->index('anio');
            $table->index('kilometraje');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clasificado_vehiculos');
    }
};
