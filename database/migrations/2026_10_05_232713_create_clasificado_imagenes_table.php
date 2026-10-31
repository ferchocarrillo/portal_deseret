<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clasificado_imagenes', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relación con el clasificado
            |--------------------------------------------------------------------------
            */

            $table->foreignId('clasificado_id')
                ->constrained('clasificados')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Archivo
            |--------------------------------------------------------------------------
            */

            $table->string('ruta');

            /*
            |--------------------------------------------------------------------------
            | Orden e imagen principal
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('orden')->default(0);

            $table->boolean('es_principal')->default(false);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('clasificado_id');

            $table->index([
                'clasificado_id',
                'orden',
            ]);

            $table->index([
                'clasificado_id',
                'es_principal',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clasificado_imagenes');
    }
};
