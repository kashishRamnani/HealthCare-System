<?php

use App\Helpers\Env;

return [
    'default' => Env::get('DB_CONNECTION', 'mysql'),

    'Connection' => [
        'mysql' => [
            'host'     => Env::get('DB_HOST', '127.0.0.1'),
            'database' => Env::get('DB_DATABASE', 'healthcare_system'),
            'username' => Env::get('DB_USERNAME', 'root'),
            'password' => Env::get('DB_PASSWORD', ''),
            'charset'  => 'utf8'
        ]
    ]
];
