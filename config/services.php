<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'google_vision' => ['key' => env('GOOGLE_VISION_API_KEY')],

    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_FROM'),

        'templates' => [
            'reserva_aprobada_cliente'            => env('WA_TEMPLATE_RESERVA_APROBADA'),
            'reserva_rechazada_cliente'            => env('WA_TEMPLATE_RESERVA_RECHAZADA'),
            'reserva_modificada_cliente'           => env('WA_TEMPLATE_RESERVA_MODIFICADA'),
            'recordatorio_sesion_cliente'          => env('WA_TEMPLATE_RECORDATORIO_SESION'),
            'solicitud_estudio_aprobada_cliente'   => env('WA_TEMPLATE_SOLICITUD_ESTUDIO_APROBADA'),
            'solicitud_estudio_rechazada_cliente'  => env('WA_TEMPLATE_SOLICITUD_ESTUDIO_RECHAZADA'),
            'nueva_solicitud_estudio_socios'       => env('WA_TEMPLATE_NUEVA_SOLICITUD_ESTUDIO_SOCIOS'),
        ]
    ],

    'tesseract' => [
        'tessdata_dir' => env('TESSERACT_TESSDATA_PATH'),
    ],

];
