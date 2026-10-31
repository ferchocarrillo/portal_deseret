<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitudes_ingreso', function (Blueprint $table) {

            $table->id();

            // ==========================================================
            // RELACIÓN CON EL USUARIO
            // ==========================================================

            // Se asignará cuando la solicitud sea aprobada
            // y se cree/habilite la cuenta del usuario.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            // ==========================================================
            // DATOS PERSONALES
            // ==========================================================

            $table->string('nombres');
            $table->string('apellidos');

            $table->string('tipo_documento', 10);

            // Máximo 10 caracteres según la validación del formulario.
            $table->string('documento', 10);


            // ==========================================================
            // UBICACIÓN
            // ==========================================================

            // Código ISO del país:
            // CO, US, MX, etc.
            $table->string('pais', 2);

            // Departamento, estado, provincia, región o distrito.
            $table->string('region');

            // Ciudad o municipio.
            $table->string('ciudad');


            // ==========================================================
            // CONTACTO
            // ==========================================================

            // Número sin el indicativo internacional.
            $table->string('telefono', 15);

            $table->string('email');


            // ==========================================================
            // CONDICIÓN DEL SOLICITANTE
            // ==========================================================

            $table->boolean('es_miembro');


            // ==========================================================
            // INFORMACIÓN COMO MIEMBRO
            // ==========================================================

            $table->string('estaca')->nullable();

            $table->string('barrio')->nullable();

            $table->string('nombre_obispo')->nullable();


            // ==========================================================
            // INFORMACIÓN DE NO MIEMBRO
            // ==========================================================

            $table->text('motivo_ingreso')->nullable();


            // ==========================================================
            // AVAL
            // ==========================================================

            $table->boolean('tiene_aval')->nullable();

            $table->string('aval_nombres')->nullable();

            $table->string('aval_apellidos')->nullable();

            $table->string('aval_email')->nullable();

            $table->string('aval_telefono', 15)->nullable();

            $table->string('aval_barrio')->nullable();


            // ==========================================================
            // INFORMACIÓN ADICIONAL
            // ==========================================================

            // Máximo 250 caracteres.
            $table->string('observaciones', 250)->nullable();


            // ==========================================================
            // CONSENTIMIENTOS
            // ==========================================================

            $table->boolean('acepta_terminos')
                ->default(false);

            $table->boolean('ha_leido_privacidad')
                ->default(false);

            $table->boolean('autoriza_tratamiento_datos')
                ->default(false);


            // ==========================================================
            // ESTADO DE LA SOLICITUD
            // ==========================================================

            $table->string('estado')
                ->default('pendiente')
                ->index();


            // ==========================================================
            // REVISIÓN ADMINISTRATIVA
            // ==========================================================

            // Usuario administrador que realiza la revisión.
            $table->foreignId('revisado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('fecha_revision')
                ->nullable();

            $table->text('observacion_revision')
                ->nullable();


            // ==========================================================
            // MARCAS DE TIEMPO
            // ==========================================================

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_ingreso');
    }
};
