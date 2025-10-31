<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Notifications extends BaseController
{
    public function get(): ResponseInterface
    {
        $session = session();
        $userId = (int) $session->get('id');
        if (!$session->get('isLoggedIn') || !$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $model = new NotificationModel();
        $count = $model->getUnreadCount($userId);
        $items = $model->getNotificationsForUser($userId, 5);

        return $this->response->setJSON([
            'count' => $count,
            'items' => $items,
        ]);
    }

    public function mark_as_read(int $id): ResponseInterface
    {
        $session = session();
        $userId = (int) $session->get('id');
        if (!$session->get('isLoggedIn') || !$userId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        // Method is constrained by route (POST); no additional check here.

        $model = new NotificationModel();

        // Optional ownership check (safer): ensure the notification belongs to the user
        $notif = $model->find($id);
        if (!$notif || (int) $notif['user_id'] !== $userId) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }

        $ok = $model->markAsRead($id);
        return $this->response->setJSON(['success' => (bool) $ok]);
    }
}
