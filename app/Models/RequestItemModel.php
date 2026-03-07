<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestItemModel extends Model
{
    protected $table = 'request_items';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'request_id',
        'product_id',
        'quantity',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
