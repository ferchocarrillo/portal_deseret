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
        Schema::create('user_infractions', function (Blueprint $table) {
            $table->id();

            // Usuario sancionado
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Moderador que aplicó la sanción (nullable si lo genera un proceso automático)
            $table->foreignId('moderator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Identificador numérico de la regla infringida (mapeado a config/community_rules.php)
            $table->unsignedTinyInteger('rule_id');

            // Detalle o fragmento del contenido reportado
            $table->text('detail');

            // Nivel acumulado de advertencia (1 = primera advertencia, 2 = suspensión temporal, etc.)
            $table->unsignedTinyInteger('strike_level')->default(1);

            // Marca de tiempo cuando el usuario leyó y aceptó el aviso en la plataforma
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // Índices para optimizar búsquedas frecuentes
            $table->index(['user_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infractions');
    }
};
