<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clasificados', function (Blueprint $table) {
            // Radio de incidencia en kilómetros (0 = nacional o sin límite)
            $table->unsignedSmallInteger('radio_alcance_km')->default(50)->after('ciudad_id');
        });
    }

    public function down(): void
    {
        Schema::table('clasificados', function (Blueprint $table) {
            $table->dropColumn('radio_alcance_km');
        });
    }
};