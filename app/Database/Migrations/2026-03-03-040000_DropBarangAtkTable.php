<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropBarangAtkTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('barang_atk', true);
    }

    public function down()
    {
        // Table has been replaced by 'products' table
    }
}
