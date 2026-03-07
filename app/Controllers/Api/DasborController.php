<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\ProdukModel;

class DasborController extends BaseController
{
    protected ProdukModel $modelProduk;
    protected KategoriModel $modelKategori;

    public function __construct()
    {
        $this->modelProduk = new ProdukModel();
        $this->modelKategori = new KategoriModel();
    }

    /**
     * Get dashboard statistics via AJAX
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_products'   => $this->modelProduk->where('is_active', true)->countAllResults(),
                'total_categories' => $this->modelKategori->where('is_active', true)->countAllResults(),
                'low_stock_count'  => count($this->modelProduk->getLowStockProducts()),
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
