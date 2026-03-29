<?php

namespace App\Controllers;

use App\Models\PermintaanModel;
use App\Models\ItemPermintaanModel;
use App\Models\ProdukModel;
use App\Models\MutasiStokModel;
use App\Models\NotifikasiModel;
use Exception;

class PermintaanController extends BaseController
{
    protected PermintaanModel $modelPermintaan;
    protected ItemPermintaanModel $modelItemPermintaan;
    protected ProdukModel $modelProduk;
    protected MutasiStokModel $modelMutasiStok;
    protected NotifikasiModel $modelNotifikasi;

    public function __construct()
    {
        $this->modelPermintaan       = new PermintaanModel();
        $this->modelItemPermintaan   = new ItemPermintaanModel();
        $this->modelProduk       = new ProdukModel();
        $this->modelMutasiStok = new MutasiStokModel();
        $this->modelNotifikasi  = new NotifikasiModel();
    }

    /**
     * Show request list
     */
    public function index()
    {
        $this->setPageData('Daftar Permintaan', 'Manajemen permintaan dan distribusi ATK');

        $status = $this->request->getGet('status');
        $builder = $this->modelPermintaan->orderBy('created_at', 'DESC');

        if ($status) {
            $builder->where('status', $status);
        }

        $requests = $builder->findAll();

        $data = [
            'daftarPinjaman' => $requests,
            'filterStatus'   => $status
        ];

        return $this->render('requests/index', $data);
    }

    /**
     * Create request form
     */
    public function create()
    {
        $this->setPageData('Buat Permintaan', 'Formulir permintaan ATK baru');

        $products = $this->modelProduk->where('is_active', true)->orderBy('name', 'ASC')->findAll();

        $data = [
            'daftarProduk' => $products
        ];

        return $this->render('requests/create', $data);
    }

    /**
     * Store new request
     */
    public function store()
    {
        $rules = [
            'borrower_name' => 'required|min_length[3]|max_length[150]',
            'borrower_unit' => 'required',
            'email'         => 'required|valid_email',
            'product_id'    => 'required',
            'quantity'      => 'required',
            'request_date'  => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $requestId = $this->modelPermintaan->insert([
                'borrower_name'       => $this->request->getPost('borrower_name'),
                'borrower_identifier' => $this->request->getPost('borrower_identifier'),
                'borrower_unit'       => $this->request->getPost('borrower_unit'),
                'email'               => $this->request->getPost('email'),
                'request_date'        => $this->request->getPost('request_date') ?: date('Y-m-d'),
                'status'              => 'requested',
                'notes'               => $this->request->getPost('notes'),
            ]);

            $productIds = (array) $this->request->getPost('product_id');
            $quantities = (array) $this->request->getPost('quantity');

            foreach ($productIds as $index => $pid) {
                if (empty($pid) || empty($quantities[$index])) continue;

                $this->modelItemPermintaan->insert([
                    'request_id' => $requestId,
                    'product_id' => $pid,
                    'quantity'   => $quantities[$index],
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Gagal menyimpan data ke database.');
            }

            $redirectUrl = $this->request->getPost('_redirect') ?: '/requests';

            // Kirim notifikasi permintaan baru
            try {
                $dataPermintaan = $this->modelPermintaan->find($requestId);
                if ($dataPermintaan) {
                    $this->modelNotifikasi->createNewRequestNotification($dataPermintaan);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi permintaan baru: ' . $e->getMessage());
            }

            return redirect()->to($redirectUrl)->with('success', 'Permintaan berhasil diajukan.');
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Request details
     */
    public function show($id)
    {
        $dataPermintaan = $this->modelPermintaan->getRequestWithItems((int)$id);

        if (!$dataPermintaan) {
            return redirect()->to('/requests')->with('error', 'Data tidak ditemukan.');
        }

        $this->setPageData('Detail Permintaan', 'Review detail permintaan ATK');

        return $this->render('requests/show', ['pinjaman' => $dataPermintaan]);
    }

    /**
     * AJAX Approve
     */
    public function approve($id)
    {
        $dataPermintaan = $this->modelPermintaan->find($id);
        if (!$dataPermintaan) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);

        $reason = $this->request->getPost('reason') ?: 'Disetujui oleh Admin';

        if ($this->modelPermintaan->update($id, ['status' => 'approved', 'status_reason' => $reason])) {
            // Kirim notifikasi disetujui
            try {
                $this->modelNotifikasi->createRequestApprovedNotification($dataPermintaan);
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi approve: ' . $e->getMessage());
            }
            return $this->jsonResponse(['status' => true, 'message' => 'Permintaan disetujui.']);
        }

        return $this->jsonResponse(['status' => false, 'message' => 'Gagal memperbarui status.'], 500);
    }

    /**
     * AJAX Distribute (Decrease Stock)
     */
    public function distribute($id)
    {
        $dataPermintaan = $this->modelPermintaan->find($id);
        if (!$dataPermintaan) {
            return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        if ($dataPermintaan['status'] !== 'approved') {
            return $this->jsonResponse(['status' => false, 'message' => 'Permintaan belum disetujui'], 400);
        }

        $daftarItem = $this->modelItemPermintaan->where('request_id', $id)->findAll();

        if (empty($daftarItem)) {
            return $this->jsonResponse(['status' => false, 'message' => 'Tidak ada item untuk didistribusikan'], 400);
        }

        // Cek stok sebelum distribusi
        $errorStok = [];
        foreach ($daftarItem as $item) {
            $produk = $this->modelProduk->find($item['product_id']);
            if (!$produk) {
                $errorStok[] = "Produk ID {$item['product_id']} tidak ditemukan";
                continue;
            }

            $stokSekarang = (int) $produk['current_stock'];
            $jumlahDiminta = (int) $item['quantity'];

            if ($stokSekarang < $jumlahDiminta) {
                $errorStok[] = "{$produk['name']}: stok tersedia {$stokSekarang}, diminta {$jumlahDiminta}";
            }
        }

        if (!empty($errorStok)) {
            return $this->jsonResponse([
                'status' => false,
                'message' => 'Stok tidak mencukupi:<br>• ' . implode('<br>• ', $errorStok)
            ], 400);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get current user ID
            $userId = session()->get('userId');
            if (!$userId || !is_numeric($userId)) {
                throw new Exception('Session tidak valid. Silakan login ulang.');
            }

            foreach ($daftarItem as $item) {
                $this->modelMutasiStok->createMovement([
                    'product_id'   => $item['product_id'],
                    'type'         => 'OUT',
                    'quantity'     => $item['quantity'],
                    'notes'        => 'Distribusi ATK - No. Permintaan: #' . $id . ' oleh ' . (session()->get('name') ?: 'Admin'),
                    'reference_no' => 'REQ-' . $id,
                    'created_by'   => (int) $userId
                ]);
            }

            $this->modelPermintaan->update($id, [
                'status' => 'distributed',
                'status_reason' => $this->request->getPost('reason') ?: 'Barang didistribusikan'
            ]);
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Gagal memproses mutasi stok. Silakan coba lagi.');
            }

            // Cek stok rendah/habis setelah distribusi
            try {
                foreach ($daftarItem as $item) {
                    $produk = $this->modelProduk->find($item['product_id']);
                    if (!$produk) continue;
                    if ((int)$produk['current_stock'] <= 0) {
                        $this->modelNotifikasi->createOutOfStockNotification($produk);
                    } elseif ((int)$produk['current_stock'] <= (int)($produk['min_stock'] ?? 0)) {
                        $this->modelNotifikasi->createLowStockNotification($produk);
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi distribusi: ' . $e->getMessage());
            }

            return $this->jsonResponse(['status' => true, 'message' => 'Barang berhasil didistribusikan dan stok telah terpotong.']);
        } catch (Exception $e) {
            $db->transRollback();
            log_message('error', 'Error saat distribusi: ' . $e->getMessage());
            return $this->jsonResponse(['status' => false, 'message' => 'Gagal distribusi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * AJAX Cancel
     */
    public function cancel($id)
    {
        $dataPermintaan = $this->modelPermintaan->find($id);
        if (!$dataPermintaan) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);

        if ($dataPermintaan['status'] == 'distributed') {
            return $this->jsonResponse(['status' => false, 'message' => 'Tidak bisa membatalkan permintaan yang sudah didistribusikan.'], 400);
        }

        $reason = $this->request->getPost('reason') ?: 'Dibatalkan oleh Admin';

        if ($this->modelPermintaan->update($id, ['status' => 'cancelled', 'status_reason' => $reason])) {
            // Kirim notifikasi dibatalkan
            try {
                $this->modelNotifikasi->createRequestCancelledNotification($dataPermintaan);
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi cancel: ' . $e->getMessage());
            }
            return $this->jsonResponse(['status' => true, 'message' => 'Permintaan berhasil dibatalkan.']);
        }

        return $this->jsonResponse(['status' => false, 'message' => 'Gagal membatalkan permintaan.'], 500);
    }

    /**
     * Halaman publik - form permintaan barang (tanpa login)
     */
    public function askForm()
    {
        $produk = $this->modelProduk
            ->where('is_active', true)
            ->where('current_stock >', 0)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title'       => 'Ajukan Permintaan ATK | SIMATIK',
            'daftarProduk' => $produk,
        ];

        return view('requests/ask', $data);
    }

    /**
     * Proses simpan permintaan publik
     */
    public function askStore()
    {
        $rules = [
            'borrower_name' => 'required|min_length[3]|max_length[150]',
            'borrower_unit' => 'required',
            'email'         => 'required|valid_email',
            'product_id'    => 'required',
            'quantity'      => 'required',
            'request_date'  => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $requestId = $this->modelPermintaan->insert([
                'borrower_name'       => $this->request->getPost('borrower_name'),
                'borrower_identifier' => $this->request->getPost('borrower_identifier'),
                'borrower_unit'       => $this->request->getPost('borrower_unit'),
                'email'               => $this->request->getPost('email'),
                'request_date'        => $this->request->getPost('request_date') ?: date('Y-m-d'),
                'status'              => 'requested',
                'notes'               => $this->request->getPost('notes'),
            ]);

            $productIds = (array) $this->request->getPost('product_id');
            $quantities = (array) $this->request->getPost('quantity');

            foreach ($productIds as $index => $pid) {
                if (empty($pid) || empty($quantities[$index])) continue;
                $this->modelItemPermintaan->insert([
                    'request_id' => $requestId,
                    'product_id' => $pid,
                    'quantity'   => $quantities[$index],
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Gagal menyimpan data ke database.');
            }

            // Kirim notifikasi ke admin
            try {
                $dataPermintaan = $this->modelPermintaan->find($requestId);
                if ($dataPermintaan) {
                    $this->modelNotifikasi->createNewRequestNotification($dataPermintaan);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi permintaan publik: ' . $e->getMessage());
            }

            return redirect()->to('/ask/success')->with('request_id', $requestId)->with('borrower_name', $this->request->getPost('borrower_name'));
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman sukses setelah permintaan publik dikirim
     */
    public function askSuccess()
    {
        $data = [
            'title'         => 'Permintaan Terkirim | SIMATIK',
            'request_id'    => session()->getFlashdata('request_id'),
            'borrower_name' => session()->getFlashdata('borrower_name'),
        ];

        return view('requests/ask_success', $data);
    }

    /**
     * Halaman publik - form tracking permintaan (tanpa login)
     */
    public function trackForm()
    {
        $data = [
            'title' => 'Lacak Permintaan ATK | SIMATIK',
        ];

        return view('requests/track', $data);
    }

    /**
     * Proses pencarian status permintaan publik
     */
    public function trackStatus()
    {
        $referenceNo = trim($this->request->getPost('reference_no') ?? '');
        $email = trim($this->request->getPost('email') ?? '');

        // Validasi input
        if (empty($referenceNo) || empty($email)) {
            return redirect()->back()->withInput()->with('error', 'Nomor referensi dan email harus diisi.');
        }

        // Parse nomor referensi - format: REQ-0001
        if (!preg_match('/^REQ-(\d+)$/i', $referenceNo, $matches)) {
            return redirect()->back()->withInput()->with('error', 'Format nomor referensi tidak valid. Gunakan format: REQ-0001');
        }

        $requestId = (int) $matches[1];

        // Cari permintaan berdasarkan ID dan email
        $dataPermintaan = $this->modelPermintaan->find($requestId);

        if (!$dataPermintaan) {
            return redirect()->back()->withInput()->with('error', 'Permintaan tidak ditemukan.');
        }

        // Validasi email
        if (strtolower($dataPermintaan['email']) !== strtolower($email)) {
            return redirect()->back()->withInput()->with('error', 'Email tidak sesuai dengan data permintaan.');
        }

        // Ambil detail item permintaan
        $itemPermintaan = $this->modelItemPermintaan->where('request_id', $requestId)->findAll();

        // Enriched item dengan detail produk
        $itemEnriched = [];
        foreach ($itemPermintaan as $item) {
            $produk = $this->modelProduk->find($item['product_id']);
            $itemEnriched[] = [
                'item' => $item,
                'produk' => $produk
            ];
        }

        // Tentukan badge status
        $statusBadges = [
            'requested'   => ['text' => 'Menunggu Persetujuan', 'color' => 'warning', 'icon' => 'hourglass-split'],
            'approved'    => ['text' => 'Disetujui', 'color' => 'info', 'icon' => 'check-circle'],
            'distributed' => ['text' => 'Sudah Dikirim', 'color' => 'success', 'icon' => 'check2-all'],
            'cancelled'   => ['text' => 'Dibatalkan', 'color' => 'danger', 'icon' => 'x-circle'],
        ];

        $data = [
            'title'              => 'Lacak Permintaan ATK | SIMATIK',
            'referenceNo'        => $referenceNo,
            'permintaan'         => $dataPermintaan,
            'itemPermintaan'     => $itemEnriched,
            'statusBadges'       => $statusBadges,
        ];

        return view('requests/track_result', $data);
    }
}
