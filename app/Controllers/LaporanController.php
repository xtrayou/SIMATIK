<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\MutasiStokModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class LaporanController extends BaseController
{
    protected ProdukModel $modelProduk;
    protected KategoriModel $modelKategori;
    protected MutasiStokModel $modelMutasiStok;

    public function __construct()
    {
        $this->modelProduk       = new ProdukModel();
        $this->modelKategori      = new KategoriModel();
        $this->modelMutasiStok = new MutasiStokModel();
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
        $month          = $this->request->getGet('month') ?: date('m');
        $year           = $this->request->getGet('year') ?: date('Y');

        // Tentukan apakah pakai data arsip atau stok real-time
        $isArchived = $this->isArchivedPeriod((int) $month, (int) $year);
        $products = $isArchived 
            ? $this->getArchivedStockReport((int) $month, (int) $year, $categoryFilter, $sortBy, $sortOrder)
            : $this->getCurrentStockReport($categoryFilter, $stockStatus, $sortBy, $sortOrder);

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

        $categories = $this->modelKategori->getActiveCategories();

        $data = [
            'products'           => $products,
            'categories'         => $categories,
            'summary'            => $summary,
            'category_breakdown' => $categoryBreakdown,
            'filters'            => [
                'category'     => $categoryFilter,
                'stock_status' => $stockStatus,
                'sort_by'      => $sortBy,
                'sort_order'   => $sortOrder,
                'month'        => $month,
                'year'         => $year,
                'is_archived'  => $isArchived
            ]
        ];

        return $this->render('reports/stock', $data);
    }

    /**
     * Cek apakah periode yang diminta adalah data arsip (bukan bulan/tahun saat ini)
     */
    private function isArchivedPeriod(int $month, int $year): bool
    {
        $currentMonth = (int) date('m');
        $currentYear  = (int) date('Y');
        return !($month === $currentMonth && $year === $currentYear);
    }

    /**
     * Ambil data stok dari stock_opname_archives untuk periode tertentu
     */
    private function getArchivedStockReport(int $month, int $year, ?string $categoryFilter, string $sortBy, string $sortOrder): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('stock_opname_archives')
            ->select('
                stock_opname_archives.product_id,
                stock_opname_archives.product_name as name,
                stock_opname_archives.quantity as current_stock,
                stock_opname_archives.unit_price as price,
                stock_opname_archives.total_value as stock_value,
                "archived" as stock_status,
                COALESCE(products.min_stock, 0) as min_stock,
                COALESCE(categories.name, "Uncategorized") as category_name,
                COALESCE(products.unit, "Pcs") as unit,
                COALESCE(products.sku, "-") as sku
            ')
            ->join('products', 'products.id = stock_opname_archives.product_id', 'left')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('stock_opname_archives.period_month', $month)
            ->where('stock_opname_archives.period_year', $year);

        // Filter kategori bisa di‑apply dengan JOIN ke products jika ada product_id
        if ($categoryFilter) {
            $builder->where('categories.id', $categoryFilter);
        }

        // Sort by field yang valid
        $validSorts = ['name', 'current_stock', 'stock_value', 'category_name'];
        if (in_array($sortBy, $validSorts)) {
            $builder->orderBy($sortBy, $sortOrder);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Ambil data stok dari database produk saat ini
     */
    private function getCurrentStockReport(?string $categoryFilter, ?string $stockStatus, string $sortBy, string $sortOrder): array
    {
        $builder = $this->modelProduk->select("
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

        return $builder->findAll();
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

        $builder = $this->modelMutasiStok->select('
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
            'categories'   => $this->modelKategori->getActiveCategories(),
            'products'     => $this->modelProduk->getProductsWithCategory(),
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

        $builder = $this->modelProduk->select('
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
            'categories' => $this->modelKategori->getActiveCategories(),
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
        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');

        if ($format === 'excel') {
            return $this->exportStockExcel($month, $year);
        } elseif ($format === 'pdf') {
            return $this->exportStockPDF($month, $year);
        }
        
        return redirect()->back()->with('error', 'Format export tidak valid');
    }

    /**
     * Export Movement Reports
     */
    public function exportMovements($format = null)
    {
        $format = $format ?: ($this->request->getGet('format') ?: 'excel');
        
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
        $movements = $this->modelMutasiStok->where('type', 'OUT')
            ->where('created_at >=', date('Y-m-d', strtotime("-{$period} days")))
            ->findAll();
        
        $totalSold    = array_sum(array_column($movements, 'quantity'));
        $avgInventory = $this->modelProduk->selectSum('current_stock')->first()['current_stock'] ?? 0;
        
        return [
            'turnover_rate' => $avgInventory > 0 ? round(($totalSold / $avgInventory) * (365 / $period), 2) : 0,
            'total_sold'    => $totalSold,
            'avg_inventory' => $avgInventory,
            'period_days'   => $period
        ];
    }

    private function calculateABCAnalysis()
    {
        $products = $this->modelProduk->select('products.*, (products.current_stock * products.price) as stock_value')
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
        $movements = $this->modelMutasiStok->select('product_id, SUM(quantity) as total_out, COUNT(*) as movement_count')
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
        $products = $this->modelProduk->select('products.*, categories.name as category_name')
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

    private function calculateDaysUntilStockout($productId) { return rand(5, 30); }

    /**
     * Export Stock Report to Excel - Matching official Stock Opname template
     */
    private function exportStockExcel($month = null, $year = null)
    {
        $month = $month ?: ($this->request->getGet('month') ?: date('m'));
        $year  = $year ?: ($this->request->getGet('year') ?: date('Y'));

        // Jika ada file stock opname arsip untuk bulan/tahun tersebut,
        // langsung kirim file itu (tidak generate dari data stok saat ini)
        $archivedFile = $this->findArchivedStockOpnameExcel($month, $year);
        if ($archivedFile && is_file($archivedFile)) {
            $filename = basename($archivedFile);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            readfile($archivedFile);
            exit;
        }

        // Jika tidak ada arsip, baru generate berdasarkan stok saat ini
        $products = $this->getStockReportData();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Opname');

        // Use aliases for cleaner code
        $alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::class;
        $border = \PhpOffice\PhpSpreadsheet\Style\Border::class;
        $fill = \PhpOffice\PhpSpreadsheet\Style\Fill::class;
        $numberFormat = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::class;

        // ── Column widths ──
        $sheet->getColumnDimension('A')->setWidth(6);   // No
        $sheet->getColumnDimension('B')->setWidth(40);  // Jenis Barang
        $sheet->getColumnDimension('C')->setWidth(12);  // Jumlah
        $sheet->getColumnDimension('D')->setWidth(18);  // Harga Satuan
        $sheet->getColumnDimension('E')->setWidth(18);  // Total Harga
        $sheet->getColumnDimension('F')->setWidth(10);  // Baik
        $sheet->getColumnDimension('G')->setWidth(12);  // Rusak/Usang

        // ── Header Section (Row 2-8) ──
        $sheet->setCellValue('A2', 'LAMPIRAN BERITA ACARA STOK OPNAME FISIK PERSEDIAAN');
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('F2', 'Lampiran 1');
        $sheet->mergeCells('F2:G2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue('A3', 'Nomor ');
        $sheet->setCellValue('B3', ': ........../UN64.7/LK/' . date('Y'));

        $sheet->setCellValue('A4', 'Tanggal ');
        $sheet->setCellValue('B4', ': ' . $this->formatTanggalIndonesia($year . '-' . $month . '-01'));

        $sheet->setCellValue('A5', 'Unit');
        $sheet->setCellValue('B5', ': Fakultas Ilmu Komputer');

        $sheet->setCellValue('A6', 'LAPORAN STOCK OPNAME ');
        $sheet->mergeCells('A6:G6');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A7', 'UNTUK PERIODE YANG BERAKHIR TANGGAL  ' . strtoupper($this->formatTanggalIndonesia($year . '-' . $month . '-01')));
        $sheet->mergeCells('A7:G7');
        $sheet->getStyle('A7')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A8', 'TAHUN ANGGARAN');
        $sheet->mergeCells('A8:G8');
        $sheet->getStyle('A8')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);

        // ── Table Headers (Row 10-11) ──
        // Row 10 - Main headers
        $sheet->setCellValue('A10', 'No');
        $sheet->mergeCells('A10:A11');
        $sheet->setCellValue('B10', 'Jenis Barang');
        $sheet->mergeCells('B10:B11');
        $sheet->setCellValue('C10', 'Hasil Stock Opname');
        $sheet->mergeCells('C10:E10');
        $sheet->setCellValue('F10', 'Kondisi Barang ');
        $sheet->mergeCells('F10:G10');

        // Row 11 - Sub-headers
        $sheet->setCellValue('C11', 'Jumlah');
        $sheet->setCellValue('D11', ' Harga Satuan');
        $sheet->setCellValue('E11', 'Total Harga');
        $sheet->setCellValue('F11', 'Baik');
        $sheet->setCellValue('G11', 'Rusak /Usang');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => $alignment::HORIZONTAL_CENTER,
                'vertical' => $alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => $border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A10:G11')->applyFromArray($headerStyle);

        // ── Data Rows ──
        $row = 12;
        $totalHargaSatuan = 0;
        $totalHarga = 0;

        foreach ($products as $index => $product) {
            $hargaSatuan = (float)($product['price'] ?? 0);
            $jumlah = (int)($product['current_stock'] ?? 0);
            $nilaiTotal = $jumlah * $hargaSatuan;
            $kondisi = $jumlah > 0 ? 'V' : '';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $product['name']);
            $sheet->setCellValue('C' . $row, $jumlah);
            $sheet->setCellValue('D' . $row, $hargaSatuan);
            $sheet->setCellValue('E' . $row, $nilaiTotal);
            $sheet->setCellValue('F' . $row, $kondisi);
            $sheet->setCellValue('G' . $row, '');

            // Alignment
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);

            // Number format
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $totalHargaSatuan += $hargaSatuan;
            $totalHarga += $nilaiTotal;
            $row++;
        }

        // ── Total Row ──
        $sheet->setCellValue('D' . $row, $totalHargaSatuan);
        $sheet->setCellValue('E' . $row, $totalHarga);
        $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('D' . $row . ':E' . $row)->getFont()->setBold(true);

        // Borders for data area
        $dataRange = 'A10:G' . $row;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle($border::BORDER_THIN);

        // ── Signature Section ──
        $sigRow = $row + 3;
        $sheet->setCellValue('B' . $sigRow, 'Mengetahui,');
        $sigRow++;
        $sheet->setCellValue('B' . $sigRow, 'A.n Dekan');
        $sheet->setCellValue('E' . $sigRow, 'Operator Persediaan');
        $sigRow++;
        $sheet->setCellValue('B' . $sigRow, 'Wakil Dekan Bidang Umum dan Keuangan');

        $sigRow += 4;
        $sheet->setCellValue('B' . $sigRow, 'Betha Nurina Sari, M.Kom.');
        $sheet->setCellValue('E' . $sigRow, 'M Rizki Fauzi S, S.Pd.');
        $sigRow++;
        $sheet->setCellValue('B' . $sigRow, 'NIP. 198910232018032001');

        // ── Generate file ──
        $bulan = strtoupper($this->getNamaBulan((int)$month));
        $filename = $bulan . ' - STOCK OPNAME PERSEDIAAN FASILKOM ' . $year . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export Stock Report to PDF - Matching official Stock Opname template
     */
    private function exportStockPDF($month = null, $year = null)
    {
        $products = $this->getStockReportData();

        $month = $month ?: ($this->request->getGet('month') ?: date('m'));
        $year  = $year ?: ($this->request->getGet('year') ?: date('Y'));

        $tanggal = $this->formatTanggalIndonesia($year . '-' . $month . '-01');
        $periodeUpper = strtoupper($tanggal);

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 9pt; margin: 20px; }
                .header { margin-bottom: 10px; }
                .header-row { margin-bottom: 2px; }
                .header-row span.label { display: inline-block; width: 80px; }
                .title { text-align: center; font-weight: bold; font-size: 11pt; margin: 10px 0 5px; }
                .subtitle { text-align: center; font-size: 9pt; margin-bottom: 15px; }
                table { width: 100%; border-collapse: collapse; }
                th { background-color: #f2f2f2; border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 8pt; font-weight: bold; }
                td { border: 1px solid #000; padding: 4px; font-size: 8pt; }
                td.center { text-align: center; }
                td.right { text-align: right; }
                tr.total td { font-weight: bold; border-top: 2px solid #000; }
                .signature { margin-top: 30px; }
                .signature table { border: none; }
                .signature td { border: none; padding: 3px; font-size: 9pt; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="header-row"><strong>LAMPIRAN BERITA ACARA STOK OPNAME FISIK PERSEDIAAN</strong></div>
                <div class="header-row"><span class="label">Nomor</span>: ........../UN64.7/LK/' . date('Y') . '</div>
                <div class="header-row"><span class="label">Tanggal</span>: ' . $tanggal . '</div>
                <div class="header-row"><span class="label">Unit</span>: Fakultas Ilmu Komputer</div>
            </div>

            <div class="title">LAPORAN STOCK OPNAME</div>
            <div class="subtitle">UNTUK PERIODE YANG BERAKHIR TANGGAL ' . $periodeUpper . '</div>
            <div class="subtitle">TAHUN ANGGARAN ' . $year . '</div>

            <table>
                <thead>
                    <tr>
                        <th rowspan="2" width="5%">No</th>
                        <th rowspan="2" width="35%">Jenis Barang</th>
                        <th colspan="3">Hasil Stock Opname</th>
                        <th colspan="2">Kondisi Barang</th>
                    </tr>
                    <tr>
                        <th width="8%">Jumlah</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Total Harga</th>
                        <th width="8%">Baik</th>
                        <th width="10%">Rusak /Usang</th>
                    </tr>
                </thead>
                <tbody>';

        $totalHargaSatuan = 0;
        $totalHarga = 0;

        foreach ($products as $index => $product) {
            $hargaSatuan = (float)($product['price'] ?? 0);
            $jumlah = (int)($product['current_stock'] ?? 0);
            $nilaiTotal = $jumlah * $hargaSatuan;
            $kondisi = $jumlah > 0 ? 'V' : '';

            $html .= '<tr>
                <td class="center">' . ($index + 1) . '</td>
                <td>' . esc($product['name']) . '</td>
                <td class="center">' . number_format($jumlah) . '</td>
                <td class="right">' . number_format($hargaSatuan) . '</td>
                <td class="right">' . number_format($nilaiTotal) . '</td>
                <td class="center">' . $kondisi . '</td>
                <td class="center"></td>
            </tr>';

            $totalHargaSatuan += $hargaSatuan;
            $totalHarga += $nilaiTotal;
        }

        $html .= '<tr class="total">
                <td colspan="3"></td>
                <td class="right">' . number_format($totalHargaSatuan) . '</td>
                <td class="right">' . number_format($totalHarga) . '</td>
                <td colspan="2"></td>
            </tr>';

        $html .= '</tbody></table>

            <div class="signature">
                <table width="100%">
                    <tr><td width="50%">Mengetahui,</td><td></td></tr>
                    <tr><td>A.n Dekan</td><td>Operator Persediaan</td></tr>
                    <tr><td>Wakil Dekan Bidang Umum dan Keuangan</td><td></td></tr>
                    <tr><td><br><br><br><br></td><td></td></tr>
                    <tr><td><u>Betha Nurina Sari, M.Kom.</u></td><td><u>M Rizki Fauzi S, S.Pd.</u></td></tr>
                    <tr><td>NIP. 198910232018032001</td><td></td></tr>
                </table>
            </div>
        </body></html>';

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $bulan = strtoupper($this->getNamaBulan((int)$month));
        $filename = $bulan . '_STOCK_OPNAME_' . $year . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    /**
     * Get stock report data for export
     */
    /**
     * Get stock report data for export - checks if archived or current
     */
    private function getStockReportData(): array
    {
        $month = (int) ($this->request->getGet('month') ?: date('m'));
        $year  = (int) ($this->request->getGet('year') ?: date('Y'));
        $categoryFilter = $this->request->getGet('category');
        $stockStatus    = $this->request->getGet('stock_status');

        $isArchived = $this->isArchivedPeriod($month, $year);
        
        if ($isArchived) {
            return $this->getArchivedStockReport($month, $year, $categoryFilter, 'name', 'ASC');
        } else {
            return $this->getCurrentStockReport($categoryFilter, $stockStatus, 'name', 'ASC');
        }
    }

    /**
     * Cari file Excel Stock Opname arsip di folder public/laporan bulanan
     * berdasarkan bulan & tahun yang diminta.
     */
    private function findArchivedStockOpnameExcel($month, $year): ?string
    {
        $dir = FCPATH . 'laporan bulanan' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            return null;
        }

        $files = glob($dir . '*.xlsx');
        if (!$files) {
            return null;
        }

        $targetMonth = (int) $month;
        foreach ($files as $file) {
            $basename = basename($file);

            // Contoh nama file:
            // "MAR 2025 - STOCK OPNAME PERSEDIAAN FASILKOM 2025.xlsx"
            if (!preg_match('/^([A-Z]+)\s+' . preg_quote((string) $year, '/') . '\s*-\s*STOCK OPNAME PERSEDIAAN FASILKOM\s+' . preg_quote((string) $year, '/') . '\.xlsx$/i', $basename, $m)) {
                continue;
            }

            $token = strtoupper($m[1]);
            $fileMonth = $this->monthTokenToNumber($token);
            if ($fileMonth === $targetMonth) {
                return $file;
            }
        }

        return null;
    }

    /**
     * Konversi token bulan (JAN, FEB, MAR, JUNI, SEPT, DES, dll) menjadi nomor bulan.
     */
    private function monthTokenToNumber(string $token): ?int
    {
        $token = strtoupper(trim($token));

        $map = [
            1  => ['JAN', 'JANUARI'],
            2  => ['FEB', 'FEBRUARI'],
            3  => ['MAR', 'MARET'],
            4  => ['APR', 'APRIL'],
            5  => ['MEI'],
            6  => ['JUN', 'JUNI'],
            7  => ['JUL', 'JULI'],
            8  => ['AGT', 'AGUST', 'AGUSTUS'],
            9  => ['SEP', 'SEPT', 'SEPTEMBER'],
            10 => ['OKT', 'OKTOBER'],
            11 => ['NOV', 'NOVEMBER'],
            12 => ['DES', 'DESEMBER'],
        ];

        foreach ($map as $num => $tokens) {
            if (in_array($token, $tokens, true)) {
                return $num;
            }
        }

        return null;
    }

    /**
     * Format tanggal dalam bahasa Indonesia
     */
    private function formatTanggalIndonesia($date)
    {
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $timestamp = strtotime($date);
        $hari = date('d', $timestamp);
        $bulan = $bulanList[(int)date('n', $timestamp)];
        $tahun = date('Y', $timestamp);

        return "{$hari} {$bulan} {$tahun}";
    }

    /**
     * Get nama bulan Indonesia
     */
    private function getNamaBulan($num)
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $bulan[(int)$num] ?? '';
    }

    /**
     * Export Movements to Excel
     */
    /**
     * Export Movements to Excel - Professional University Template
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
        $builder = $this->modelMutasiStok->select('
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

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mutasi Stok');

        // Aliases
        $alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::class;
        $border = \PhpOffice\PhpSpreadsheet\Style\Border::class;

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(25);

        // Header Section
        $sheet->setCellValue('A2', 'LAPORAN MUTASI / PERGERAKAN STOK PERSEDIAAN');
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('G2', 'Lampiran 2');
        $sheet->mergeCells('G2:H2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue('A3', 'Nomor ');
        $sheet->setCellValue('B3', ': ........../UN64.7/LK/' . date('Y'));
        $sheet->setCellValue('A4', 'Unit');
        $sheet->setCellValue('B4', ': Fakultas Ilmu Komputer');

        $sheet->setCellValue('A6', 'LAPORAN MUTASI BARANG PERSEDIAAN');
        $sheet->mergeCells('A6:H6');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A7', 'PERIODE: ' . strtoupper($this->formatTanggalIndonesia($startDate)) . ' S/D ' . strtoupper($this->formatTanggalIndonesia($endDate)));
        $sheet->mergeCells('A7:H7');
        $sheet->getStyle('A7')->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);

        // Table Headers
        $headers = ['No', 'Tanggal', 'Nama Barang', 'SKU', 'Kategori', 'Tipe', 'Jumlah', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '10', $header);
            $col++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => $alignment::HORIZONTAL_CENTER,
                'vertical' => $alignment::VERTICAL_CENTER,
            ],
            'borders' => ['allBorders' => ['borderStyle' => $border::BORDER_THIN]],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'],
            ],
        ];
        $sheet->getStyle('A10:H10')->applyFromArray($headerStyle);

        // Data Rows
        $row = 11;
        foreach ($movements as $index => $m) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($m['created_at'])));
            $sheet->setCellValue('C' . $row, $m['product_name']);
            $sheet->setCellValue('D' . $row, $m['product_sku']);
            $sheet->setCellValue('E' . $row, $m['category_name']);
            $sheet->setCellValue('F' . $row, $m['type']);
            $sheet->setCellValue('G' . $row, $m['quantity']);
            $sheet->setCellValue('H' . $row, $m['notes'] ?? '-');

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal($alignment::HORIZONTAL_CENTER);
            $row++;
        }

        // Borders
        $sheet->getStyle('A10:H' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle($border::BORDER_THIN);

        // Signature Section
        $sigRow = $row + 3;
        $sheet->setCellValue('B' . $sigRow, 'Mengetahui,');
        $sigRow++;
        $sheet->setCellValue('B' . $sigRow, 'A.n Dekan');
        $sheet->setCellValue('F' . $sigRow, 'Operator Persediaan');
        $sigRow++;
        $sheet->setCellValue('B' . $sigRow, 'Wakil Dekan Bidang Umum dan Keuangan');

        $sigRow += 4;
        $sheet->setCellValue('B' . $sigRow, 'Betha Nurina Sari, M.Kom.');
        $sheet->setCellValue('F' . $sigRow, 'M Rizki Fauzi S, S.Pd.');
        $sigRow++;
        $sheet->setCellValue('B' . $sigRow, 'NIP. 198910232018032001');

        $filename = 'MUTASI_STOK_' . date('Ymd') . '.xlsx';
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
    /**
     * Export Movements to PDF - Professional University Template
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
        $builder = $this->modelMutasiStok->select('
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

        $tanggal = $this->formatTanggalIndonesia(date('Y-m-d'));
        
        // Generate HTML
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; font-size: 8pt; margin: 20px; }
                .header { margin-bottom: 10px; }
                .header-row { margin-bottom: 2px; }
                .header-row span.label { display: inline-block; width: 80px; }
                .title { text-align: center; font-weight: bold; font-size: 11pt; margin: 15px 0 5px; }
                .subtitle { text-align: center; font-size: 9pt; margin-bottom: 15px; }
                table { width: 100%; border-collapse: collapse; }
                th { background-color: #f2f2f2; border: 1px solid #000; padding: 6px 4px; text-align: center; font-weight: bold; }
                td { border: 1px solid #000; padding: 4px; }
                td.center { text-align: center; }
                .signature { margin-top: 30px; line-height: 1.4; }
                .signature table { border: none; }
                .signature td { border: none; padding: 0; font-size: 9pt; }
                .badge { padding: 2px 5px; border-radius: 3px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="header-row"><strong>LAPORAN MUTASI / PERGERAKAN STOK PERSEDIAAN</strong></div>
                <div class="header-row"><span class="label">Nomor</span>: ........../UN64.7/LK/' . date('Y') . '</div>
                <div class="header-row"><span class="label">Tanggal</span>: ' . $tanggal . '</div>
                <div class="header-row"><span class="label">Unit</span>: Fakultas Ilmu Komputer</div>
            </div>

            <div class="title">LAPORAN MUTASI BARANG PERSEDIAAN</div>
            <div class="subtitle">PERIODE ' . strtoupper($this->formatTanggalIndonesia($startDate)) . ' S/D ' . strtoupper($this->formatTanggalIndonesia($endDate)) . '</div>

            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="25%">Nama Barang</th>
                        <th width="10%">SKU</th>
                        <th width="15%">Kategori</th>
                        <th width="8%">Tipe</th>
                        <th width="8%">Jumlah</th>
                        <th width="14%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($movements as $index => $m) {
            $html .= '<tr>
                <td class="center">' . ($index + 1) . '</td>
                <td class="center">' . date('d/m/Y H:i', strtotime($m['created_at'])) . '</td>
                <td>' . esc($m['product_name']) . '</td>
                <td class="center">' . esc($m['product_sku']) . '</td>
                <td>' . esc($m['category_name']) . '</td>
                <td class="center">' . $m['type'] . '</td>
                <td class="center">' . number_format($m['quantity']) . '</td>
                <td>' . esc($m['notes'] ?? '-') . '</td>
            </tr>';
        }

        $html .= '</tbody></table>

            <div class="signature">
                <table width="100%">
                    <tr><td width="60%">Mengetahui,</td><td></td></tr>
                    <tr><td>A.n Dekan</td><td>Operator Persediaan</td></tr>
                    <tr><td>Wakil Dekan Bidang Umum dan Keuangan</td><td></td></tr>
                    <tr><td><br><br><br><br></td><td></td></tr>
                    <tr><td><u>Betha Nurina Sari, M.Kom.</u></td><td><u>M Rizki Fauzi S, S.Pd.</u></td></tr>
                    <tr><td>NIP. 198910232018032001</td><td></td></tr>
                </table>
            </div>
        </body></html>';

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'MUTASI_STOK_' . date('YmdHis') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
