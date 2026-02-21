<?php
namespace App\Controllers;

use App\Services\NotificationService;
use App\Constants\Status;

class NotificationController
{
    private NotificationService $service;

    public function __construct()
    {
        $this->service = new NotificationService();
    }

    // Get all notifications
   public function index(int $userId): void
{
    header('Content-Type: application/json');

    // If admin, get all notifications
    if ($_SESSION['user']['role'] === 'admin') {
        $notifications = $this->service->getAll();
    }

    echo json_encode($notifications);
}

    // **Create a notification**
    public function create(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['user_id'], $data['type'], $data['title'], $data['message'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid input']);
            return;
        }

        $created = $this->service->send(
            (int)$data['user_id'],
            $data['type'],
            $data['title'],
            $data['message']
        );

        if ($created) {
            echo json_encode(['message' => 'Notification created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create notification']);
        }
    }

    // Mark as read
    public function markRead(int $notificationId): void
    {
        header('Content-Type: application/json');
        if ($this->service->markRead($notificationId)) {
            echo json_encode(['message' => 'Notification marked as read']);
        } else {
            http_response_code(Status::BAD_REQUEST);
            echo json_encode(['message' => 'Failed to mark notification']);
        }
    }
}
?>