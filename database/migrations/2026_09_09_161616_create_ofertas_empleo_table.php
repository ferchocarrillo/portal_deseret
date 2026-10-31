<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofertas_empleo', function (Blueprint $table) {
            // Identificación
            $table->id();

            $table->string('codigo_global', 20)->unique();

            // Publicador
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Empresa
            $table->foreignId('empresa_id')
                ->nullable()
                ->constrained('empresas')
                ->nullOnDelete();

            // Información general
            $table->string('titulo');
            $table->text('descripcion');

            // Ubicación
            $table->string('pais', 100)
                ->default('Colombia');

            $table->string('departamento', 100)
                ->nullable();

            $table->string('ciudad', 100)
                ->nullable();

            $table->string('direccion')
                ->nullable();

            // Modalidad
            $table->string('modalidad', 30)
                ->nullable();

            // Tipo de contrato
            $table->string('tipo_contrato', 50)
                ->nullable();

            // Nivel y experiencia requerida
            $table->string('nivel_experiencia', 50)
                ->nullable();

            $table->unsignedSmallInteger('experiencia_minima_meses')
                ->default(0);

            // Compensación
            $table->string('tipo_salario', 30)
                ->nullable();

            $table->decimal('salario_monto', 15, 2)
                ->nullable();

            $table->string('periodicidad_salario', 30)
                ->nullable();

            // Beneficios y condiciones
            $table->boolean('incluye_subsidio_transporte')
                ->default(false);

            $table->boolean('incluye_prestaciones_ley')
                ->default(false);

            $table->boolean('incluye_comisiones')
                ->default(false);

            $table->text('descripcion_comisiones')
                ->nullable();

            $table->boolean('incluye_bonificaciones')
                ->default(false);

            $table->text('descripcion_bonificaciones')
                ->nullable();

            $table->boolean('incluye_beneficios_adicionales')
                ->default(false);

            $table->text('descripcion_beneficios_adicionales')
                ->nullable();

            // Visibilidad de la compensación
            $table->boolean('mostrar_salario')
                ->default(true);

            // Estado
            $table->string('estado', 30)
                ->default('borrador')
                ->index();

            // Fechas de publicación
            $table->timestamp('fecha_publicacion')
                ->nullable();

            $table->timestamp('fecha_cierre')
                ->nullable();

            // Estadísticas
            $table->unsignedBigInteger('visualizaciones')
                ->default(0);

            // Fechas del registro
            $table->timestamps();

            // Índices
            $table->index([
                'estado',
                'fecha_publicacion',
            ]);

            $table->index([
                'empresa_id',
                'estado',
            ]);

            $table->index([
                'user_id',
                'estado',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofertas_empleo');
    }
};
