<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\StockMovementModel;
use App\Models\NotificationModel;
use App\Controllers\BaseController;
use Exception;

class StockController extends BaseController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;
    protected StockMovementModel $stockMovementModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->productModel        = new ProductModel();
        $this->categoryModel       = new CategoryModel();
        $this->stockMovementModel  = new StockMovementModel();
        $this->notificationModel   = new NotificationModel();
    }

    /**
     * Stock In page
     */
    public function stockIn()
    {
        $this->setPageData('Barang Masuk', 'Input stok barang masuk ke gudang / inventory');

        $products   = $this->productModel->getProductsWithCategory();
        $categories = $this->categoryModel->getActiveCategories();

        $recentHistory = $this->stockMovementModel->select('stock_movements.*, products.name as product_name, products.sku as product_sku')
            ->join('products', 'products.id = stock_movements.product_id')
            ->where('stock_movements.type', 'IN')
            ->orderBy('stock_movements.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'daftarProduk'    => $products,
            'daftarKategori'  => $categories,
            'riwayatTerakhir' => $recentHistory,
            'produkTerpilih'  => $this->request->getGet('product')
        ];

        return $this->render('stock/in', $data);
    }

    /**
     * Process Stock In
     */
    public function storeStockIn()
    {
        $rules = [
            'movements' => 'required',
            'movements.*.product_id' => 'required',
            'movements.*.quantity'   => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $movementData = $this->request->getPost('movements');
        $globalNotes  = $this->request->getPost('global_notes');
        $reference    = $this->request->getPost('reference_no') ?: 'IN-' . time();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $successCount = 0;

            foreach ($movementData as $m) {
                if (empty($m['product_id']) || empty($m['quantity'])) continue;

                $this->stockMovementModel->createMovement([
                    'product_id'   => $m['product_id'],
                    'type'         => 'IN',
                    'quantity'     => $m['quantity'],
                    'reference_no' => $reference,
                    'notes'        => $m['notes'] ?: $globalNotes,
                    'created_by'   => session()->get('username') ?: 'System'
                ]);
                $successCount++;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Gagal menyimpan mutasi stok.');
            }

            // Kirim notifikasi barang masuk
            try {
                foreach ($movementData as $m) {
                    if (empty($m['product_id'])) continue;
                    /** @var array $product */
                    $product = $this->productModel->find($m['product_id']);
                    if ($product) {
                        $this->notificationModel->createStockInNotification(
                            (string) $product['name'],
                            (int)$m['quantity'],
                            $reference
                        );
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi stok masuk: ' . $e->getMessage());
            }

            return redirect()->to('/stock/in')->with('success', "Berhasil memproses $successCount item barang masuk.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Stock Out page
     */
    public function stockOut()
    {
        $this->setPageData('Barang Keluar', 'Input pengeluaran stok barang dari gudang');

        $products   = $this->productModel->where('current_stock >', 0)->orderBy('name', 'ASC')->findAll();
        $categories = $this->categoryModel->getActiveCategories();

        $recentHistory = $this->stockMovementModel->select('stock_movements.*, products.name as product_name, products.sku as product_sku')
            ->join('products', 'products.id = stock_movements.product_id')
            ->where('stock_movements.type', 'OUT')
            ->orderBy('stock_movements.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'daftarProduk'    => $products,
            'daftarKategori'  => $categories,
            'riwayatTerakhir' => $recentHistory,
            'produkTerpilih'  => $this->request->getGet('product')
        ];

        return $this->render('stock/out', $data);
    }

    /**
     * Process Stock Out
     */
    public function storeStockOut()
    {
        $rules = [
            'movements' => 'required',
            'movements.*.product_id' => 'required',
            'movements.*.quantity'   => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $movementData = $this->request->getPost('movements');
        $globalNotes  = $this->request->getPost('global_notes');
        $reference    = $this->request->getPost('reference_no') ?: 'OUT-' . time();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $successCount = 0;

            foreach ($movementData as $m) {
                if (empty($m['product_id']) || empty($m['quantity'])) continue;

                $this->stockMovementModel->createMovement([
                    'product_id'   => $m['product_id'],
                    'type'         => 'OUT',
                    'quantity'     => $m['quantity'],
                    'reference_no' => $reference,
                    'notes'        => $m['notes'] ?: $globalNotes,
                    'created_by'   => session()->get('username') ?: 'System'
                ]);
                $successCount++;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Gagal menyimpan mutasi keluar.');
            }

            // Kirim notifikasi barang keluar + cek stok rendah/habis
            try {
                foreach ($movementData as $m) {
                    if (empty($m['product_id'])) continue;
                    /** @var array $product */
                    $product = $this->productModel->find($m['product_id']);
                    if (!$product) continue;

                    $this->notificationModel->createStockOutNotification(
                        (string) $product['name'],
                        (int)$m['quantity'],
                        $reference
                    );

                    // Cek apakah stok habis atau rendah
                    if ((int)$product['current_stock'] <= 0) {
                        $this->notificationModel->createOutOfStockNotification($product);
                    } elseif ((int)$product['current_stock'] <= (int)($product['min_stock'] ?? 0)) {
                        $this->notificationModel->createLowStockNotification($product);
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi stok keluar: ' . $e->getMessage());
            }

            return redirect()->to('/stock/out')->with('success', "Berhasil memproses $successCount item barang keluar.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Stock Movements page (unified IN/OUT view)
     */
    public function movements()
    {
        $currentType = $this->request->getGet('type') ?: 'IN';
        if (!in_array($currentType, ['IN', 'OUT'], true)) {
            $currentType = 'IN';
        }

        $typeLabel = $currentType === 'IN' ? 'Mode Barang Masuk' : 'Mode Barang Keluar';
        $this->setPageData(
            $currentType === 'IN' ? 'Barang Masuk' : 'Barang Keluar',
            'Kelola pergerakan stok barang ' . ($currentType === 'IN' ? 'masuk' : 'keluar')
        );

        $filters = [
            'product'    => $this->request->getGet('product'),
            'category'   => $this->request->getGet('category'),
            'start_date' => $this->request->getGet('start_date'),
            'end_date'   => $this->request->getGet('end_date'),
        ];

        // Build query for recent movements
        $builder = $this->stockMovementModel
            ->select('stock_movements.*, products.name as product_name, products.sku as product_sku, categories.name as category_name')
            ->join('products', 'products.id = stock_movements.product_id')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('stock_movements.type', $currentType);

        if (!empty($filters['product'])) {
            $builder->where('stock_movements.product_id', $filters['product']);
        }
        if (!empty($filters['category'])) {
            $builder->where('products.category_id', $filters['category']);
        }
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(stock_movements.created_at) >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(stock_movements.created_at) <=', $filters['end_date']);
        }

        $recentMovements = $builder->orderBy('stock_movements.created_at', 'DESC')
            ->limit(20)
            ->findAll();

        // Stats
        $statsBuilder = $this->stockMovementModel->where('type', $currentType);
        $totalTransactions = $statsBuilder->countAllResults(false);
        $totalQuantity     = (int) $this->stockMovementModel
            ->selectSum('quantity')
            ->where('type', $currentType)
            ->first()['quantity'];

        $products   = $this->productModel->orderBy('name', 'ASC')->findAll();
        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'current_type'     => $currentType,
            'stats'            => [
                'total_transactions' => $totalTransactions,
                'total_quantity'     => $totalQuantity,
                'type_label'         => $typeLabel,
            ],
            'filters'          => $filters,
            'recent_movements' => $recentMovements,
            'products'         => $products,
            'categories'       => $categories,
        ];

        return $this->render('stock/movements', $data);
    }

    /**
     * Stock History page
     */
    public function history()
    {
        $this->setPageData('Riwayat Stok', 'History pergerakan keluar dan masuk barang');

        $filters = [
            'product_id' => $this->request->getGet('product'),
            'type'       => $this->request->getGet('type'),
            'start_date' => $this->request->getGet('start_date'),
            'end_date'   => $this->request->getGet('end_date')
        ];

        $movements = $this->stockMovementModel->getMovementsWithProduct(0, $filters);

        $data = [
            'daftarMutasi'   => $movements,
            'daftarProduk'   => $this->productModel->orderBy('name', 'ASC')->findAll(),
            'filterProduk'   => $filters['product_id'],
            'filterTipe'     => $filters['type'],
            'tglMulai'       => $filters['start_date'],
            'tglSelesai'     => $filters['end_date']
        ];

        return $this->render('stock/history', $data);
    }

    /**
     * Stock Adjustment page
     */
    public function adjustment()
    {
        $this->setPageData('Penyesuaian Stok', 'Koreksi stok barang sesuai kondisi fisik gudang');

        $data = [
            'daftarProduk' => $this->productModel->getProductsWithCategory()
        ];

        return $this->render('stock/adjustment', $data);
    }

    /**
     * Save Stock Adjustment
     */
    public function storeAdjustment()
    {
        $adjustments = $this->request->getPost('adjustments');
        $globalNotes = $this->request->getPost('global_notes') ?: 'Penyesuaian stok manual';

        if (empty($adjustments)) {
            return redirect()->back()->with('error', 'Tidak ada data penyesuaian yang dikirim.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $successCount = 0;
            foreach ($adjustments as $p) {
                if (!isset($p['product_id']) || !isset($p['new_stock'])) continue;

                $this->stockMovementModel->createMovement([
                    'product_id'   => $p['product_id'],
                    'type'         => 'ADJUSTMENT',
                    'quantity'     => $p['new_stock'],
                    'reference_no' => 'ADJ-' . time(),
                    'notes'        => $p['notes'] ?: $globalNotes,
                    'created_by'   => session()->get('name') ?: 'System'
                ]);
                $successCount++;
            }

            $db->transComplete();
            if ($db->transStatus() === false) throw new Exception('Gagal menyimpan penyesuaian.');

            // Cek stok rendah/habis setelah penyesuaian
            try {
                foreach ($adjustments as $p) {
                    if (!isset($p['product_id'])) continue;
                    $product = $this->productModel->find($p['product_id']);
                    if (!$product) continue;

                    if ((int)$product['current_stock'] <= 0) {
                        $this->notificationModel->createOutOfStockNotification($product);
                    } elseif ((int)$product['current_stock'] <= (int)($product['min_stock'] ?? 0)) {
                        $this->notificationModel->createLowStockNotification($product);
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi penyesuaian: ' . $e->getMessage());
            }

            return redirect()->to('/stock/adjustment')->with('success', "Berhasil menyesuaikan $successCount item stok.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Stock Alerts page
     */
    public function alerts()
    {
        $this->setPageData('Peringatan Stok', 'Daftar barang yang stoknya menipis atau habis');

        $lowStockProducts = $this->productModel->getLowStockProducts();
        $outOfStockProducts = $this->productModel->where('current_stock', 0)->where('is_active', true)->findAll();
        $totalActive = $this->productModel->where('is_active', true)->countAllResults(false);

        $data = [
            'stokRendah' => $lowStockProducts,
            'stokHabis'  => $outOfStockProducts,
            'stats' => [
                'out_of_stock'  => count($outOfStockProducts),
                'low_stock'     => count($lowStockProducts),
                'normal_stock'  => $totalActive - count($outOfStockProducts) - count($lowStockProducts),
            ],
        ];

        return $this->render('stock/alerts', $data);
    }

    /**
     * AJAX: Get Stock Info
     */
    public function getProductStock($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return $this->jsonResponse(['status' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        return $this->jsonResponse([
            'status' => true,
            'produk' => $product
        ]);
    }
}
