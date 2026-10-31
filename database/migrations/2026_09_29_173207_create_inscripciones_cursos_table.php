<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones_cursos', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            $table->foreignId('curso_id')
                ->constrained('cursos')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Datos del participante
            |--------------------------------------------------------------------------
            |
            | Se almacenan como una copia de los datos del perfil en el momento
            | en que se realiza la inscripción.
            |
            */

            $table->string('nombre_inscrito');
            $table->string('email_inscrito');
            $table->string('telefono_inscrito');

            /*
            |--------------------------------------------------------------------------
            | Estado y seguimiento
            |--------------------------------------------------------------------------
            */

            $table->string('estado', 30)
                ->default('pendiente');

            // Se completa cuando el creador abre/revisa esta inscripción.
            $table->timestamp('visto_at')
                ->nullable();

            // Se completa cuando el creador activa la inscripción.
            $table->timestamp('activado_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Cancelación
            |--------------------------------------------------------------------------
            |
            | Estos campos permiten conservar el historial de una cancelación
            | realizada por el participante.
            |
            | Ejemplos de motivo_cancelacion:
            |
            | - ya_no_puedo_asistir
            | - horario_no_disponible
            | - modalidad_no_conveniente
            | - inscripcion_por_error
            | - otra_alternativa
            | - motivos_personales
            | - motivos_laborales
            | - motivos_economicos
            | - otro
            |
            */

            $table->string('motivo_cancelacion', 50)
                ->nullable();

            $table->text('observacion_cancelacion')
                ->nullable();

            $table->timestamp('cancelada_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Fechas
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricciones
            |--------------------------------------------------------------------------
            |
            | Un usuario solo puede tener una inscripción por curso.
            |
            | Si la inscripción se cancela, la misma fila podrá reutilizarse
            | posteriormente para realizar una nueva solicitud.
            |
            */

            $table->unique(
                ['curso_id', 'user_id'],
                'inscripciones_cursos_curso_usuario_unique'
            );

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index(['curso_id', 'estado']);

            $table->index(['curso_id', 'visto_at']);

            $table->index(['curso_id', 'motivo_cancelacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones_cursos');
    }
};
