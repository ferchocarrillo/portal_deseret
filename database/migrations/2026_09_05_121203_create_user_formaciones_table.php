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
        Schema::create('user_formaciones', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('nivel')->nullable();

            $table->string('institucion')->nullable();

            $table->string('programa');

            $table->text('descripcion')->nullable();

            $table->date('fecha_inicio')->nullable();

            $table->date('fecha_fin')->nullable();

            $table->boolean('en_curso')
                ->default(false);

            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_formaciones');
    }
};
