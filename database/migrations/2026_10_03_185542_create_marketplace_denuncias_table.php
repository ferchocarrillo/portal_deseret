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
        Schema::create('marketplace_denuncias', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Publicación denunciada
            |--------------------------------------------------------------------------
            */

            $table->foreignId('publicacion_id')
                ->constrained('marketplace_publicaciones')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Usuario que realiza la denuncia
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Motivo de la denuncia
            |--------------------------------------------------------------------------
            |
            | producto_prohibido
            | posible_estafa
            | informacion_enganosa
            | contenido_inapropiado
            | publicacion_duplicada
            | categoria_incorrecta
            | otro
            |
            */

            $table->string('motivo');

            /*
            |--------------------------------------------------------------------------
            | Detalle proporcionado por el usuario
            |--------------------------------------------------------------------------
            */

            $table->text('descripcion')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado de la denuncia
            |--------------------------------------------------------------------------
            |
            | pendiente
            | revisada
            | descartada
            | accion_tomada
            |
            */

            $table->string('estado')
                ->default('pendiente');

            /*
            |--------------------------------------------------------------------------
            | Resolución administrativa
            |--------------------------------------------------------------------------
            */

            $table->text('resolucion')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Administrador que resolvió la denuncia
            |--------------------------------------------------------------------------
            */

            $table->foreignId('resuelto_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('resuelto_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('publicacion_id');
            $table->index('user_id');
            $table->index('estado');
            $table->index('motivo');
            $table->index('resuelto_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_denuncias');
    }
};
