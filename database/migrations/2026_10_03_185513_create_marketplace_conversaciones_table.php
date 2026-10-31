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
        Schema::create('marketplace_conversaciones', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Publicación relacionada
            |--------------------------------------------------------------------------
            */

            $table->foreignId('publicacion_id')
                ->constrained('marketplace_publicaciones')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Participantes
            |--------------------------------------------------------------------------
            |
            | comprador_id: usuario interesado en la publicación.
            | vendedor_id: propietario de la publicación.
            |
            */

            $table->foreignId('comprador_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('vendedor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Última actividad
            |--------------------------------------------------------------------------
            |
            | Permite ordenar las conversaciones por la actividad más reciente
            | sin tener que consultar constantemente el último mensaje.
            |
            */

            $table->timestamp('ultimo_mensaje_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Evitar conversaciones duplicadas
            |--------------------------------------------------------------------------
            |
            | Un comprador tendrá una sola conversación con un vendedor
            | respecto de una publicación.
            |
            */

            $table->unique([
                'publicacion_id',
                'comprador_id',
                'vendedor_id'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('publicacion_id');
            $table->index('comprador_id');
            $table->index('vendedor_id');
            $table->index('ultimo_mensaje_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_conversaciones');
    }
};
