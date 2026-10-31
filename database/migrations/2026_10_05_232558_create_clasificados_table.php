<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clasificados', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | Identificación
            |--------------------------------------------------------------------------
            */

            $table->id();

            $table->string('codigo_global', 20)
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Usuario
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Categoría
            |--------------------------------------------------------------------------
            */

            $table->foreignId('categoria_id')
                ->constrained('clasificado_categorias')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Intención
            |--------------------------------------------------------------------------
            */

            $table->enum('intencion', [
                'ofrecer',
                'buscar',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Operación
            |--------------------------------------------------------------------------
            */

            $table->enum('operacion', [
                'venta',
                'arriendo',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Información general
            |--------------------------------------------------------------------------
            */

            $table->string('titulo');

            $table->string('slug')
                ->unique();

            $table->text('descripcion');

            /*
            |--------------------------------------------------------------------------
            | Precio / presupuesto
            |--------------------------------------------------------------------------
            */

            $table->decimal('precio', 15, 2)
                ->nullable();

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
            | Estado
            |--------------------------------------------------------------------------
            */

            $table->enum('estado', [
                'publicado',
                'pausado',
                'vendido',
                'arrendado',
                'cerrado',
                'suspendido',
            ])->default('publicado');

            /*
            |--------------------------------------------------------------------------
            | Fechas
            |--------------------------------------------------------------------------
            */

            $table->timestamp('fecha_publicacion')
                ->nullable();

            $table->timestamp('fecha_cierre')
                ->nullable();

            $table->timestamp('fecha_suspension')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Moderación
            |--------------------------------------------------------------------------
            */

            $table->text('motivo_suspension')
                ->nullable();

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
            $table->index('intencion');
            $table->index('operacion');
            $table->index('estado');

            $table->index([
                'categoria_id',
                'intencion',
                'operacion',
                'estado',
            ]);

            $table->index([
                'pais_id',
                'division_administrativa_id',
                'ciudad_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clasificados');
    }
};
