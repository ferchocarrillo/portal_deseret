<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_errores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_ticket')->unique(); // Ej: BUG-2026-X8Y1Z
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('modulo'); // General, Marketplace, Servicios, Clasificados, Cursos, etc.
            $table->string('titulo');
            $table->string('severidad')->default('media'); // baja, media, alta, critica
            $table->text('descripcion');
            $table->text('pasos_reproducir')->nullable();
            $table->string('url_afectada')->nullable();
            $table->string('captura_path')->nullable();
            $table->string('estado')->default('abierto'); // abierto, en_revision, resuelto, descartado
            $table->text('notas_solucion')->nullable();
            $table->foreignId('resuelto_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resuelto_el')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_errores');
    }
};
