<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios_conversaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('servicio_id')
                ->constrained('servicios')
                ->cascadeOnDelete();

            $table->foreignId('usuario_interesado_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('propietario_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('ultimo_mensaje_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'servicio_id',
                'usuario_interesado_id',
                'propietario_id',
            ]);

            $table->index([
                'usuario_interesado_id',
                'ultimo_mensaje_at',
            ]);

            $table->index([
                'propietario_id',
                'ultimo_mensaje_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios_conversaciones');
    }
};
