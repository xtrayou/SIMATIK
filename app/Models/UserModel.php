<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'password', 'name', 'role', 'is_active'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Ambil daftar permission untuk user berdasarkan role-nya
     */
    public function getUserPermissions(int $userId): array
    {
        $user = $this->find($userId);
        if (!$user) return [];

        $permissionModel = new PermissionModel();
        return $permissionModel->getPermissionNamesByRole($user['role']);
    }

    /**
     * Cek apakah user punya permission tertentu
     */
    public function hasPermission(int $userId, string $permissionName): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;

        // Admin selalu punya semua permission
        if ($user['role'] === 'admin') return true;

        $permissionModel = new PermissionModel();
        return $permissionModel->roleHasPermission($user['role'], $permissionName);
    }
}
