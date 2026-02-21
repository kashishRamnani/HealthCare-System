<?php
namespace App\Middleware;

use App\Constants\Status;

function requireRole(array|string $roles)
{
    if (!isset($_SESSION['user'])) {
        http_response_code(Status::FORBIDDEN);
        echo json_encode(['error' => 'Login required']);
        exit;
    }

    $userRole = strtolower($_SESSION['user']['role']); // convert to lowercase

    if (is_array($roles)) {
        $roles = array_map('strtolower', $roles); // convert allowed roles to lowercase
        if (!in_array($userRole, $roles)) {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Access denied: role not allowed']);
            exit;
        }
    } else {
        if ($userRole !== strtolower($roles)) {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Access denied: role not allowed']);
            exit;
        }
    }
}
?>