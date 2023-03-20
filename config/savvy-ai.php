<?php

return [
    'openai'=> [
        'key' => env('OPENAI_API_KEY'),
        'org' => env('OPENAI_ORGANIZATION'),
    ],

    'pinecone' => [
        'key' => env('PINECONE_API_KEY'),
        'url' => env('PINECONE_INDEX_URL'),
    ],

    'yelp' => [
        'key' => env('YELP_API_KEY'),
        'url' => env('YELP_API_URL'),
    ],

    'weather' => [
        'key' => env('WEATHER_API_KEY'),
        'url' => env('WEATHER_API_URL'),
    ],

    'twilio' => [
        'sid'      => env('TWILIO_ACCOUNT_SID'),
        'token'    => env('TWILIO_AUTH_TOKEN'),
        'phone'    => env('TWILIO_PHONE_NUMBER'),
        'whatsapp' => env('TWILIO_WHATSAPP_NUMBER'),
    ],
];
