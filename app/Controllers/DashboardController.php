<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\StockMovementModel;

class DashboardController extends BaseController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;
    protected StockMovementModel $stockMovementModel;

    public function __construct()
    {
        $this->productModel        = new ProductModel();
        $this->categoryModel       = new CategoryModel();
        $this->stockMovementModel  = new StockMovementModel();
    }

    public function index()
    {
        $this->setPageData('Dashboard', 'Ringkasan sistem inventaris dan statistik');

        $stats = [
            'total_products'     => $this->countActiveProducts(),
            'total_categories'   => $this->countActiveCategories(),
            'inventory_value'    => $this->productModel->getTotalInventoryValue(),
            'low_stock_count'    => count($this->productModel->getLowStockProducts()),
            'out_of_stock_count' => $this->countOutOfStockProducts(),
        ];

        $data = array_merge($stats, [
            'recent_movements'    => $this->stockMovementModel->getMovementsWithProduct(10),
            'low_stock_products'  => $this->productModel->getLowStockProducts(8),
            'top_products'        => $this->getTopProductsByValue(5),
            'chart_data'          => [
                'monthly_movements'      => $this->getMonthlyMovementChartData(),
                'category_distribution'  => $this->getCategoryDistributionChartData(),
                'stock_status_pie'       => $this->getStockStatusChartData(),
            ],
            'quick_stats' => $this->getQuickStats(),
        ]);

        return $this->render('dashboard/index', $data);
    }

    /**
     * Count active products
     */
    private function countActiveProducts(): int
    {
        return $this->productModel->where('is_active', true)->countAllResults();
    }

    /**
     * Count active categories
     */
    private function countActiveCategories(): int
    {
        return $this->categoryModel->where('is_active', true)->countAllResults();
    }

    /**
     * Count out of stock products
     */
    private function countOutOfStockProducts(): int
    {
        return $this->productModel
            ->where('current_stock', 0)
            ->where('is_active', true)
            ->countAllResults();
    }

    /**
     * Get quick weekly stats
     */
    private function getQuickStats(): array
    {
        $today = date('Y-m-d');
        $thisWeek = date('Y-m-d', strtotime('-7 days'));

        $totalIn = $this->stockMovementModel->where('type', 'IN')
            ->where('created_at >=', $thisWeek)
            ->selectSum('quantity', 'total')->first()['total'] ?? 0;

        $totalOut = $this->stockMovementModel->where('type', 'OUT')
            ->where('created_at >=', $thisWeek)
            ->selectSum('quantity', 'total')->first()['total'] ?? 0;

        return [
            'today_movements' => $this->stockMovementModel->where("DATE(created_at)", $today, false)->countAllResults(),
            'this_week_in'    => (int) $totalIn,
            'this_week_out'   => (int) $totalOut,
        ];
    }

    /**
     * Get top products by financial value
     */
    private function getTopProductsByValue(int $limit = 5): array
    {
        return $this->productModel->select('
                products.*,
                categories.name as category_name,
                (products.current_stock * products.price) as total_value
            ')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true)
            ->where('products.current_stock >', 0)
            ->orderBy('total_value', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Prepare monthly movement chart data
     */
    private function getMonthlyMovementChartData(): array
    {
        $movements = $this->stockMovementModel->getMonthlyMovements();
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $labels = [];
        $stockIn = array_fill(0, 6, 0);
        $stockOut = array_fill(0, 6, 0);

        // Define labels for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $labels[] = $monthNames[(int) date('n', strtotime("-$i months")) - 1];
        }

        foreach ($movements as $row) {
            $monthLabel = $monthNames[(int) $row['month'] - 1];
            $idx = array_search($monthLabel, $labels, true);
            if ($idx === false) continue;

            $qty = (int) $row['total_quantity'];
            if ($row['type'] === 'IN') {
                $stockIn[$idx] = $qty;
            } elseif ($row['type'] === 'OUT') {
                $stockOut[$idx] = $qty;
            }
        }

        return ['labels' => $labels, 'stock_in' => $stockIn, 'stock_out' => $stockOut];
    }

    /**
     * Prepare category distribution chart data
     */
    private function getCategoryDistributionChartData(): array
    {
        $categories = $this->categoryModel->select('
                categories.name,
                COUNT(products.id) as product_count,
                SUM(products.current_stock * products.price) as total_value
            ')
            ->join('products', 'products.category_id = categories.id', 'left')
            ->where('categories.is_active', true)
            ->groupBy('categories.id')
            ->orderBy('product_count', 'DESC')
            ->findAll();

        return [
            'labels' => array_column($categories, 'name'),
            'data'   => array_map('intval', array_column($categories, 'product_count')),
            'values' => array_map('floatval', array_column($categories, 'total_value')),
        ];
    }

    /**
     * Prepare stock status pie chart data
     */
    private function getStockStatusChartData(): array
    {
        $outOfStock = $this->productModel->where('current_stock', 0)->where('is_active', true)->countAllResults();

        $lowStock = $this->productModel
            ->where('current_stock <= products.min_stock', null, false)
            ->where('current_stock >', 0)
            ->where('is_active', true)
            ->countAllResults();

        $normalStock = $this->productModel
            ->where('current_stock > products.min_stock', null, false)
            ->where('is_active', true)
            ->countAllResults();

        return [
            'labels' => ['Habis', 'Stok Rendah', 'Normal'],
            'data'   => [$outOfStock, $lowStock, $normalStock],
            'colors' => ['#dc3545', '#ffc107', '#198754'],
        ];
    }
}
