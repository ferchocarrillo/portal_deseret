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
        Schema::create('contactos_servicios', function (Blueprint $table) {
            $table->id();

            // Servicio al que se contactó
            $table->foreignId('servicio_id')
                ->constrained('servicios')
                ->cascadeOnDelete();

            // Usuario que inició el contacto
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Medio de contacto (por defecto 'chat' de la plataforma)
            $table->string('canal', 30)->default('chat');

            // Fecha y hora del registro de contacto
            $table->timestamps();

            // Índices para consultas de reseñas y estadísticas
            $table->index(
                ['servicio_id', 'created_at'],
                'contactos_servicio_fecha_index'
            );

            $table->index(
                ['user_id', 'created_at'],
                'contactos_usuario_fecha_index'
            );

            // Evita duplicar el registro de contacto entre el mismo usuario y servicio
            $table->unique(['servicio_id', 'user_id'], 'contacto_servicio_usuario_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos_servicios');
    }
};
