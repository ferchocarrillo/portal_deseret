<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_publicaciones', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identificador global
            |--------------------------------------------------------------------------
            */

            $table->string('codigo_global', 20)->unique();

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('categoria_id')
                ->constrained('marketplace_categorias')
                ->restrictOnDelete();

            $table->foreignId('subcategoria_id')
                ->nullable()
                ->constrained('marketplace_subcategorias')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Información de la publicación
            |--------------------------------------------------------------------------
            */

            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion');

            /*
            |--------------------------------------------------------------------------
            | Precio
            |--------------------------------------------------------------------------
            */

            $table->decimal('precio', 15, 2)->nullable();
            $table->boolean('es_gratis')->default(false);

            /*
            |--------------------------------------------------------------------------
            | Estado del producto
            |--------------------------------------------------------------------------
            */

            $table->string('estado_producto');

            /*
            |--------------------------------------------------------------------------
            | Estado de la publicación
            |--------------------------------------------------------------------------
            */

            $table->string('estado_publicacion')
                ->default('publicada');

            /*
            |--------------------------------------------------------------------------
            | Ubicación
            |--------------------------------------------------------------------------
            */

            $table->foreignId('pais_id')
                ->nullable()
                ->constrained('paises')
                ->nullOnDelete();

            $table->foreignId('division_administrativa_id')
                ->nullable()
                ->constrained('divisiones_administrativas')
                ->nullOnDelete();

            $table->foreignId('ciudad_id')
                ->nullable()
                ->constrained('ciudades')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Entrega
            |--------------------------------------------------------------------------
            */

            $table->string('tipo_entrega')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Fechas de control
            |--------------------------------------------------------------------------
            */

            $table->timestamp('fecha_publicacion')->nullable();
            $table->timestamp('fecha_reserva')->nullable();
            $table->timestamp('fecha_venta')->nullable();
            $table->timestamp('fecha_pausa')->nullable();
            $table->timestamp('fecha_suspension')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Moderación
            |--------------------------------------------------------------------------
            */

            $table->text('motivo_suspension')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('user_id');
            $table->index('categoria_id');
            $table->index('subcategoria_id');
            $table->index('pais_id');
            $table->index('division_administrativa_id');
            $table->index('ciudad_id');
            $table->index('estado_publicacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_publicaciones');
    }
};
