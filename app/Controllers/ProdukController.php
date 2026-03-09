<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\MutasiStokModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class ProdukController extends BaseController
{
    protected ProdukModel $modelProduk;
    protected KategoriModel $modelKategori;
    protected MutasiStokModel $modelMutasiStok;

    public function __construct()
    {
        $this->modelProduk = new ProdukModel();
        $this->modelKategori = new KategoriModel();
        $this->modelMutasiStok = new MutasiStokModel();
    }

    /**
     * Show product list
     */
    public function index()
    {
        $this->setPageData('Daftar Produk', 'Manajemen stok dan inventaris barang');

        $search      = $this->request->getGet('search');
        $category    = $this->request->getGet('category');
        $stockStatus = $this->request->getGet('stock_status');

        $products = $this->modelProduk->getFilteredProducts([
            'search'       => $search,
            'category'     => $category,
            'stock_status' => $stockStatus
        ]);

        $categories = $this->modelKategori->getActiveCategories();

        $data = [
            'daftarProduk'   => $products,
            'daftarKategori' => $categories,
            'filterCari'     => $search,
            'filterKategori' => $category,
            'filterStok'     => $stockStatus,
            'totalItem'      => count($products)
        ];

        return $this->render('products/index', $data);
    }

    /**
     * Create product form
     */
    public function create()
    {
        $this->setPageData('Tambah Produk', 'Input data barang baru ke sistem');

        $data = [
            'produk' => [
                'name'          => '',
                'sku'           => '',
                'category_id'   => '',
                'description'   => '',
                'price'         => 0,
                'cost_price'    => 0,
                'min_stock'     => 5,
                'current_stock' => 0,
                'unit'          => 'Pcs',
                'is_active'     => 1
            ],
            'daftarKategori' => $this->modelKategori->getActiveCategories(),
        ];

        return $this->render('products/create', $data);
    }

    /**
     * Store new product
     */
    public function store()
    {
        $rules = [
            'name'          => 'required|min_length[3]|max_length[255]',
            'sku'           => 'required|min_length[3]|max_length[50]|is_unique[products.sku]',
            'category_id'   => 'required|integer',
            'price'         => 'required|decimal',
            'min_stock'     => 'required|integer',
            'current_stock' => 'integer',
            'unit'          => 'required|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'name'          => $this->request->getPost('name'),
            'sku'           => strtoupper((string)$this->request->getPost('sku')),
            'category_id'   => $this->request->getPost('category_id'),
            'description'   => $this->request->getPost('description'),
            'price'         => (float) $this->request->getPost('price'),
            'cost_price'    => (float) $this->request->getPost('cost_price') ?: 0,
            'min_stock'     => (int) $this->request->getPost('min_stock'),
            'current_stock' => (int) $this->request->getPost('current_stock') ?: 0,
            'unit'          => $this->request->getPost('unit'),
            'is_active'     => 1
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $productId = $this->modelProduk->insert($payload);

            $this->modelMutasiStok->insert([
                'product_id'   => $productId,
                'type'         => 'IN',
                'quantity'     => (int) $this->request->getPost('current_stock') ?: 0,
                'notes'        => 'Stok awal produk baru',
                'reference_no' => 'INIT-' . time(),
                'created_by'   => session()->get('userId') ?: null
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Gagal menyimpan ke database');
            }

            return redirect()->to('/products')->with('success', 'Produk berhasil ditambahkan.');
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Product details
     */
    public function show($id)
    {
        $product = $this->modelProduk->getProductWithCategory((int)$id);

        if (!$product) {
            return redirect()->to('/products')->with('error', 'Produk tidak ditemukan.');
        }

        $this->setPageData('Detail Produk', $product['name']);

        $stockHistory = $this->modelMutasiStok->where('product_id', $id)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $stats = [
            'total_masuk'  => $this->modelMutasiStok->where('product_id', $id)->where('type', 'IN')->selectSum('quantity', 'total')->first()['total'] ?? 0,
            'total_keluar' => $this->modelMutasiStok->where('product_id', $id)->where('type', 'OUT')->selectSum('quantity', 'total')->first()['total'] ?? 0,
        ];

        $data = [
            'produk'      => $product,
            'riwayatStok' => $stockHistory,
            'statistik'   => $stats
        ];

        return $this->render('products/show', $data);
    }

    /**
     * Edit product form
     */
    public function edit($id)
    {
        $product = $this->modelProduk->find($id);

        if (!$product) {
            return redirect()->to('/products')->with('error', 'Produk tidak ditemukan.');
        }

        $this->setPageData('Edit Produk', $product['name']);

        $data = [
            'produk'         => $product,
            'daftarKategori' => $this->modelKategori->getActiveCategories(),
        ];

        return $this->render('products/edit', $data);
    }

    /**
     * Update product
     */
    public function update($id)
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'sku'         => "required|min_length[3]|max_length[50]|is_unique[products.sku,id,$id]",
            'category_id' => 'required|integer',
            'price'       => 'required|decimal',
            'min_stock'   => 'required|integer',
            'unit'        => 'required|max_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'name'        => $this->request->getPost('name'),
            'sku'         => strtoupper((string)$this->request->getPost('sku')),
            'category_id' => $this->request->getPost('category_id'),
            'description' => $this->request->getPost('description'),
            'price'       => (float) $this->request->getPost('price'),
            'cost_price'  => (float) $this->request->getPost('cost_price') ?: 0,
            'min_stock'   => (int) $this->request->getPost('min_stock'),
            'unit'        => $this->request->getPost('unit'),
        ];

        if ($this->modelProduk->update($id, $payload)) {
            return redirect()->to('/products')->with('success', 'Data produk berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
    }

    /**
     * Delete product
     */
    public function delete($id)
    {
        $movementCount = $this->modelMutasiStok->where('product_id', $id)->countAllResults();

        if ($movementCount > 0) {
            return $this->jsonResponse(['status' => false, 'message' => 'Produk tidak bisa dihapus karena sudah memiliki riwayat transaksi.'], 400);
        }

        if ($this->modelProduk->delete($id)) {
            return $this->jsonResponse(['status' => true, 'message' => 'Produk berhasil dihapus.']);
        }

        return $this->jsonResponse(['status' => false, 'message' => 'Gagal menghapus produk.'], 500);
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        $products = $this->modelProduk->getProductsWithCategory();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'SKU')
            ->setCellValue('C1', 'Nama Barang')
            ->setCellValue('D1', 'Kategori')
            ->setCellValue('E1', 'Stok')
            ->setCellValue('F1', 'Satuan')
            ->setCellValue('G1', 'Harga');

        $rowNum = 2;
        foreach ($products as $idx => $row) {
            $sheet->setCellValue('A' . $rowNum, $idx + 1)
                ->setCellValue('B' . $rowNum, $row['sku'])
                ->setCellValue('C' . $rowNum, $row['name'])
                ->setCellValue('D' . $rowNum, $row['category_name'])
                ->setCellValue('E' . $rowNum, $row['current_stock'])
                ->setCellValue('F' . $rowNum, $row['unit'])
                ->setCellValue('G' . $rowNum, $row['price']);
            $rowNum++;
        }

        $fileName = 'Laporan_Stok_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export to PDF
     */
    public function exportPdf()
    {
        $products = $this->modelProduk->getProductsWithCategory();

        $html = "<h2>Laporan Inventaris Barang</h2>";
        $html .= "<table border='1' width='100%' cellpadding='5' style='border-collapse:collapse;'>
                    <thead>
                        <tr style='background:#f2f2f2;'>
                            <th>No</th>
                            <th>SKU</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>";

        foreach ($products as $idx => $row) {
            $html .= "<tr>
                        <td>" . ($idx + 1) . "</td>
                        <td>{$row['sku']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['category_name']}</td>
                        <td>{$row['current_stock']}</td>
                        <td>{$row['unit']}</td>
                      </tr>";
        }
        $html .= "</tbody></table>";

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('Laporan_Stok_' . date('Ymd') . '.pdf', ["Attachment" => true]);
        exit;
    }
}
