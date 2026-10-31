<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Referencia única para Wompi o control manual
            $table->string('gateway')->default('wompi'); // 'wompi', 'nequi', 'daviplata', 'transferencia'
            $table->decimal('amount', 12, 2); // Monto en COP
            $table->string('status')->default('pending'); // pending, approved, declined
            $table->string('donor_name')->nullable(); // Nombre o Anónimo
            $table->string('donor_email')->nullable(); // Para contacto o agradecimiento
            $table->text('message')->nullable(); // Mensaje de apoyo
            $table->json('gateway_response')->nullable(); // Respuesta completa de Wompi (opcional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};