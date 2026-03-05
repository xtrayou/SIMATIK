<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanItemModel extends Model
{
    protected $table = 'loan_items';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'loan_id',
        'product_id',
        'quantity',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
