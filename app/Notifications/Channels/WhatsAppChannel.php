<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        // Si el modelo no tiene teléfono, no intentes enviar
        if (empty($notifiable->telefono)) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        $client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $client->messages->create(
            'whatsapp:' . $notifiable->telefono,
            [
                'from' => config('services.twilio.from'),
                'body' => $message,
            ]
        );
    }
}
