<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category'); // Ej: Emprendimientos, Marketplace, General, Donaciones
            $table->text('description')->nullable();
            $table->string('video_url'); // Enlace de Google Flow / YouTube o ruta del archivo
            $table->string('thumbnail')->nullable(); // Miniatura opcional
            $table->integer('order')->default(0); // Para ordenarlos en pantalla
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutorials');
    }
};
