<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];

    public function getUnreadCount(int $userId): int
    {
        return (int) $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function getNotificationsForUser(int $userId, int $limit = 5): array
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    public function markAsRead(int $notificationId): bool
    {
        return (bool) $this->update($notificationId, ['is_read' => 1]);
    }
}
