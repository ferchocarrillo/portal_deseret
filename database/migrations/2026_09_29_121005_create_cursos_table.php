<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_global', 20)->unique();

            // Responsable del curso
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Información general
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->string('imagen');

            // Origen del curso
            $table->enum('tipo', [
                'propio',
                'externo',
            ])->default('propio');

            $table->string('url_externa')
                ->nullable();

            // Precio
            $table->boolean('es_gratuito')
                ->default(true);

            $table->decimal('precio', 12, 2)
                ->nullable();

            // Modalidad
            $table->enum('modalidad', [
                'virtual',
                'presencial',
                'hibrido',
            ]);

            $table->string('pais')
                ->nullable();

            $table->string('departamento')
                ->nullable();

            $table->string('ciudad')
                ->nullable();

            $table->string('direccion')
                ->nullable();

            $table->string('enlace_virtual')
                ->nullable();

            // Intensidad horaria y frecuencia
            $table->unsignedTinyInteger('frecuencia_clases')
                ->nullable();

            $table->unsignedSmallInteger('duracion_sesion_minutos')
                ->nullable();

            $table->unsignedSmallInteger('intensidad_horaria')
                ->nullable();

            // Fechas del curso
            $table->date('fecha_inicio')
                ->nullable();

            $table->date('fecha_fin')
                ->nullable();

            $table->dateTime('fecha_limite_inscripcion')
                ->nullable();

            // Vigencia de la publicación
            $table->dateTime('publicado_desde')
                ->nullable();

            $table->dateTime('publicado_hasta')
                ->nullable();

            // Cupos e inscripciones
            $table->boolean('cupos_ilimitados')
                ->default(false);

            $table->unsignedInteger('cantidad_cupos')
                ->nullable();

            $table->boolean('permite_lista_espera')
                ->default(false);

            // Estado
            $table->enum('estado', [
                'borrador',
                'publicado',
                'pausado',
                'cerrado',
                'cancelado',
                'finalizado',
            ])->default('borrador');

            $table->timestamps();

            // Índices
            $table->index(['estado', 'publicado_desde']);
            $table->index(['estado', 'publicado_hasta']);
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index(['user_id', 'estado']);
            $table->index(['pais', 'departamento', 'ciudad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
