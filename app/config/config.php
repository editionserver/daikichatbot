<?php
return [
    'app' => [
        'name' => 'Daikin Chatbot',
        'env' => 'development',
        'url' => 'http://localhost',
        'timezone' => 'Europe/Istanbul',
        'locale' => 'tr',
    ],

    'database' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', 'localhost'),
        'database' => env('DB_DATABASE', 'daikin_chatbot'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => 'gpt-4',
        'max_tokens' => 2000,
        'temperature' => 0.7,
    ],

    'chat' => [
        'guest_limit' => 2,
        'default_plan_limit' => 50,
        'pro_plan_limit' => -1, // Sınırsız
        'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
        'max_file_size' => 5 * 1024 * 1024, // 5MB
    ],

    'session' => [
        'lifetime' => 120,
        'secure' => true,
        'http_only' => true,
    ],
];