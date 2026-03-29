<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusReasonToRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('requests', [
            'status_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status',
            ],
        ]);
        
        $this->forge->addColumn('products', [
            'stock_baik' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'current_stock',
            ],
            'stock_rusak' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'stock_baik',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('requests', 'status_reason');
        $this->forge->dropColumn('products', ['stock_baik', 'stock_rusak']);
    }
}
