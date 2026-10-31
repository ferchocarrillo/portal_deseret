<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clasificado_propiedades', function (Blueprint $table) {
            $table->boolean('tiene_internet')->default(false)->after('tiene_alcantarillado');
            $table->boolean('tiene_tv')->default(false)->after('tiene_internet');
        });
    }

    public function down(): void
    {
        Schema::table('clasificado_propiedades', function (Blueprint $table) {
            $table->dropColumn(['tiene_internet', 'tiene_tv']);
        });
    }
};
