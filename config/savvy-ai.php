<?php

return [
    'snippets' => [
        'namespace' => '\\App\\Snippets',
    ],

    'drivers' => [
        'ai'     => env('AI_DRIVER', 'openai'),
        'sms'    => env('SMS_DRIVER', 'twilio'),
        'vector' => env('VECTOR_DRIVER', 'pinecone'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'org' => env('OPENAI_ORGANIZATION'),
    ],

    'cohere' => [
        'key' => env('COHERE_API_KEY'),
        'url' => env('COHERE_API_URL'),
    ],

    'pinecone' => [
        'key' => env('PINECONE_API_KEY'),
        'url' => env('PINECONE_INDEX_URL'),
    ],

    'weaviate' => [
        'key' => env('WEAVIATE_API_KEY'),
        'url' => env('WEAVIATE_INDEX_URL'),
    ],

    'twilio' => [
        'sid'      => env('TWILIO_ACCOUNT_SID'),
        'token'    => env('TWILIO_AUTH_TOKEN'),
        'phone'    => env('TWILIO_PHONE_NUMBER'),
        'whatsapp' => env('TWILIO_WHATSAPP_NUMBER'),
    ],
];
