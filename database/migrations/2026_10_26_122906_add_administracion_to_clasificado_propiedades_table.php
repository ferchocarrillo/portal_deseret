<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clasificado_propiedades', function (Blueprint $table) {
            $table->boolean('incluye_administracion')->default(false)->after('propiedad_observaciones');
            $table->decimal('precio_administracion', 12, 2)->nullable()->after('incluye_administracion');
        });
    }

    public function down(): void
    {
        Schema::table('clasificado_propiedades', function (Blueprint $table) {
            $table->dropColumn(['incluye_administracion', 'precio_administracion']);
        });
    }
};
