<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Driver
    |--------------------------------------------------------------------------
    |
    | Supported drivers: "evolution", "meta", "zapi"
    |
    | - evolution: Evolution API (self-hosted ou cloud)
    | - meta: WhatsApp Cloud API (oficial da Meta)
    | - zapi: Z-API (https://www.z-api.io/)
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

];
