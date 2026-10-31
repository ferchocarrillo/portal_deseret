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
        Schema::create('marketplace_calificaciones', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            $table->foreignId('publicacion_id')
                ->constrained('marketplace_publicaciones')
                ->cascadeOnDelete();

            $table->foreignId('conversacion_id')
                ->constrained('marketplace_conversaciones')
                ->cascadeOnDelete();

            $table->foreignId('comprador_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('vendedor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Calificación
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('calificacion');

            $table->text('comentario')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            |
            | publicada
            | oculta
            |
            */

            $table->string('estado')
                ->default('publicada');

            /*
            |--------------------------------------------------------------------------
            | Respuesta del vendedor
            |--------------------------------------------------------------------------
            */

            $table->text('respuesta_vendedor')->nullable();

            $table->timestamp('respuesta_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Evitar calificaciones duplicadas
            |--------------------------------------------------------------------------
            |
            | Una conversación solamente puede generar una calificación.
            |
            */

            $table->unique('conversacion_id');

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index(['publicacion_id', 'estado']);
            $table->index(['vendedor_id', 'estado']);
            $table->index(['comprador_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_calificaciones');
    }
};
