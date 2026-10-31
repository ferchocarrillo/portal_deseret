<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            // Identificación
            $table->id();
            $table->string('codigo_global', 20)->unique();

            // Propietario
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Información del servicio
            $table->string('titulo', 150);
            $table->text('descripcion');
            $table->string('categoria', 100);

            $table->enum('modalidad', [
                'presencial',
                'remoto',
                'hibrido',
            ]);

            // Precio
            $table->enum('precio_tipo', [
                'hora',
                'dia',
                'servicio',
                'proyecto',
                'fijo',
                'a_convenir',
            ]);

            $table->decimal('precio', 12, 2)
                ->nullable();

            $table->char('moneda', 3)
                ->default('COP');

            // Imagen
            $table->enum('imagen_tipo', [
                'perfil',
                'archivo',
            ])->default('perfil');

            $table->string('imagen')
                ->nullable();

            // Ubicación
            $table->string('pais', 100)
                ->nullable();

            $table->string('departamento', 100)
                ->nullable();

            $table->string('ciudad', 100)
                ->nullable();

            // Medios de contacto
            $table->boolean('usar_whatsapp')
                ->default(false);

            $table->boolean('usar_email')
                ->default(false);

            $table->boolean('usar_telefono')
                ->default(false);

            // Datos de contacto
            $table->string('contacto_whatsapp', 30)
                ->nullable();

            $table->string('contacto_email')
                ->nullable();

            $table->string('contacto_telefono', 30)
                ->nullable();

            // Estado de la publicación
            $table->enum('estado', [
                'activo',
                'pausado',
            ])->default('activo');

            // Marcas de tiempo
            $table->timestamps();

            // Índices
            $table->index(['estado', 'created_at']);
            $table->index(['categoria', 'estado']);
            $table->index(['modalidad', 'estado']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
