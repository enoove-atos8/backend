<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Driver
    |--------------------------------------------------------------------------
    |
    | Supported drivers: "evolution", "meta", "zapi", "twilio"
    |
    | - evolution: Evolution API (self-hosted ou cloud)
    | - meta: WhatsApp Cloud API (oficial da Meta)
    | - zapi: Z-API (https://www.z-api.io/)
    | - twilio: Twilio WhatsApp API (https://www.twilio.com/whatsapp)
    |
    */

    'driver' => env('WHATSAPP_DRIVER', 'zapi'),

    /*
    |--------------------------------------------------------------------------
    | Evolution API Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para Evolution API
    |
    */

    'evolution' => [
        'base_url' => env('WHATSAPP_EVOLUTION_BASE_URL', 'http://localhost:8080'),
        'api_key' => env('WHATSAPP_EVOLUTION_API_KEY', ''),
        'instance_name' => env('WHATSAPP_EVOLUTION_INSTANCE_NAME', 'atos8'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta Cloud API Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para WhatsApp Cloud API da Meta
    |
    */

    'meta' => [
        'phone_number_id' => env('WHATSAPP_META_PHONE_NUMBER_ID', ''),
        'access_token' => env('WHATSAPP_META_ACCESS_TOKEN', ''),
        'api_version' => env('WHATSAPP_META_API_VERSION', 'v21.0'),
        'business_account_id' => env('WHATSAPP_META_BUSINESS_ACCOUNT_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Z-API Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para Z-API
    | Documentação: https://developer.z-api.io/
    |
    */

    'zapi' => [
        'instance_id' => env('WHATSAPP_ZAPI_INSTANCE_ID', ''),
        'token' => env('WHATSAPP_ZAPI_TOKEN', ''),
        'client_token' => env('WHATSAPP_ZAPI_CLIENT_TOKEN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para Twilio WhatsApp API
    | Documentação: https://www.twilio.com/docs/whatsapp/api
    |
    | Account SID: Identificador da sua conta Twilio (ex: ACxxxxxxxxxxxx)
    | Auth Token: Token de autenticação da sua conta
    | Phone Number: Número WhatsApp remetente no formato whatsapp:+5581999999999
    |               - Para Sandbox (testes): use o número fornecido pelo Twilio
    |               - Para Produção: use seu número registrado no WhatsApp Business
    |
    */

    'twilio' => [
        'account_sid' => env('WHATSAPP_TWILIO_ACCOUNT_SID', ''),
        'auth_token' => env('WHATSAPP_TWILIO_AUTH_TOKEN', ''),
        'phone_number' => env('WHATSAPP_TWILIO_PHONE_NUMBER', ''),
    ],

];
