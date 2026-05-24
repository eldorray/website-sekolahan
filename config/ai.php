<?php

return [
    'default' => env('AI_PROVIDER', 'gemini'),

    'providers' => [
        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY'),
            'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
            'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com'),
        ],
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/openai'),
        ],
    ],
];
