<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsAppService
{
    /**
     * URL base de la API de WhatsApp.
     */
    protected string $baseUrl;

    /**
     * ID de la cuenta empresarial de WhatsApp.
     */
    protected ?string $phoneNumberId;

    /**
     * Token de acceso.
     */
    protected ?string $accessToken;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://graph.facebook.com/v23.0';

        $this->phoneNumberId = config('services.whatsapp.phone_number_id');

        $this->accessToken = config('services.whatsapp.access_token');
    }


    // ==========================================================
    // ENVIAR MENSAJE DE APROBACIÓN
    // ==========================================================

    public function enviarAprobacion(
        string $telefono,
        string $nombre,
        string $email,
        string $passwordTemporal
    ): void {

        $mensaje = "Hola {$nombre}.\n\n"
            . "Nos complace informarte que tu solicitud de ingreso a "
            . "Portal Deseret ha sido aprobada.\n\n"
            . "Ya puedes ingresar a Portal Deseret:\n"
            . config('app.url') . "\n\n"
            . "Correo: {$email}\n"
            . "Contraseña temporal: {$passwordTemporal}\n\n"
            . "Por seguridad, al ingresar deberás cambiar tu contraseña "
            . "y completar o actualizar tu perfil.\n\n"
            . "¡Bienvenido a Portal Deseret!";


        $this->enviarMensaje(
            $telefono,
            $mensaje
        );
    }


    // ==========================================================
    // ENVIAR MENSAJE DE RECHAZO
    // ==========================================================

    public function enviarRechazo(
        string $telefono,
        string $nombre,
        string $observacion
    ): void {

        $mensaje = "Hola {$nombre}.\n\n"
            . "Hemos revisado tu solicitud de ingreso a "
            . "Portal Deseret.\n\n"
            . "En esta ocasión no ha sido posible aprobar tu solicitud.\n\n"
            . "Motivo de la decisión:\n"
            . "{$observacion}\n\n"
            . "Si consideras que se trata de un error o deseas "
            . "actualizar la información suministrada, puedes realizar "
            . "una nueva solicitud.\n\n"
            . "Gracias por tu interés en Portal Deseret.";


        $this->enviarMensaje(
            $telefono,
            $mensaje
        );
    }


    // ==========================================================
    // MÉTODO GENERAL DE ENVÍO
    // ==========================================================

    protected function enviarMensaje(
        string $telefono,
        string $mensaje
    ): void {

        if (!$this->phoneNumberId || !$this->accessToken) {

            throw new RuntimeException(
                'La configuración de WhatsApp no está completa.'
            );
        }


        $telefono = $this->normalizarTelefono($telefono);


        $url = "{$this->baseUrl}/{$this->phoneNumberId}/messages";


        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->post($url, [

                'messaging_product' => 'whatsapp',

                'recipient_type' => 'individual',

                'to' => $telefono,

                'type' => 'text',

                'text' => [
                    'preview_url' => true,
                    'body' => $mensaje,
                ],

            ]);


        if ($response->failed()) {

            throw new RuntimeException(
                'No fue posible enviar el mensaje de WhatsApp. '
                    . $response->body()
            );
        }
    }


    // ==========================================================
    // NORMALIZAR TELÉFONO
    // ==========================================================

    protected function normalizarTelefono(string $telefono): string
    {
        // Eliminar espacios, guiones, paréntesis y otros caracteres.

        $telefono = preg_replace(
            '/[^0-9]/',
            '',
            $telefono
        );


        // Si el número colombiano viene como 3112819811,
        // agregamos el código de país +57.

        if (strlen($telefono) === 10) {

            $telefono = '57' . $telefono;
        }


        return $telefono;
    }
}
