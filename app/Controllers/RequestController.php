<?php

namespace App\Controllers;

use App\Models\RequestModel;
use App\Models\RequestItemModel;
use App\Models\ProductModel;
use App\Models\StockMovementModel;
use App\Models\NotificationModel;
use Exception;

class RequestController extends BaseController
{
    protected RequestModel $requestModel;
    protected RequestItemModel $requestItemModel;
    protected ProductModel $productModel;
    protected StockMovementModel $stockMovementModel;
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->requestModel       = new RequestModel();
        $this->requestItemModel   = new RequestItemModel();
        $this->productModel       = new ProductModel();
        $this->stockMovementModel = new StockMovementModel();
        $this->notificationModel  = new NotificationModel();
    }

    /**
     * Show request list
     */
    public function index()
    {
        $this->setPageData('Daftar Permintaan', 'Manajemen permintaan dan distribusi ATK');

        $status = $this->request->getGet('status');
        $builder = $this->requestModel->orderBy('created_at', 'DESC');

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

        $products = $this->productModel->where('is_active', true)->orderBy('name', 'ASC')->findAll();

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
            $requestId = $this->requestModel->insert([
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

                $this->requestItemModel->insert([
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
                $requestData = $this->requestModel->find($requestId);
                if ($requestData) {
                    $this->notificationModel->createNewRequestNotification($requestData);
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
        $requestData = $this->requestModel->getRequestWithItems((int)$id);

        if (!$requestData) {
            return redirect()->to('/requests')->with('error', 'Data tidak ditemukan.');
        }

        $this->setPageData('Detail Permintaan', 'Review detail permintaan ATK');

        return $this->render('requests/show', ['pinjaman' => $requestData]);
    }

    /**
     * AJAX Approve
     */
    public function approve($id)
    {
        $requestData = $this->requestModel->find($id);
        if (!$requestData) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);

        if ($this->requestModel->update($id, ['status' => 'approved'])) {
            // Kirim notifikasi disetujui
            try {
                $this->notificationModel->createRequestApprovedNotification($requestData);
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
        $requestData = $this->requestModel->find($id);
        if (!$requestData) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);

        $items = $this->requestItemModel->where('request_id', $id)->findAll();

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

            $this->requestModel->update($id, ['status' => 'distributed']);
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
        $requestData = $this->requestModel->find($id);
        if (!$requestData) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);

        if ($requestData['status'] == 'distributed') {
            return $this->jsonResponse(['status' => false, 'message' => 'Tidak bisa membatalkan permintaan yang sudah didistribusikan.'], 400);
        }

        if ($this->requestModel->update($id, ['status' => 'cancelled'])) {
            // Kirim notifikasi dibatalkan
            try {
                $this->notificationModel->createRequestCancelledNotification($requestData);
            } catch (\Throwable $e) {
                log_message('error', 'Gagal kirim notifikasi cancel: ' . $e->getMessage());
            }
            return $this->jsonResponse(['status' => true, 'message' => 'Permintaan berhasil dibatalkan.']);
        }

        return $this->jsonResponse(['status' => false, 'message' => 'Gagal membatalkan permintaan.'], 500);
    }
}
