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

    // Inicio de sesión con Google (Laravel Socialite). Las claves están en .env y en Vercel.
    // La dirección de vuelta es relativa: Socialite le añade el dominio con el que se abre la web
    // (http://localhost:8000 en local, https://prodaw2627mma.vercel.app en producción).
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => '/auth/google/callback',
    ],

    // Pasarela de pago (Stripe Checkout). En modo de prueba la clave empieza por sk_test_.
    // Sin clave, las reservas se confirman sin pago (útil para desarrollar y para los tests).
    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        // Secreto de firma del webhook (whsec_…): Stripe Dashboard → Desarrolladores → Webhooks
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

];
