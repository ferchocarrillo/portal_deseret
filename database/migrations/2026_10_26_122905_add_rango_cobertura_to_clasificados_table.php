<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clasificados', function (Blueprint $table) {
            $table->string('rango_cobertura', 30)->default('departamental')->after('ciudad_id');
        });
    }

    public function down(): void
    {
        Schema::table('clasificados', function (Blueprint $table) {
            $table->dropColumn('rango_cobertura');
        });
    }
};
