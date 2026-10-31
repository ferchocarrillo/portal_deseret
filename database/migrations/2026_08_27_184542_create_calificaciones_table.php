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
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();

            // ==========================================================
            // USUARIO QUE REALIZA LA CALIFICACIÓN
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ==========================================================
            // ELEMENTO CALIFICADO
            // ==========================================================
            //
            // Puede ser:
            //
            // Publicacion
            // User
            //
            // Ejemplo:
            //
            // calificable_type = App\Models\Publicacion
            // calificable_id   = 25
            //
            // o:
            //
            // calificable_type = App\Models\User
            // calificable_id   = 8
            //

            $table->string('calificable_type');

            $table->unsignedBigInteger('calificable_id');

            // ==========================================================
            // CALIFICACIÓN
            // ==========================================================

            // Valor permitido: 1 a 5 estrellas.
            $table->unsignedTinyInteger('calificacion');

            // ==========================================================
            // COMENTARIO OPCIONAL
            // ==========================================================

            $table->text('comentario')->nullable();

            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();

            // ==========================================================
            // RESTRICCIÓN
            // ==========================================================
            //
            // Un usuario solamente puede calificar una vez
            // al mismo elemento.
            //

            $table->unique([
                'user_id',
                'calificable_type',
                'calificable_id',
            ]);

            // ==========================================================
            // ÍNDICE
            // ==========================================================

            $table->index([
                'calificable_type',
                'calificable_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
