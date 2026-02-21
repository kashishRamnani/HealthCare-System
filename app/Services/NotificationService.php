<?php
namespace App\Services;

use App\Repositories\NotificationRepository;

class NotificationService
{
    private NotificationRepository $repo;

    public function __construct()
    {
        $this->repo = new NotificationRepository();
    }

    public function send(int $userId, string $type, string $title, string $message): bool
    {
        return $this->repo->create($userId, $type, $title, $message);
    }

  public function getAll(): array
{
    return $this->repo->getAll(); // fetch all notifications
}



    public function markRead(int $notificationId): bool
    {
        return $this->repo->markAsRead($notificationId);
    }
}
?>
