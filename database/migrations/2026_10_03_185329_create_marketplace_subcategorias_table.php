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
        Schema::create('marketplace_subcategorias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')
                ->constrained('marketplace_categorias')
                ->cascadeOnDelete();

            $table->string('nombre');
            $table->string('slug');

            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);

            $table->timestamps();

            $table->unique(['categoria_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_subcategorias');
    }
};
