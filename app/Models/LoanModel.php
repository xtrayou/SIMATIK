<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanModel extends Model
{
    protected $table = 'loans';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'borrower_name',
        'borrower_identifier',
        'borrower_unit',
        'email',
        'loan_date',
        'due_date',
        'status',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get loan details with its items
     */
    public function getLoanWithItems(int $id): ?array
    {
        $loan = $this->find($id);
        if (!$loan) {
            return null;
        }

        $loanItemModel = new LoanItemModel();
        $loan['items'] = $loanItemModel->select('loan_items.*, products.name as product_name, products.sku as product_sku, products.unit')
            ->join('products', 'products.id = loan_items.product_id')
            ->where('loan_items.loan_id', $id)
            ->findAll();

        return $loan;
    }
}
