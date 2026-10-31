<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->boolean('mostrar_direccion')->default(false)->after('ciudad');
            $table->decimal('latitud', 10, 8)->nullable()->after('mostrar_direccion');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
            $table->decimal('radio_alcance_km', 5, 2)->nullable()->after('longitud');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn([
                'mostrar_direccion',
                'latitud',
                'longitud',
                'radio_alcance_km'
            ]);
        });
    }
};
