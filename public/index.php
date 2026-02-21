<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\Env;
use App\Database\Connection;

// Load env
Env::load(__DIR__ . '/../.env');

// Database connection
try {
    $db = Connection::make();
} catch (\Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 500,
        'message' => $e->getMessage()
    ]);
    exit;
}

// Get method & URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include route files
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/admin.php';
require __DIR__ . '/../routes/doctor.php';
require __DIR__ . '/../routes/patient.php';
