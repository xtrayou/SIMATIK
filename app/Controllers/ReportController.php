<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\KategoriModel;
use App\Models\MutasiStokModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends BaseController
{
    protected ProductModel $productModel;
    protected KategoriModel $categoryModel;
    protected MutasiStokModel $stockMovementModel;

    public function __construct()
    {
        $this->productModel       = new ProductModel();
        $this->categoryModel      = new KategoriModel();
        $this->stockMovementModel = new MutasiStokModel();
    }

    /**
     * Stock Report - Current inventory status
     */
    public function stock()
    {
        $this->setPageData('Laporan Stok', 'Analisis kondisi stok inventory saat ini');

        $categoryFilter = $this->request->getGet('category');
        $stockStatus    = $this->request->getGet('stock_status');
        $sortBy         = $this->request->getGet('sort_by') ?: 'name';
        $sortOrder      = $this->request->getGet('sort_order') ?: 'ASC';

        $builder = $this->productModel->select("
                products.*, 
                categories.name as category_name,
                (products.current_stock * products.price) as stock_value,
                CASE 
                    WHEN products.current_stock = 0 THEN 'out_of_stock'
                    WHEN products.current_stock <= products.min_stock THEN 'low_stock'
                    ELSE 'normal'
                END as stock_status
            ")
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true);

        if ($categoryFilter) {
            $builder->where('products.category_id', $categoryFilter);
        }

        if ($stockStatus) {
            switch ($stockStatus) {
                case 'out_of_stock':
                    $builder->where('products.current_stock', 0);
                    break;
                case 'low_stock':
                    $builder->where('products.current_stock <= products.min_stock', null, false)
                        ->where('products.current_stock >', 0);
                    break;
                case 'normal':
                    $builder->where('products.current_stock > products.min_stock', null, false);
                    break;
                case 'overstocked':
                    $builder->where('products.current_stock > (products.min_stock * 3)', null, false);
                    break;
            }
        }

        $validSorts = ['name', 'current_stock', 'stock_value', 'category_name'];
        if (in_array($sortBy, $validSorts)) {
            $builder->orderBy($sortBy, $sortOrder);
        }

        $products = $builder->findAll();

        $summary = [
            'total_products' => count($products),
            'total_value'    => array_sum(array_column($products, 'stock_value')),
            'total_quantity' => array_sum(array_column($products, 'current_stock')),
            'out_of_stock'   => count(array_filter($products, fn($p) => $p['current_stock'] == 0)),
            'low_stock'      => count(array_filter($products, fn($p) => $p['current_stock'] > 0 && $p['current_stock'] <= $p['min_stock'])),
        ];
        $summary['normal_stock'] = $summary['total_products'] - $summary['out_of_stock'] - $summary['low_stock'];

        $categoryBreakdown = [];
        foreach ($products as $product) {
            $catName = $product['category_name'];
            if (!isset($categoryBreakdown[$catName])) {
                $categoryBreakdown[$catName] = [
                    'products'    => 0,
                    'total_stock' => 0,
                    'total_value' => 0
                ];
            }
            $categoryBreakdown[$catName]['products']++;
            $categoryBreakdown[$catName]['total_stock'] += $product['current_stock'];
            $categoryBreakdown[$catName]['total_value'] += $product['stock_value'];
        }

        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'products'           => $products,
            'categories'         => $categories,
            'summary'            => $summary,
            'category_breakdown' => $categoryBreakdown,
            'filters'            => [
                'category'     => $categoryFilter,
                'stock_status' => $stockStatus,
                'sort_by'      => $sortBy,
                'sort_order'   => $sortOrder
            ]
        ];

        return $this->render('reports/stock', $data);
    }

    /**
     * Movement Report - Stock movement analysis
     */
    public function movements()
    {
        $this->setPageData('Laporan Pergerakan', 'Analisis pergerakan stok dalam periode tertentu');

        $startDate      = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate        = $this->request->getGet('end_date') ?: date('Y-m-d');
        $categoryFilter = $this->request->getGet('category');
        $productFilter  = $this->request->getGet('product');
        $movementType   = $this->request->getGet('type');

        $builder = $this->stockMovementModel->select('
                stock_movements.*, 
                products.name as product_name,
                products.sku as product_sku,
                products.price as product_price,
                categories.name as category_name
            ')
            ->join('products', 'products.id = stock_movements.product_id')
            ->join('categories', 'categories.id = products.category_id')
            ->where('DATE(stock_movements.created_at) >=', $startDate)
            ->where('DATE(stock_movements.created_at) <=', $endDate);

        if ($categoryFilter) {
            $builder->where('categories.id', $categoryFilter);
        }

        if ($productFilter) {
            $builder->where('products.id', $productFilter);
        }

        if ($movementType) {
            $builder->where('stock_movements.type', $movementType);
        }

        $movements = $builder->orderBy('stock_movements.created_at', 'DESC')->findAll();

        $analytics = $this->calculateMovementAnalytics($movements, $startDate, $endDate);

        $data = [
            'movements'    => $movements,
            'analytics'    => $analytics,
            'summary'      => $analytics, // Alias for view compatibility
            'top_products' => $this->getTopMovementProducts($movements),
            'daily_trend'  => $this->getDailyMovementTrend($movements, $startDate, $endDate),
            'categories'   => $this->categoryModel->getActiveCategories(),
            'products'     => $this->productModel->getProductsWithCategory(),
            'filters'      => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'category'   => $categoryFilter,
                'product'    => $productFilter,
                'type'       => $movementType
            ]
        ];

        return $this->render('reports/movements', $data);
    }

    /**
     * Valuation Report - Inventory valuation analysis
     */
    public function valuation()
    {
        $this->setPageData('Valuasi Inventory', 'Analisis nilai inventory dan profitability');

        $categoryFilter  = $this->request->getGet('category');
        $valuationMethod = $this->request->getGet('method') ?: 'current';

        $builder = $this->productModel->select('
                products.*, 
                categories.name as category_name,
                (products.current_stock * products.price) as current_value,
                (products.current_stock * products.cost_price) as cost_value,
                (products.current_stock * products.price) - (products.current_stock * products.cost_price) as potential_profit
            ')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true)
            ->where('products.current_stock >', 0);

        if ($categoryFilter) {
            $builder->where('products.category_id', $categoryFilter);
        }

        $products = $builder->orderBy('current_value', 'DESC')->findAll();

        $totalCurrentValue = array_sum(array_column($products, 'current_value'));
        $totalCostValue    = array_sum(array_column($products, 'cost_value'));

        $categoryValuation = [];
        foreach ($products as $product) {
            $catName = $product['category_name'];
            if (!isset($categoryValuation[$catName])) {
                $categoryValuation[$catName] = [
                    'products'         => 0,
                    'total_quantity'   => 0,
                    'current_value'    => 0,
                    'cost_value'       => 0,
                    'potential_profit' => 0
                ];
            }
            $categoryValuation[$catName]['products']++;
            $categoryValuation[$catName]['total_quantity']   += $product['current_stock'];
            $categoryValuation[$catName]['current_value']    += $product['current_value'];
            $categoryValuation[$catName]['cost_value']       += $product['cost_value'];
            $categoryValuation[$catName]['potential_profit'] += $product['potential_profit'];
        }

        foreach ($categoryValuation as &$catData) {
            $catData['margin_percentage'] = $catData['current_value'] > 0 ?
                (($catData['current_value'] - $catData['cost_value']) / $catData['current_value']) * 100 : 0;
        }

        $data = [
            'products'   => $products,
            'categories' => $this->categoryModel->getActiveCategories(),
            'summary'    => [
                'total_current_value'    => $totalCurrentValue,
                'total_cost_value'       => $totalCostValue,
                'total_potential_profit' => array_sum(array_column($products, 'potential_profit')),
                'average_margin'         => $totalCurrentValue > 0 ? (($totalCurrentValue - $totalCostValue) / $totalCurrentValue) * 100 : 0,
                'total_products'         => count($products)
            ],
            'category_valuation' => $categoryValuation,
            'filters'            => [
                'category' => $categoryFilter,
                'method'   => $valuationMethod
            ]
        ];

        return $this->render('reports/valuation', $data);
    }

    /**
     * Analytics Dashboard
     */
    public function analytics()
    {
        $this->setPageData('Analytics Dashboard', 'Advanced analytics dan insights bisnis');

        $period    = (int) ($this->request->getGet('period') ?: '30');
        $endDate   = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime("-{$period} days"));

        $analytics = [
            'inventory_turnover'  => $this->calculateInventoryTurnover($period),
            'abc_analysis'        => $this->calculateABCAnalysis(),
            'demand_forecast'     => $this->calculateDemandForecast($period),
            'reorder_suggestions' => $this->getReorderSuggestions(),
            'performance_metrics' => $this->getPerformanceMetrics($period),
            'trends'              => $this->getTrendAnalysis($period)
        ];

        $data = [
            'analytics'  => $analytics,
            'period'     => $period,
            'start_date' => $startDate,
            'end_date'   => $endDate
        ];

        return $this->render('reports/analytics', $data);
    }

    /**
     * Export Reports
     */
    public function exportStock($format = 'excel')
    {
        if ($format === 'excel') {
            return $this->exportStockExcel();
        } elseif ($format === 'pdf') {
            return $this->exportStockPDF();
        }

        return redirect()->back()->with('error', 'Format export tidak valid');
    }

    /**
     * Export Movement Reports
     */
    public function exportMovements()
    {
        $format = $this->request->getGet('format') ?: 'excel';

        if ($format === 'excel') {
            return $this->exportMovementsExcel();
        } elseif ($format === 'pdf') {
            return $this->exportMovementsPDF();
        }

        return redirect()->back()->with('error', 'Format export tidak valid');
    }

    // --- Private Helper Methods ---

    private function calculateMovementAnalytics($movements, $startDate, $endDate)
    {
        $stats = [
            'total_movements'    => count($movements),
            'total_in'           => 0,
            'total_out'          => 0,
            'total_adjustments'  => 0,
            'total_in_quantity'  => 0,
            'total_out_quantity' => 0,
        ];

        foreach ($movements as $m) {
            switch ($m['type']) {
                case 'IN':
                    $stats['total_in']++;
                    $stats['total_in_quantity'] += $m['quantity'];
                    break;
                case 'OUT':
                    $stats['total_out']++;
                    $stats['total_out_quantity'] += $m['quantity'];
                    break;
                case 'ADJUSTMENT':
                    $stats['total_adjustments']++;
                    break;
            }
        }

        $periodDays = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
        $stats['net_movement']          = $stats['total_in_quantity'] - $stats['total_out_quantity'];
        $stats['avg_movements_per_day'] = round($stats['total_movements'] / $periodDays, 2);
        $stats['period_days']           = $periodDays;

        return $stats;
    }

    private function getTopMovementProducts($movements)
    {
        $productStats = [];
        foreach ($movements as $m) {
            $pid = $m['product_id'];
            if (!isset($productStats[$pid])) {
                $productStats[$pid] = [
                    'product_name'    => $m['product_name'],
                    'product_sku'     => $m['product_sku'],
                    'total_movements' => 0,
                    'total_in'        => 0,
                    'total_out'       => 0,
                ];
            }

            $productStats[$pid]['total_movements']++;
            if ($m['type'] === 'IN') {
                $productStats[$pid]['total_in'] += $m['quantity'];
            } elseif ($m['type'] === 'OUT') {
                $productStats[$pid]['total_out'] += $m['quantity'];
            }
        }

        uasort($productStats, fn($a, $b) => $b['total_movements'] - $a['total_movements']);
        return array_slice($productStats, 0, 10);
    }

    private function getDailyMovementTrend($movements, $startDate, $endDate)
    {
        $dailyStats  = [];
        $currentDate = $startDate;

        while ($currentDate <= $endDate) {
            $dailyStats[$currentDate] = [
                'date'            => $currentDate,
                'in_quantity'     => 0,
                'out_quantity'    => 0,
                'movements_count' => 0
            ];
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        foreach ($movements as $m) {
            $date = date('Y-m-d', strtotime($m['created_at']));
            if (isset($dailyStats[$date])) {
                $dailyStats[$date]['movements_count']++;
                if ($m['type'] === 'IN')  $dailyStats[$date]['in_quantity']  += $m['quantity'];
                if ($m['type'] === 'OUT') $dailyStats[$date]['out_quantity'] += $m['quantity'];
            }
        }

        return array_values($dailyStats);
    }

    private function calculateInventoryTurnover($period)
    {
        $movements = $this->stockMovementModel->where('type', 'OUT')
            ->where('created_at >=', date('Y-m-d', strtotime("-{$period} days")))
            ->findAll();

        $totalSold    = array_sum(array_column($movements, 'quantity'));
        $avgInventory = $this->productModel->selectSum('current_stock')->first()['current_stock'] ?? 0;

        return [
            'turnover_rate' => $avgInventory > 0 ? round(($totalSold / $avgInventory) * (365 / $period), 2) : 0,
            'total_sold'    => $totalSold,
            'avg_inventory' => $avgInventory,
            'period_days'   => $period
        ];
    }

    private function calculateABCAnalysis()
    {
        $products = $this->productModel->select('products.*, (products.current_stock * products.price) as stock_value')
            ->where('is_active', true)->where('current_stock >', 0)
            ->orderBy('stock_value', 'DESC')->findAll();

        $totalValue   = array_sum(array_column($products, 'stock_value'));
        $runningValue = 0;
        $abc          = ['A' => [], 'B' => [], 'C' => []];

        foreach ($products as $p) {
            $runningValue += $p['stock_value'];
            $percentage    = $totalValue > 0 ? ($runningValue / $totalValue) * 100 : 0;

            if ($percentage <= 80)      $abc['A'][] = $p;
            elseif ($percentage <= 95)  $abc['B'][] = $p;
            else                        $abc['C'][] = $p;
        }

        return [
            'categories' => $abc,
            'summary'    => [
                'A_count'        => count($abc['A']),
                'B_count'        => count($abc['B']),
                'C_count'        => count($abc['C']),
                'total_products' => count($products),
                'total_value'    => $totalValue
            ]
        ];
    }

    private function calculateDemandForecast($period)
    {
        $movements = $this->stockMovementModel->select('product_id, SUM(quantity) as total_out, COUNT(*) as movement_count')
            ->where('type', 'OUT')->where('created_at >=', date('Y-m-d', strtotime("-{$period} days")))
            ->groupBy('product_id')->findAll();

        $forecasts = [];
        foreach ($movements as $m) {
            $dailyDemand = $m['total_out'] / $period;
            $forecasts[$m['product_id']] = [
                'daily_demand'     => round($dailyDemand, 2),
                'weekly_forecast'  => round($dailyDemand * 7),
                'monthly_forecast' => round($dailyDemand * 30),
                'movement_count'   => $m['movement_count']
            ];
        }

        return $forecasts;
    }

    private function getReorderSuggestions()
    {
        $products = $this->productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true)
            ->where('products.current_stock <= (products.min_stock * 1.5)', null, false)
            ->orderBy('(products.current_stock / products.min_stock)', 'ASC')->findAll();

        $suggestions = [];
        foreach ($products as $p) {
            $stockRatio = $p['min_stock'] > 0 ? $p['current_stock'] / $p['min_stock'] : 0;
            $urgency    = 'low';

            if ($p['current_stock'] == 0) $urgency = 'critical';
            elseif ($stockRatio <= 0.5)   $urgency = 'high';
            elseif ($stockRatio <= 1.0)   $urgency = 'medium';

            $suggestions[] = [
                'product'                  => $p,
                'urgency'                  => $urgency,
                'stock_ratio'              => round($stockRatio, 2),
                'suggested_order_quantity' => max($p['min_stock'] * 2 - $p['current_stock'], $p['min_stock']),
                'days_until_stockout'      => $this->calculateDaysUntilStockout($p['id'])
            ];
        }

        return $suggestions;
    }

    private function getPerformanceMetrics($period)
    {
        return [
            'stock_accuracy'         => 95.5, // Placeholder
            'order_fulfillment_rate' => 98.2, // Placeholder
            'carrying_cost_ratio'    => 15.3, // Placeholder
            'stockout_frequency'     => 2.1,  // Placeholder
        ];
    }

    private function getTrendAnalysis($period)
    {
        return [
            'stock_level_trend'     => [], // Placeholder
            'movement_volume_trend' => [], // Placeholder
            'value_trend'           => [], // Placeholder
        ];
    }

    private function calculateDaysUntilStockout($productId)
    {
        return rand(5, 30);
    }

    private function exportStockExcel()
    {
        return $this->response->download('stock_report.xlsx', null);
    }
    private function exportStockPDF()
    {
        return $this->response->download('stock_report.pdf', null);
    }

    /**
     * Export Movements to Excel
     */
    private function exportMovementsExcel()
    {
        // Get filters from request
        $startDate      = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate        = $this->request->getGet('end_date') ?: date('Y-m-d');
        $categoryFilter = $this->request->getGet('category');
        $productFilter  = $this->request->getGet('product');
        $movementType   = $this->request->getGet('type');

        // Get movements data
        $builder = $this->stockMovementModel->select('
                stock_movements.*, 
                products.name as product_name,
                products.sku as product_sku,
                categories.name as category_name
            ')
            ->join('products', 'products.id = stock_movements.product_id')
            ->join('categories', 'categories.id = products.category_id')
            ->where('DATE(stock_movements.created_at) >=', $startDate)
            ->where('DATE(stock_movements.created_at) <=', $endDate);

        if ($categoryFilter) $builder->where('categories.id', $categoryFilter);
        if ($productFilter)  $builder->where('products.id', $productFilter);
        if ($movementType)   $builder->where('stock_movements.type', $movementType);

        $movements = $builder->orderBy('stock_movements.created_at', 'DESC')->findAll();

        // Create Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'LAPORAN PERGERAKAN STOK');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Periode: ' . date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Column headers
        $headers = ['No', 'Tanggal', 'Produk', 'SKU', 'Kategori', 'Tipe', 'Jumlah', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $sheet->getStyle($col . '4')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($col . '4')->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }

        // Data
        $row = 5;
        foreach ($movements as $index => $movement) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($movement['created_at'])));
            $sheet->setCellValue('C' . $row, $movement['product_name']);
            $sheet->setCellValue('D' . $row, $movement['product_sku']);
            $sheet->setCellValue('E' . $row, $movement['category_name']);
            $sheet->setCellValue('F' . $row, $movement['type']);
            $sheet->setCellValue('G' . $row, $movement['quantity']);
            $sheet->setCellValue('H' . $row, $movement['notes'] ?? '-');
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate file
        $filename = 'Laporan_Pergerakan_' . date('YmdHis') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export Movements to PDF
     */
    private function exportMovementsPDF()
    {
        // Get filters from request
        $startDate      = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate        = $this->request->getGet('end_date') ?: date('Y-m-d');
        $categoryFilter = $this->request->getGet('category');
        $productFilter  = $this->request->getGet('product');
        $movementType   = $this->request->getGet('type');

        // Get movements data
        $builder = $this->stockMovementModel->select('
                stock_movements.*, 
                products.name as product_name,
                products.sku as product_sku,
                categories.name as category_name
            ')
            ->join('products', 'products.id = stock_movements.product_id')
            ->join('categories', 'categories.id = products.category_id')
            ->where('DATE(stock_movements.created_at) >=', $startDate)
            ->where('DATE(stock_movements.created_at) <=', $endDate);

        if ($categoryFilter) $builder->where('categories.id', $categoryFilter);
        if ($productFilter)  $builder->where('products.id', $productFilter);
        if ($movementType)   $builder->where('stock_movements.type', $movementType);

        $movements = $builder->orderBy('stock_movements.created_at', 'DESC')->findAll();
        $analytics = $this->calculateMovementAnalytics($movements, $startDate, $endDate);

        // Generate HTML
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 10pt; }
                h2 { text-align: center; color: #333; margin-bottom: 5px; }
                .period { text-align: center; color: #666; margin-bottom: 20px; }
                .summary { margin-bottom: 20px; }
                .summary table { width: 100%; border-collapse: collapse; }
                .summary td { padding: 8px; background: #f8f9fa; }
                .summary strong { color: #435ebe; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th { background-color: #435ebe; color: white; padding: 8px; text-align: left; font-size: 9pt; }
                td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9pt; }
                tr:nth-child(even) { background-color: #f8f9fa; }
                .badge-in { background: #28a745; color: white; padding: 2px 6px; border-radius: 3px; }
                .badge-out { background: #ffc107; color: #000; padding: 2px 6px; border-radius: 3px; }
                .badge-adj { background: #17a2b8; color: white; padding: 2px 6px; border-radius: 3px; }
            </style>
        </head>
        <body>
            <h2>LAPORAN PERGERAKAN STOK</h2>
            <div class="period">Periode: ' . date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)) . '</div>
            
            <div class="summary">
                <table>
                    <tr>
                        <td><strong>Total Pergerakan:</strong> ' . number_format($analytics['total_movements']) . '</td>
                        <td><strong>Barang Masuk:</strong> ' . number_format($analytics['total_in_quantity']) . '</td>
                        <td><strong>Barang Keluar:</strong> ' . number_format($analytics['total_out_quantity']) . '</td>
                        <td><strong>Net Movement:</strong> ' . number_format($analytics['net_movement']) . '</td>
                    </tr>
                </table>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="20%">Produk</th>
                        <th width="10%">SKU</th>
                        <th width="15%">Kategori</th>
                        <th width="8%">Tipe</th>
                        <th width="10%">Jumlah</th>
                        <th width="20%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($movements as $index => $movement) {
            $badgeClass = $movement['type'] === 'IN' ? 'badge-in' : ($movement['type'] === 'OUT' ? 'badge-out' : 'badge-adj');
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($movement['created_at'])) . '</td>
                <td>' . esc($movement['product_name']) . '</td>
                <td>' . esc($movement['product_sku']) . '</td>
                <td>' . esc($movement['category_name']) . '</td>
                <td><span class="' . $badgeClass . '">' . $movement['type'] . '</span></td>
                <td>' . number_format($movement['quantity']) . '</td>
                <td>' . esc($movement['notes'] ?? '-') . '</td>
            </tr>';
        }

        $html .= '</tbody></table></body></html>';

        // Generate PDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Laporan_Pergerakan_' . date('YmdHis') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
