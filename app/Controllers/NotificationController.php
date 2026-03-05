<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Halaman daftar semua notifikasi
     */
    public function index()
    {
        $this->setPageData('Notifikasi', 'Daftar semua notifikasi sistem');

        $role = session()->get('role') ?? 'staff';

        $notifications = $this->notificationModel->getForRole($role, 20);
        $pager = $this->notificationModel->pager;

        $data = [
            'notifications' => $notifications,
            'pager'         => $pager,
            'unreadCount'   => $this->notificationModel->countUnreadForRole($role),
        ];

        return $this->render('notifications/index', $data);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca dan redirect ke URL tujuan
     */
    public function read($id)
    {
        $notification = $this->notificationModel->find($id);

        if (!$notification) {
            return redirect()->to('/notifications')->with('galat', 'Notifikasi tidak ditemukan.');
        }

        $userId = session()->get('userId');
        $this->notificationModel->markAsRead($id, $userId);

        // Redirect ke URL tujuan jika tersedia
        if (!empty($notification['url'])) {
            return redirect()->to($notification['url']);
        }

        return redirect()->to('/notifications');
    }

    /**
     * Tandai semua notifikasi sebagai dibaca
     */
    public function markAllRead()
    {
        $role   = session()->get('role') ?? 'staff';
        $userId = session()->get('userId');

        $this->notificationModel->markAllAsRead($role, $userId);

        if ($this->isAjax()) {
            return $this->jsonResponse(['status' => true, 'message' => 'Semua notifikasi telah dibaca.']);
        }

        return redirect()->to('/notifications')->with('sukses', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Hapus satu notifikasi
     */
    public function delete($id)
    {
        $notification = $this->notificationModel->find($id);

        if (!$notification) {
            return $this->jsonResponse(['status' => false, 'message' => 'Notifikasi tidak ditemukan.'], 404);
        }

        $this->notificationModel->delete($id);

        if ($this->isAjax()) {
            return $this->jsonResponse(['status' => true, 'message' => 'Notifikasi dihapus.']);
        }

        return redirect()->to('/notifications')->with('sukses', 'Notifikasi dihapus.');
    }

    /**
     * Hapus notifikasi lama (admin only)
     */
    public function cleanOld()
    {
        if (session()->get('role') !== 'admin') {
            return $this->jsonResponse(['status' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $deleted = $this->notificationModel->cleanOld(30);

        return $this->jsonResponse([
            'status'  => true,
            'message' => "Berhasil menghapus {$deleted} notifikasi lama.",
        ]);
    }
}
