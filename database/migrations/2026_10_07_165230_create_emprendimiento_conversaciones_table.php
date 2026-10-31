<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprendimiento_conversaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('emprendimiento_id')
                ->constrained('emprendimientos')
                ->cascadeOnDelete();

            $table->foreignId('usuario_iniciador_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('usuario_receptor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('emprendimiento_id');
            $table->index('usuario_iniciador_id');
            $table->index('usuario_receptor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprendimiento_conversaciones');
    }
};
