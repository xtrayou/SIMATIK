<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        // Tabel permissions — daftar hak akses
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
                'comment'    => 'Nama permission, misal: products.create',
            ],
            'group' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Grup menu: categories, products, stock, requests, reports, users, settings',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('permissions');

        // Tabel role_permissions — pivot role ↔ permission
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'staff'],
            ],
            'permission_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['role', 'permission_id']);
        $this->forge->createTable('role_permissions');

        // ──────────────────────────────────────────────
        // Seed default permissions
        // ──────────────────────────────────────────────
        $permissions = [
            // Categories
            ['name' => 'categories.view',   'group' => 'categories', 'description' => 'Melihat daftar kategori'],
            ['name' => 'categories.create', 'group' => 'categories', 'description' => 'Menambah kategori baru'],
            ['name' => 'categories.edit',   'group' => 'categories', 'description' => 'Mengedit kategori'],
            ['name' => 'categories.delete', 'group' => 'categories', 'description' => 'Menghapus kategori'],

            // Products
            ['name' => 'products.view',   'group' => 'products', 'description' => 'Melihat daftar produk'],
            ['name' => 'products.create', 'group' => 'products', 'description' => 'Menambah produk baru'],
            ['name' => 'products.edit',   'group' => 'products', 'description' => 'Mengedit produk'],
            ['name' => 'products.delete', 'group' => 'products', 'description' => 'Menghapus produk'],
            ['name' => 'products.export', 'group' => 'products', 'description' => 'Ekspor data produk'],

            // Stock
            ['name' => 'stock.in',         'group' => 'stock', 'description' => 'Input barang masuk'],
            ['name' => 'stock.out',        'group' => 'stock', 'description' => 'Input barang keluar'],
            ['name' => 'stock.adjustment', 'group' => 'stock', 'description' => 'Penyesuaian stok'],
            ['name' => 'stock.history',    'group' => 'stock', 'description' => 'Lihat riwayat stok'],
            ['name' => 'stock.alerts',     'group' => 'stock', 'description' => 'Lihat peringatan stok'],

            // Requests / Permintaan ATK
            ['name' => 'requests.view',    'group' => 'requests', 'description' => 'Melihat daftar permintaan'],
            ['name' => 'requests.create',  'group' => 'requests', 'description' => 'Membuat permintaan baru'],
            ['name' => 'requests.approve', 'group' => 'requests', 'description' => 'Menyetujui permintaan'],
            ['name' => 'requests.cancel',  'group' => 'requests', 'description' => 'Membatalkan permintaan'],

            // Reports
            ['name' => 'reports.view',   'group' => 'reports', 'description' => 'Melihat laporan'],
            ['name' => 'reports.export', 'group' => 'reports', 'description' => 'Ekspor laporan'],

            // Users
            ['name' => 'users.view',   'group' => 'users', 'description' => 'Melihat daftar user'],
            ['name' => 'users.create', 'group' => 'users', 'description' => 'Menambah user baru'],
            ['name' => 'users.edit',   'group' => 'users', 'description' => 'Mengedit user'],
            ['name' => 'users.delete', 'group' => 'users', 'description' => 'Menghapus user'],

            // Settings
            ['name' => 'settings.view',   'group' => 'settings', 'description' => 'Melihat pengaturan'],
            ['name' => 'settings.update', 'group' => 'settings', 'description' => 'Mengubah pengaturan'],

            // Notifications
            ['name' => 'notifications.view', 'group' => 'notifications', 'description' => 'Melihat notifikasi'],
        ];

        $db = \Config\Database::connect();

        foreach ($permissions as $perm) {
            $db->table('permissions')->insert($perm);
        }

        // Admin mendapat SEMUA permission
        $allPermissions = $db->table('permissions')->get()->getResultArray();
        foreach ($allPermissions as $perm) {
            $db->table('role_permissions')->insert([
                'role'          => 'admin',
                'permission_id' => $perm['id'],
            ]);
        }

        // Staff mendapat permission terbatas
        $staffPermissions = [
            'categories.view',
            'products.view',
            'stock.in',
            'stock.out',
            'stock.history',
            'stock.alerts',
            'requests.view',
            'requests.create',
            'reports.view',
            'notifications.view',
        ];

        foreach ($staffPermissions as $permName) {
            $perm = $db->table('permissions')->where('name', $permName)->get()->getRowArray();
            if ($perm) {
                $db->table('role_permissions')->insert([
                    'role'          => 'staff',
                    'permission_id' => $perm['id'],
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('role_permissions', true);
        $this->forge->dropTable('permissions', true);
    }
}
