<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\MutasiStokModel;
use App\Models\NotifikasiModel;
use App\Controllers\BaseController;
use Exception;

class StokController extends BaseController
{
    protected ProdukModel $modelProduk;
    protected KategoriModel $modelKategori;
    protected MutasiStokModel $modelMutasiStok;
    protected NotifikasiModel $modelNotifikasi;

    public function __construct()
    {
        $this->modelProduk        = new ProdukModel();
        $this->modelKategori       = new KategoriModel();
        $this->modelMutasiStok  = new MutasiStokModel();
        $this->modelNotifikasi   = new NotifikasiModel();
    }

    /**
     * Stock In page
     */
    public function stockIn()
    {
        $this->setPageData('Barang Masuk', 'Input stok barang masuk ke gudang / inventory');

        $products   = $this->modelProduk->getProductsWithCategory();
        $categories = $this->modelKategori->getActiveCategories();

        $recentHistory = $this->modelMutasiStok->select('stock_movements.*, products.name as product_name, products.sku as product_sku')
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

                $this->modelMutasiStok->createMovement([
                    'product_id'   => $m['product_id'],
                    'type'         => 'IN',
                    'quantity'     => $m['quantity'],
                    'reference_no' => $reference,
                    'notes'        => $m['notes'] ?: $globalNotes,
                    'created_by'   => session()->get('userId') ?: null
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
                    $produk = $this->modelProduk->find($m['product_id']);
                    if ($produk) {
                        $this->modelNotifikasi->createStockInNotification(
                            (string) $produk['name'],
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

        $products   = $this->modelProduk->where('current_stock >', 0)->orderBy('name', 'ASC')->findAll();
        $categories = $this->modelKategori->getActiveCategories();

        $recentHistory = $this->modelMutasiStok->select('stock_movements.*, products.name as product_name, products.sku as product_sku')
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

                $this->modelMutasiStok->createMovement([
                    'product_id'   => $m['product_id'],
                    'type'         => 'OUT',
                    'quantity'     => $m['quantity'],
                    'reference_no' => $reference,
                    'notes'        => $m['notes'] ?: $globalNotes,
                    'created_by'   => session()->get('userId') ?: null
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
                    $produk = $this->modelProduk->find($m['product_id']);
                    if (!$produk) continue;

                    $this->modelNotifikasi->createStockOutNotification(
                        (string) $produk['name'],
                        (int)$m['quantity'],
                        $reference
                    );

                    // Cek apakah stok habis atau rendah
                    if ((int)$produk['current_stock'] <= 0) {
                        $this->modelNotifikasi->createOutOfStockNotification($produk);
                    } elseif ((int)$produk['current_stock'] <= (int)($produk['min_stock'] ?? 0)) {
                        $this->modelNotifikasi->createLowStockNotification($produk);
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
        $builder = $this->modelMutasiStok
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
        $statsBuilder = $this->modelMutasiStok->where('type', $currentType);
        $totalTransactions = $statsBuilder->countAllResults(false);
        $totalQuantity     = (int) $this->modelMutasiStok
            ->selectSum('quantity')
            ->where('type', $currentType)
            ->first()['quantity'];

        $products   = $this->modelProduk->orderBy('name', 'ASC')->findAll();
        $categories = $this->modelKategori->getActiveCategories();

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

        $movements = $this->modelMutasiStok->getMovementsWithProduct(0, $filters);

        $data = [
            'daftarMutasi'   => $movements,
            'daftarProduk'   => $this->modelProduk->orderBy('name', 'ASC')->findAll(),
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
            'daftarProduk' => $this->modelProduk->getProductsWithCategory()
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

                $this->modelMutasiStok->createMovement([
                    'product_id'   => $p['product_id'],
                    'type'         => 'ADJUSTMENT',
                    'quantity'     => $p['new_stock'],
                    'reference_no' => 'ADJ-' . time(),
                    'notes'        => $p['notes'] ?: $globalNotes,
                    'created_by'   => session()->get('userId') ?: null
                ]);
                $successCount++;
            }

            $db->transComplete();
            if ($db->transStatus() === false) throw new Exception('Gagal menyimpan penyesuaian.');

            // Cek stok rendah/habis setelah penyesuaian
            try {
                foreach ($adjustments as $p) {
                    if (!isset($p['product_id'])) continue;
                    $produk = $this->modelProduk->find($p['product_id']);
                    if (!$produk) continue;

                    if ((int)$produk['current_stock'] <= 0) {
                        $this->modelNotifikasi->createOutOfStockNotification($produk);
                    } elseif ((int)$produk['current_stock'] <= (int)($produk['min_stock'] ?? 0)) {
                        $this->modelNotifikasi->createLowStockNotification($produk);
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

        $lowStockProducts = $this->modelProduk->getLowStockProducts();
        $outOfStockProducts = $this->modelProduk->where('current_stock', 0)->where('is_active', true)->findAll();
        $totalActive = $this->modelProduk->where('is_active', true)->countAllResults(false);

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
     * Export Stock History
     */
    public function exportHistory($format = 'excel')
    {
        $filters = [
            'product_id' => $this->request->getGet('product'),
            'type'       => $this->request->getGet('type'),
            'start_date' => $this->request->getGet('start_date'),
            'end_date'   => $this->request->getGet('end_date')
        ];

        // Ambil semua data tanpa limit (0)
        $movements = $this->modelMutasiStok->getMovementsWithProduct(0, $filters);

        if ($format === 'excel') {
            return $this->exportHistoryExcel($movements);
        } else {
            return $this->exportHistoryPDF($movements);
        }
    }

    private function exportHistoryExcel(array $movements)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'RIWAYAT MUTASI STOK INVENTORY');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Dicetak pada: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Column headers
        $headers = ['No', 'Tanggal & Waktu', 'Produk', 'SKU', 'Tipe', 'Jumlah', 'Stok Sisa', 'Referensi/Ket'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $sheet->getStyle($col . '4')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E9ECEF');
            $col++;
        }

        // Data
        $row = 5;
        foreach ($movements as $index => $mut) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($mut['created_at'])));
            $sheet->setCellValue('C' . $row, $mut['product_name']);
            $sheet->setCellValue('D' . $row, $mut['product_sku']);
            $sheet->setCellValue('E' . $row, $mut['type']);
            $sheet->setCellValue('F' . $row, ($mut['type'] == 'IN' ? '+' : ($mut['type'] == 'OUT' ? '-' : '±')) . $mut['quantity']);
            $sheet->setCellValue('G' . $row, $mut['current_stock']);
            $sheet->setCellValue('H' . $row, ($mut['reference_no'] ? "Ref: " . $mut['reference_no'] . " | " : "") . ($mut['notes'] ?: '-'));
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Riwayat_Stok_' . date('YmdHis') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function exportHistoryPDF(array $movements)
    {
        $html = view('stock/history_pdf', ['movements' => $movements]);

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'Riwayat_Stok_' . date('YmdHis') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
