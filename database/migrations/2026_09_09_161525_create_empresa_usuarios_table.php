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
        Schema::create('empresa_usuarios', function (Blueprint $table) {

            $table->id();


            // ==========================================================
            // EMPRESA
            // ==========================================================

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->cascadeOnDelete();


            // ==========================================================
            // USUARIO / REPRESENTANTE
            // ==========================================================

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();


            // ==========================================================
            // INFORMACIÓN DEL VÍNCULO
            // ==========================================================

            $table->string('cargo');

            $table->string('correo_corporativo');

            $table->string('telefono_contacto', 30)
                ->nullable();


            // ==========================================================
            // VERIFICACIÓN
            // ==========================================================

            $table->boolean('verificado')
                ->default(false)
                ->index();

            $table->timestamp('verificado_at')
                ->nullable();


            // ==========================================================
            // REPRESENTACIÓN PRINCIPAL
            // ==========================================================

            $table->boolean('es_principal')
                ->default(false)
                ->index();


            // ==========================================================
            // FECHAS
            // ==========================================================

            $table->timestamps();


            // ==========================================================
            // REGLA:
            // UN USUARIO NO PUEDE REGISTRARSE DOS VECES
            // COMO REPRESENTANTE DE LA MISMA EMPRESA
            // ==========================================================

            $table->unique([
                'empresa_id',
                'user_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_usuarios');
    }
};

