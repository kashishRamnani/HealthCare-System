
<?php
use App\Helpers\Env;
return [
    'name' => Env::get('APP_NAME', 'HealthCare System'),
    'env' => Env::get('APP_ENV', 'local'),
    'debug' => Env::get('APP_DEBUG', false),
    'url' => Env::get('APP_URL', 'http://localhost'),
    'timezone' => Env::get('APP_TIMEZONE', 'Asia/Karachi'),
    'key' => Env::get('APP_KEY'),
];
?>