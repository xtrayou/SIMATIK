<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;

class NotifikasiController extends BaseController
{
    protected NotifikasiModel $modelNotifikasi;

    public function __construct()
    {
        $this->modelNotifikasi = new NotifikasiModel();
    }

    /**
     * GET /api/notifications — notifikasi terbaru (untuk dropdown navbar)
     */
    public function latest()
    {
        $role  = session()->get('role') ?? 'staff';
        $limit = (int) ($this->request->getGet('limit') ?? 5);

        $notifications = $this->modelNotifikasi->getUnreadForRole($role, $limit);
        $unreadCount   = $this->modelNotifikasi->countUnreadForRole($role);

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
        $count = $this->modelNotifikasi->countUnreadForRole($role);

        return $this->jsonResponse([
            'status' => true,
            'count'  => $count,
        ]);
    }
}
