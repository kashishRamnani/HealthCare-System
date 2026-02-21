<?php
namespace App\Repositories;

use App\Database\Connection;
use PDO;

class NotificationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::make();
    }

 public function create(int $userId, string $type, string $title, string $message): bool
{
    try {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, type, title, message)
            VALUES (:user_id, :type, :title, :message)
        ");
        return $stmt->execute([
            ':user_id' => $userId,
            ':type' => $type,
            ':title' => $title,
            ':message' => $message,
        ]);
    } catch (\PDOException $e) {
        if ($e->getCode() === '23000') { 
            throw new \Exception("Cannot create notification: user_id $userId does not exist");
        }
        throw $e;
    }
}

public function getAll(): array
{
    $stmt = $this->db->query("
        SELECT id, user_id, type, title, message, is_read, created_at
        FROM notifications
        ORDER BY created_at DESC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    // Mark notification as read
    public function markAsRead(int $notificationId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $notificationId]);
    }
}
?>
