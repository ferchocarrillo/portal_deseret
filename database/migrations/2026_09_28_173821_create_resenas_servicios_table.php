<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resenas_servicios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contacto_servicio_id')
                ->constrained('contactos_servicios')
                ->cascadeOnDelete();

            $table->foreignId('servicio_id')
                ->constrained('servicios')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('tipo', [
                'atencion',
                'servicio',
            ]);

            $table->unsignedTinyInteger('calificacion');

            $table->text('comentario')->nullable();

            $table->enum('estado', [
                'publicada',
                'pendiente_revision',
                'oculta',
            ])->default('publicada');

            $table->timestamps();

            $table->unique(
                ['contacto_servicio_id', 'tipo'],
                'resenas_contacto_tipo_unique'
            );

            $table->index(
                ['servicio_id', 'estado'],
                'resenas_servicio_estado_index'
            );

            $table->index(
                ['user_id', 'created_at'],
                'resenas_usuario_fecha_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resenas_servicios');
    }
};
