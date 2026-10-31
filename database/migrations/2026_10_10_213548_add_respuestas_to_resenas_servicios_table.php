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
        Schema::table('resenas_servicios', function (Blueprint $table) {
            $table->text('respuesta_propietario')->nullable()->after('comentario');
            $table->timestamp('respuesta_at')->nullable()->after('respuesta_propietario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resenas_servicios', function (Blueprint $table) {
            $table->dropColumn(['respuesta_propietario', 'respuesta_at']);
        });
    }
};
