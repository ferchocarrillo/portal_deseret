<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clasificado_denuncias', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Clasificado denunciado
            |--------------------------------------------------------------------------
            */

            $table->foreignId('clasificado_id')
                ->constrained('clasificados')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Usuario que realiza la denuncia
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Motivo de la denuncia
            |--------------------------------------------------------------------------
            */

            $table->string('motivo');

            $table->text('descripcion')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado de la denuncia
            |--------------------------------------------------------------------------
            */

            $table->enum('estado', [
                'pendiente',
                'en_revision',
                'resuelta',
                'rechazada',
            ])->default('pendiente');

            /*
            |--------------------------------------------------------------------------
            | Gestión administrativa
            |--------------------------------------------------------------------------
            */

            $table->text('respuesta_admin')->nullable();

            $table->foreignId('revisado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('revisada_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('clasificado_id');
            $table->index('user_id');
            $table->index('estado');
            $table->index([
                'clasificado_id',
                'estado',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clasificado_denuncias');
    }
};
