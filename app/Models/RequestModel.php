<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestModel extends Model
{
    protected $table = 'requests';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'borrower_name',
        'borrower_identifier',
        'borrower_unit',
        'email',
        'request_date',
        'due_date',
        'status',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get request details with its items
     */
    public function getRequestWithItems(int $id): ?array
    {
        $request = $this->find($id);
        if (!$request) {
            return null;
        }

        $requestItemModel = new RequestItemModel();
        $request['items'] = $requestItemModel->select('request_items.*, products.name as product_name, products.sku as product_sku, products.unit')
            ->join('products', 'products.id = request_items.product_id')
            ->where('request_items.request_id', $id)
            ->findAll();

        return $request;
    }
}
