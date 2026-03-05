<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'description',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active categories
     */
    public function getActiveCategories(): array
    {
        return $this->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get categories with product count
     */
    public function getCategoriesWithProductCount(
        string $keyword = '',
        $status = null,
        string $orderBy = 'name',
        string $orderDir = 'ASC',
        int $limit = 0,
        int $offset = 0
    ): array {
        $builder = $this->select('categories.*, COUNT(products.id) as product_count')
            ->join('products', 'products.category_id = categories.id', 'left')
            ->groupBy('categories.id');

        if ($keyword) {
            $builder->like('categories.name', $keyword)
                ->orLike('categories.description', $keyword);
        }

        if ($status !== null && $status !== '') {
            $builder->where('categories.is_active', $status);
        }

        $builder->orderBy('categories.' . $orderBy, $orderDir);

        if ($limit > 0) {
            return $builder->findAll($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Count categories with filters
     */
    public function countCategories(string $keyword = '', $status = null): int
    {
        $builder = $this;

        if ($keyword) {
            $builder->like('name', $keyword)
                ->orLike('description', $keyword);
        }

        if ($status !== null && $status !== '') {
            $builder->where('is_active', $status);
        }

        return $builder->countAllResults();
    }

    /**
     * Check if category can be deleted (no related products)
     */
    public function canDelete(int $id): bool
    {
        $productModel = new ProductModel();
        $productCount = $productModel->where('category_id', $id)->countAllResults();

        return $productCount === 0;
    }
}
