<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_publicaciones', function (Blueprint $table) {
            $table->string('rango_cobertura', 30)
                ->default('local')
                ->after('tipo_entrega')
                ->comment('Alcance de entrega/despacho: local, departamental, nacional, internacional');

            $table->index('rango_cobertura');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_publicaciones', function (Blueprint $table) {
            $table->dropIndex(['rango_cobertura']);
            $table->dropColumn('rango_cobertura');
        });
    }
};
