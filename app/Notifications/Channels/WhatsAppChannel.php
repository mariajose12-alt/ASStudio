<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class WhatsAppChannel
{
    // Códigos de error de Twilio que indican un problema permanente
    // (número mal formado, no habilitado para WhatsApp, etc.) — reintentar
    // no sirve de nada en estos casos.
    private const CODIGOS_NO_REINTENTABLES = [
        21211, // 'To' number no es válido
        21614, // 'To' number no puede recibir mensajes SMS/WhatsApp
        63007, // No se encontró un canal de WhatsApp para ese número
        63016, // Número no está en la lista de sandbox (cuenta de prueba)
    ];

    public function send($notifiable, Notification $notification)
    {
        \Log::info('WhatsAppChannel::send llamado', [
            'notification' => get_class($notification),
            'notifiable_id' => $notifiable->id ?? null,
            'telefono' => $notifiable->telefono ?? null,
            'acepta_whatsapp' => $notifiable->acepta_whatsapp ?? null,
        ]);

        if (!method_exists($notification, 'toWhatsApp')) {
            \Log::info('WhatsAppChannel: sin método toWhatsApp');
            return;
        }

        if (empty($notifiable->telefono)) {
            \Log::info('WhatsAppChannel: telefono vacío');
            return;
        }

        if (!$notifiable->acepta_whatsapp) {
            \Log::info('WhatsAppChannel: acepta_whatsapp false');
            return;
        }

        $payload = $notification->toWhatsApp($notifiable);

        if (empty($payload['content_sid'])) {
            Log::info('WhatsApp omitido: falta ContentSid configurado', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        \Log::info('WhatsAppChannel: enviando a Twilio', ['content_sid' => $payload['content_sid']]);

        $client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        try {
            $client->messages->create(
                'whatsapp:' . $notifiable->telefono,
                [
                    'from'             => config('services.twilio.from'),
                    'contentSid'       => $payload['content_sid'],
                    'contentVariables' => json_encode($payload['variables'] ?? []),
                ]
            );
        } catch (TwilioException $e) {
            $esPermanente = in_array($e->getCode(), self::CODIGOS_NO_REINTENTABLES, true);

            Log::warning('Fallo al enviar notificación por WhatsApp', [
                'notifiable_id'   => $notifiable->id ?? null,
                'notification'    => get_class($notification),
                'twilio_code'     => $e->getCode(),
                'twilio_message'  => $e->getMessage(),
                'reintentable'    => !$esPermanente,
                // No se loguea el cuerpo del mensaje: puede contener datos sensibles.
            ]);

            // Error permanente: no reintentar, se descarta silenciosamente
            // (ya quedó registrado en logs para seguimiento manual).
            if ($esPermanente) {
                return;
            }

            // Error temporal (timeout, rate limit, etc.): relanzar para que
            // el job encolado reintente según su $tries/$backoff.
            throw $e;
        }
    }
}
