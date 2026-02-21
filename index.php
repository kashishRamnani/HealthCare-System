<?php

require __DIR__ . '/vendor/autoload.php';
use App\Helpers\Env;
use App\Database\Connection;

Env::load(__DIR__ . '/.env');

echo Env::get('APP_NAME');

$db = Connection::make();
echo "<br/>DataBase Connected Successfully";
?>