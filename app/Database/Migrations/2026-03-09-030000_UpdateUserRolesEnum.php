<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUserRolesEnum extends Migration
{
    public function up()
    {
        // Modify role column to new ENUM values
        // Old: ['admin', 'staff']
        // New: ['superadmin', 'admin', 'user']

        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['superadmin', 'admin', 'user'],
                'default'    => 'user',
            ],
        ];

        $this->forge->modifyColumn('users', $fields);

        // Update existing 'staff' records to 'admin'
        $this->db->query("UPDATE users SET role = 'admin' WHERE role = 'staff'");
    }

    public function down()
    {
        // Revert to old ENUM values
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'staff'],
                'default'    => 'staff',
            ],
        ];

        $this->forge->modifyColumn('users', $fields);

        // Revert changes
        $this->db->query("UPDATE users SET role = 'staff' WHERE role IN ('admin', 'user')");
        $this->db->query("DELETE FROM users WHERE role = 'superadmin'");
    }
}
