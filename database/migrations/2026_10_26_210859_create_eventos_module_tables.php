<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla principal de Eventos
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Ubicación geográfica integrada con el sistema
            $table->foreignId('ciudad_id')->nullable()->constrained('ciudades')->nullOnDelete();
            $table->foreignId('division_administrativa_id')->nullable()->constrained('divisiones_administrativas')->nullOnDelete();

            $table->string('titulo');
            $table->text('descripcion');

            // Modalidad y locación
            $table->enum('modalidad', ['presencial', 'virtual', 'hibrido'])->default('presencial');
            $table->string('ubicacion')->nullable(); // Dirección física
            $table->string('enlace_virtual')->nullable(); // Enlace de transmisión

            // Fechas y Horarios
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();

            // Costos y Precios
            $table->boolean('es_gratuito')->default(false);
            $table->decimal('precio', 12, 2)->nullable();
            $table->decimal('precio_preventa', 12, 2)->nullable();
            $table->dateTime('fecha_limite_preventa')->nullable();

            // Precios por ubicación / localidades (Estructura JSON)
            $table->json('precios_localidades')->nullable();

            $table->integer('capacidad')->nullable();

            $table->string('imagen')->nullable();
            $table->enum('estado', ['activo', 'finalizado', 'suspendido'])->default('activo');
            $table->boolean('es_destacado')->default(false);

            $table->timestamps();
        });

        // 2. Tabla de Conversaciones del módulo de eventos
        Schema::create('evento_conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('comprador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organizador_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['evento_id', 'comprador_id', 'organizador_id'], 'evento_conversacion_unique');
        });

        // 3. Tabla de Mensajes internos del módulo de eventos
        Schema::create('evento_mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_conversacion_id')->constrained('evento_conversaciones')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('cuerpo');
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_mensajes');
        Schema::dropIfExists('evento_conversaciones');
        Schema::dropIfExists('eventos');
    }
};
