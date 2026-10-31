<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clasificado_conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clasificado_id')
                ->constrained('clasificados')
                ->cascadeOnDelete();
            $table->foreignId('comprador_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('vendedor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamp('ultimo_mensaje_at')->nullable();
            $table->timestamps();

            $table->unique([
                'clasificado_id',
                'comprador_id',
                'vendedor_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clasificado_conversaciones');
    }
};
