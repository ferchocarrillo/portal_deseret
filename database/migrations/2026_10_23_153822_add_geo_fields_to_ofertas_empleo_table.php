<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ofertas_empleo', function (Blueprint $table) {
            // Se agrega después del campo direccion
            $table->boolean('mostrar_direccion')->default(false)->after('direccion');

            // Campos geoespaciales agregados después de modalidad
            $table->decimal('latitud', 10, 8)->nullable()->after('modalidad');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
            $table->decimal('radio_alcance_km', 5, 2)->nullable()->after('longitud');
        });
    }

    public function down(): void
    {
        Schema::table('ofertas_empleo', function (Blueprint $table) {
            $table->dropColumn([
                'mostrar_direccion',
                'latitud',
                'longitud',
                'radio_alcance_km'
            ]);
        });
    }
};
