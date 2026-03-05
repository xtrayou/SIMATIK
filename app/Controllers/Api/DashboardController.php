<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class DashboardController extends BaseController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Get dashboard statistics via AJAX
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_products'   => $this->productModel->where('is_active', true)->countAllResults(),
                'total_categories' => $this->categoryModel->where('is_active', true)->countAllResults(),
                'low_stock_count'  => count($this->productModel->getLowStockProducts()),
            ];

            return $this->jsonResponse([
                'status' => true,
                'stats'  => $stats
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
