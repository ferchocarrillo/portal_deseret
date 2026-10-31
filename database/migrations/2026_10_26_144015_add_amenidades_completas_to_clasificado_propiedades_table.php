<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clasificado_propiedades', function (Blueprint $table) {
            // Zonas comunes y amenidades
            $table->boolean('tiene_gimnasio')->default(false)->after('tiene_parqueadero');
            $table->boolean('tiene_piscina')->default(false)->after('tiene_gimnasio');
            $table->boolean('tiene_bbq')->default(false)->after('tiene_piscina');
            $table->boolean('tiene_salon_comunal')->default(false)->after('tiene_bbq');
            $table->boolean('tiene_zona_infantil')->default(false)->after('tiene_salon_comunal');
            $table->boolean('tiene_zonas_verdes')->default(false)->after('tiene_zona_infantil');
            $table->boolean('tiene_cancha_multiple')->default(false)->after('tiene_zonas_verdes');
            $table->boolean('tiene_sauna_turco')->default(false)->after('tiene_cancha_multiple');
            $table->boolean('tiene_coworking')->default(false)->after('tiene_sauna_turco');

            // Seguridad y accesos
            $table->boolean('tiene_porteria_24_7')->default(false)->after('tiene_coworking');
            $table->boolean('tiene_circuito_cerrado')->default(false)->after('tiene_porteria_24_7');
            $table->boolean('tiene_parqueadero_visitantes')->default(false)->after('tiene_circuito_cerrado');

            // Comodidades interiores y políticas
            $table->boolean('permite_mascotas')->default(false)->after('tiene_parqueadero_visitantes');
            $table->boolean('tiene_balcon')->default(false)->after('permite_mascotas');
            $table->boolean('tiene_terraza')->default(false)->after('tiene_balcon');
            $table->boolean('tiene_deposito')->default(false)->after('tiene_terraza');
            $table->boolean('tiene_calentador')->default(false)->after('tiene_deposito');
            $table->boolean('tiene_aire_acondicionado')->default(false)->after('tiene_calentador');
            $table->boolean('cocina_integral')->default(false)->after('tiene_aire_acondicionado');
            $table->boolean('zona_lavanderia')->default(false)->after('cocina_integral');
        });
    }

    public function down(): void
    {
        Schema::table('clasificado_propiedades', function (Blueprint $table) {
            $table->dropColumn([
                'tiene_gimnasio',
                'tiene_piscina',
                'tiene_bbq',
                'tiene_salon_comunal',
                'tiene_zona_infantil',
                'tiene_zonas_verdes',
                'tiene_cancha_multiple',
                'tiene_sauna_turco',
                'tiene_coworking',
                'tiene_porteria_24_7',
                'tiene_circuito_cerrado',
                'tiene_parqueadero_visitantes',
                'permite_mascotas',
                'tiene_balcon',
                'tiene_terraza',
                'tiene_deposito',
                'tiene_calentador',
                'tiene_aire_acondicionado',
                'cocina_integral',
                'zona_lavanderia',
            ]);
        });
    }
};
