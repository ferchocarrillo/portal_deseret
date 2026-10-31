<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('skill_subcategory_id')
                ->constrained('skill_subcategories')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('slug');

            $table->text('description')->nullable();

            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->boolean('is_featured')
                ->default(false)
                ->index();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->unique([
                'skill_subcategory_id',
                'name'
            ]);

            $table->unique([
                'skill_subcategory_id',
                'slug'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
