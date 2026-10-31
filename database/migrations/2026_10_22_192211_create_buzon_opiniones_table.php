<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buzon_opiniones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo'); // sugerencia, queja, felicitacion
            $table->string('modulo')->default('General'); // General, Marketplace, Cursos, etc.
            $table->string('asunto');
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->foreignId('leido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('leido_el')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buzon_opiniones');
    }
};
