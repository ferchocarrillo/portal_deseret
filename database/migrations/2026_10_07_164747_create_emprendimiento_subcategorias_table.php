<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprendimiento_subcategorias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')
                ->constrained('emprendimiento_categorias')
                ->cascadeOnDelete();

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);

            $table->timestamps();

            $table->index(['categoria_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprendimiento_subcategorias');
    }
};
