<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type',
        'title',
        'message',
        'icon',
        'color',
        'url',
        'reference_type',
        'reference_id',
        'for_role',
        'is_read',
        'read_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ────────────────────────────────────────────────────
    // Query helpers
    // ────────────────────────────────────────────────────

    /**
     * Ambil notifikasi untuk role tertentu (belum dibaca)
     */
    public function getUnreadForRole(string $role, int $limit = 10): array
    {
        return $this->whereIn('for_role', [$role, 'all'])
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Hitung notifikasi belum dibaca untuk role
     */
    public function countUnreadForRole(string $role): int
    {
        return $this->whereIn('for_role', [$role, 'all'])
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * Ambil semua notifikasi untuk role (paginated)
     */
    public function getForRole(string $role, int $perPage = 20)
    {
        return $this->whereIn('for_role', [$role, 'all'])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca
     */
    public function markAsRead(int $id, int $userId): bool
    {
        return $this->update($id, [
            'is_read' => 1,
            'read_by' => $userId,
        ]);
    }

    /**
     * Tandai semua notifikasi role sebagai dibaca
     */
    public function markAllAsRead(string $role, int $userId): int
    {
        $builder = $this->builder();
        return $builder->whereIn('for_role', [$role, 'all'])
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_by' => $userId,
            ]);
    }

    /**
     * Hapus notifikasi lama (> 30 hari)
     */
    public function cleanOld(int $days = 30): int
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('created_at <', $cutoff)->delete();
    }

    // ────────────────────────────────────────────────────
    // Factory: buat notifikasi dari event sistem
    // ────────────────────────────────────────────────────

    /**
     * Notifikasi: stok rendah
     */
    public function createLowStockNotification(array $product): int|false
    {
        // Cek apakah sudah ada notifikasi serupa yang belum dibaca
        $existing = $this->where('type', 'low_stock')
            ->where('reference_type', 'product')
            ->where('reference_id', $product['id'])
            ->where('is_read', 0)
            ->first();

        if ($existing) return false; // Sudah ada, skip

        $productId   = $product['id'];
        $productName = $product['name'] ?? 'Produk';
        $currentStock = $product['current_stock'] ?? 0;
        $minStock     = $product['min_stock'] ?? 0;

        return $this->insert([
            'type'           => 'low_stock',
            'title'          => 'Stok Rendah!',
            'message'        => "Stok {$productName} tinggal {$currentStock} unit (minimum: {$minStock}).",
            'icon'           => 'bi-exclamation-triangle-fill',
            'color'          => 'warning',
            'url'            => "/products/show/{$productId}",
            'reference_type' => 'product',
            'reference_id'   => $productId,
            'for_role'       => 'all',
        ]);
    }

    /**
     * Notifikasi: stok habis
     */
    public function createOutOfStockNotification(array $product): int|false
    {
        $existing = $this->where('type', 'out_of_stock')
            ->where('reference_type', 'product')
            ->where('reference_id', $product['id'])
            ->where('is_read', 0)
            ->first();

        if ($existing) return false;

        $productId   = $product['id'];
        $productName = $product['name'] ?? 'Produk';

        return $this->insert([
            'type'           => 'out_of_stock',
            'title'          => 'Stok Habis!',
            'message'        => "Stok {$productName} telah habis (0 unit).",
            'icon'           => 'bi-x-circle-fill',
            'color'          => 'danger',
            'url'            => "/products/show/{$productId}",
            'reference_type' => 'product',
            'reference_id'   => $productId,
            'for_role'       => 'all',
        ]);
    }

    /**
     * Notifikasi: permintaan ATK baru
     */
    public function createNewLoanNotification(array $loan): int|false
    {
        $loanId = $loan['id'] ?? 0;

        return $this->insert([
            'type'           => 'new_loan',
            'title'          => 'Permintaan ATK Baru',
            'message'        => "Permintaan baru dari {$loan['borrower_name']} - Unit {$loan['borrower_unit']}.",
            'icon'           => 'bi-journal-arrow-down',
            'color'          => 'info',
            'url'            => "/loans/show/{$loanId}",
            'reference_type' => 'loan',
            'reference_id'   => $loanId,
            'for_role'       => 'admin',
        ]);
    }

    /**
     * Notifikasi: permintaan disetujui
     */
    public function createLoanApprovedNotification(array $loan): int|false
    {
        $loanId = $loan['id'] ?? 0;

        return $this->insert([
            'type'           => 'loan_approved',
            'title'          => 'Permintaan Disetujui',
            'message'        => "Permintaan #{$loanId} dari {$loan['borrower_name']} telah disetujui.",
            'icon'           => 'bi-check-circle-fill',
            'color'          => 'success',
            'url'            => "/loans/show/{$loanId}",
            'reference_type' => 'loan',
            'reference_id'   => $loanId,
            'for_role'       => 'all',
        ]);
    }

    /**
     * Notifikasi: permintaan dibatalkan
     */
    public function createLoanCancelledNotification(array $loan): int|false
    {
        $loanId = $loan['id'] ?? 0;

        return $this->insert([
            'type'           => 'loan_cancelled',
            'title'          => 'Permintaan Dibatalkan',
            'message'        => "Permintaan #{$loanId} dari {$loan['borrower_name']} telah dibatalkan.",
            'icon'           => 'bi-x-circle',
            'color'          => 'secondary',
            'url'            => "/loans/show/{$loanId}",
            'reference_type' => 'loan',
            'reference_id'   => $loanId,
            'for_role'       => 'all',
        ]);
    }

    /**
     * Notifikasi: barang masuk
     */
    public function createStockInNotification(string $productName, int $quantity, string $reference): int|false
    {
        return $this->insert([
            'type'           => 'stock_in',
            'title'          => 'Barang Masuk',
            'message'        => "{$productName}: +{$quantity} unit masuk (Ref: {$reference}).",
            'icon'           => 'bi-arrow-down-circle-fill',
            'color'          => 'success',
            'url'            => '/stock/history',
            'reference_type' => 'stock_movement',
            'for_role'       => 'admin',
        ]);
    }

    /**
     * Notifikasi: barang keluar
     */
    public function createStockOutNotification(string $productName, int $quantity, string $reference): int|false
    {
        return $this->insert([
            'type'           => 'stock_out',
            'title'          => 'Barang Keluar',
            'message'        => "{$productName}: -{$quantity} unit keluar (Ref: {$reference}).",
            'icon'           => 'bi-arrow-up-circle-fill',
            'color'          => 'danger',
            'url'            => '/stock/history',
            'reference_type' => 'stock_movement',
            'for_role'       => 'admin',
        ]);
    }
}
