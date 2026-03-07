<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'sku',
        'category_id',
        'description',
        'price',
        'cost_price',
        'min_stock',
        'current_stock',
        'unit',
        'is_active',
    ];


    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get filtered products with category information
     */
    public function getFilteredProducts(array $filters = []): array
    {
        $builder = $this->select("
                    products.id, 
                    products.name, 
                    products.sku, 
                    products.category_id, 
                    products.description, 
                    products.price, 
                    products.cost_price, 
                    products.min_stock, 
                    products.current_stock, 
                    products.unit, 
                    products.is_active,
                    categories.name as category_name,
                    CASE 
                        WHEN products.current_stock = 0 THEN 'habis'
                        WHEN products.current_stock <= products.min_stock THEN 'rendah'
                        ELSE 'normal'
                    END as stock_status
                ")
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('products.name', $filters['search'])
                ->orLike('products.sku', $filters['search'])
                ->orLike('products.description', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['category'])) {
            $builder->where('products.category_id', $filters['category']);
        }

        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'habis':
                    $builder->where('products.current_stock', 0);
                    break;
                case 'rendah':
                    $builder->where('products.current_stock <= products.min_stock', null, false)
                        ->where('products.current_stock >', 0);
                    break;
                case 'normal':
                    $builder->where('products.current_stock > products.min_stock', null, false);
                    break;
            }
        }

        return $builder->orderBy('products.name', 'ASC')->findAll();
    }

    /**
     * Get all products with category name
     */
    public function getProductsWithCategory(): array
    {
        return $this->select('products.id, products.name, products.sku, products.category_id, products.current_stock, products.unit, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true)
            ->orderBy('products.name', 'ASC')
            ->findAll();
    }

    /**
     * Get single product with category name
     */
    public function getProductWithCategory(int $id): ?array
    {
        return $this->select('products.id, products.name, products.sku, products.category_id, products.description, products.price, products.cost_price, products.min_stock, products.current_stock, products.unit, products.is_active, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.id', $id)
            ->first();
    }

    /**
     * Calculate total inventory value (stock * price)
     */
    public function getTotalInventoryValue(): float
    {
        $result = $this->select('SUM(current_stock * price) as total_value', false)
            ->where('is_active', true)
            ->first();

        return (float) ($result['total_value'] ?? 0);
    }

    /**
     * Get products with low stock
     */
    public function getLowStockProducts(int $limit = 0): array
    {
        $builder = $this->select('products.id, products.name, products.sku, products.current_stock, products.min_stock, products.unit, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.current_stock <= products.min_stock', null, false)
            ->where('products.current_stock >', 0)
            ->where('products.is_active', true)
            ->orderBy('products.current_stock', 'ASC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Generate SKU automatically based on category and product name
     */
    public function generateSKU(int $categoryId, string $productName): ?string
    {
        $modelKategori = new KategoriModel();
        $category = $modelKategori->find($categoryId);
        if (!$category) {
            return null;
        }

        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category['name'] ?? ''), 0, 3));
        $namePart = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $productName), 0, 3));

        $lastProduct = $this->like('sku', $prefix . $namePart, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $number = 1;
        if ($lastProduct) {
            $lastNumber = (int) substr($lastProduct['sku'], strlen($prefix . $namePart));
            $number = $lastNumber + 1;
        }

        return $prefix . $namePart . str_pad((string)$number, 4, '0', STR_PAD_LEFT);
    }
}
