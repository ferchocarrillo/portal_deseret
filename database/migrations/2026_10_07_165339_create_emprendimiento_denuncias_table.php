<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprendimiento_denuncias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('emprendimiento_id')
                ->constrained('emprendimientos')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('motivo');

            $table->text('descripcion')->nullable();

            $table->string('estado')->default('pendiente');

            $table->timestamps();

            $table->index(['emprendimiento_id', 'estado']);
            $table->index(['user_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprendimiento_denuncias');
    }
};
