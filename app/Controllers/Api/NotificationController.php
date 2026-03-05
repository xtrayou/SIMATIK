<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * GET /api/notifications — notifikasi terbaru (untuk dropdown navbar)
     */
    public function latest()
    {
        $role  = session()->get('role') ?? 'staff';
        $limit = (int) ($this->request->getGet('limit') ?? 5);

        $notifications = $this->notificationModel->getUnreadForRole($role, $limit);
        $unreadCount   = $this->notificationModel->countUnreadForRole($role);

        return $this->jsonResponse([
            'status'       => true,
            'unread_count' => $unreadCount,
            'data'         => $notifications,
        ]);
    }

    /**
     * GET /api/notifications/count — jumlah belum dibaca
     */
    public function count()
    {
        $role = session()->get('role') ?? 'staff';
        $count = $this->notificationModel->countUnreadForRole($role);

        return $this->jsonResponse([
            'status' => true,
            'count'  => $count,
        ]);
    }
}
