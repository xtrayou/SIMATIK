<?php

namespace App\Controllers;

use App\Models\LoanModel;
use App\Models\LoanItemModel;
use App\Models\ProductModel;
use App\Models\StockMovementModel;
use App\Models\NotificationModel;
use Exception;

class LoanController extends BaseController
{
    protected LoanModel $loanModel;
    protected LoanItemModel $loanItemModel;
    protected ProductModel $productModel;
    protected StockMovementModel $stockMovementModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->loanModel          = new LoanModel();
        $this->loanItemModel      = new LoanItemModel();
        $this->productModel       = new ProductModel();
        $this->stockMovementModel = new StockMovementModel();
        $this->notificationModel  = new NotificationModel();
    }

    /**
     * Show loan/request list
     */
    public function index()
    {
        $this->setPageData('Daftar Permintaan', 'Manajemen permintaan dan distribusi ATK');

        $status = $this->request->getGet('status');
        $builder = $this->loanModel->orderBy('created_at', 'DESC');

        if ($status) {
            $builder->where('status', $status);
        }

        $loans = $builder->findAll();

        $data = [
            'daftarPinjaman' => $loans,
            'filterStatus'   => $status
        ];

        return $this->render('loans/index', $data);
    }

    /**
     * Create request form
     */
    public function create()
    {
        $this->setPageData('Buat Permintaan', 'Formulir permintaan ATK baru');

        $products = $this->productModel->where('is_active', true)->orderBy('name', 'ASC')->findAll();

        $data = [
            'daftarProduk' => $products
        ];

        return $this->render('loans/create', $data);
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
            'loan_date'     => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $loanId = $this->loanModel->insert([
                'borrower_name'       => $this->request->getPost('borrower_name'),
                'borrower_identifier' => $this->request->getPost('borrower_identifier'),
                'borrower_unit'       => $this->request->getPost('borrower_unit'),
                'email'               => $this->request->getPost('email'),
                'loan_date'           => $this->request->getPost('loan_date') ?: date('Y-m-d'),
                'status'              => 'requested',
                'notes'               => $this->request->getPost('notes'),
            ]);

            $productIds = (array) $this->request->getPost('product_id');
            $quantities = (array) $this->request->getPost('quantity');

            foreach ($productIds as $index => $pid) {
                if (empty($pid) || empty($quantities[$index])) continue;

                $this->loanItemModel->insert([
                    'loan_id'    => $loanId,
                    'product_id' => $pid,
                    'quantity'   => $quantities[$index],
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new Exception('Gagal menyimpan data ke database.');
            }

            $redirectUrl = $this->request->getPost('_redirect') ?: '/loans';

            // Kirim notifikasi permintaan baru
            try {
                $loanData = $this->loanModel->find($loanId);
                if ($loanData) {
                    $this->notificationModel->createNewLoanNotification($loanData);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi permintaan baru: ' . $e->getMessage());
            }

            return redirect()->to($redirectUrl)->with('sukses', 'Permintaan berhasil diajukan.');
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('galat', $e->getMessage());
        }
    }

    /**
     * Request details
     */
    public function show($id)
    {
        $loan = $this->loanModel->getLoanWithItems((int)$id);

        if (!$loan) {
            return redirect()->to('/loans')->with('galat', 'Data tidak ditemukan.');
        }

        $this->setPageData('Detail Permintaan', 'Review detail permintaan ATK');

        return $this->render('loans/show', ['pinjaman' => $loan]);
    }

    /**
     * AJAX Approve
     */
    public function approve($id)
    {
        $loan = $this->loanModel->find($id);
        if (!$loan) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);

        if ($this->loanModel->update($id, ['status' => 'approved'])) {
            // Kirim notifikasi disetujui
            try {
                $this->notificationModel->createLoanApprovedNotification($loan);
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
        $loan = $this->loanModel->find($id);
        if (!$loan) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);

        $items = $this->loanItemModel->where('loan_id', $id)->findAll();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($items as $item) {
                $this->stockMovementModel->createMovement([
                    'product_id'   => $item['product_id'],
                    'type'         => 'OUT',
                    'quantity'     => $item['quantity'],
                    'notes'        => 'Distribusi ATK - No. Permintaan: #' . $id,
                    'reference_no' => 'REQ-' . $id,
                    'created_by'   => session()->get('name') ?: 'System'
                ]);
            }

            $this->loanModel->update($id, ['status' => 'distributed']);
            $db->transComplete();

            if ($db->transStatus() === false) throw new Exception('Gagal memproses mutasi stok.');

            // Cek stok rendah/habis setelah distribusi
            try {
                foreach ($items as $item) {
                    $product = $this->productModel->find($item['product_id']);
                    if (!$product) continue;
                    if ((int)$product['current_stock'] <= 0) {
                        $this->notificationModel->createOutOfStockNotification($product);
                    } elseif ((int)$product['current_stock'] <= (int)($product['min_stock'] ?? 0)) {
                        $this->notificationModel->createLowStockNotification($product);
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi distribusi: ' . $e->getMessage());
            }

            return $this->jsonResponse(['status' => true, 'message' => 'Barang berhasil didistribusikan dan stok telah terpotong.']);
        } catch (Exception $e) {
            $db->transRollback();
            return $this->jsonResponse(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX Cancel
     */
    public function cancel($id)
    {
        $loan = $this->loanModel->find($id);
        if (!$loan) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);

        if ($loan['status'] == 'distributed') {
            return $this->jsonResponse(['status' => false, 'message' => 'Tidak bisa membatalkan permintaan yang sudah didistribusikan.'], 400);
        }

        if ($this->loanModel->update($id, ['status' => 'cancelled'])) {
            // Kirim notifikasi dibatalkan
            try {
                $this->notificationModel->createLoanCancelledNotification($loan);
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi cancel: ' . $e->getMessage());
            }
            return $this->jsonResponse(['status' => true, 'message' => 'Permintaan berhasil dibatalkan.']);
        }

        return $this->jsonResponse(['status' => false, 'message' => 'Gagal membatalkan permintaan.'], 500);
    }
}
