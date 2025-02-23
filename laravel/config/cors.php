<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'laravel-filemanager/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Địa chỉ của Vue
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Cho phép gửi cookie (quan trọng nếu bạn dùng authentication)
];

