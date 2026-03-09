<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\ProdukModel;

class BerandaController extends BaseController
{
    protected ProdukModel  $modelProduk;
    protected KategoriModel $modelKategori;

    public function __construct()
    {
        $this->modelProduk   = new ProdukModel();
        $this->modelKategori = new KategoriModel();
    }

    /**
     * Landing Page — tampilkan daftar produk & kategori dari DB
     */
    public function index()
    {
        // Jika sudah login, langsung ke dashboard
        // if (session()->get('isLoggedIn')) {
        //     return redirect()->to('/dashboard');
        // }

        // Ambil semua produk aktif beserta category_id, unit, dan stok
        try {
            $daftarProduk = $this->modelProduk
                ->select('products.id, products.name, products.category_id, products.current_stock, products.unit')
                ->where('products.is_active', true)
                ->orderBy('products.name', 'ASC')
                ->findAll();

            // Ambil kategori aktif untuk filter dropdown
            $daftarKategori = $this->modelKategori
                ->select('id, name')
                ->where('is_active', true)
                ->orderBy('name', 'ASC')
                ->findAll();
        } catch (\Exception $e) {
            $daftarProduk   = [];
            $daftarKategori = [];
        }

        // Daftar unit kerja / prodi
        $unitKerja = [
            'Sistem Informasi',
            'Informatika',
            'TU Fakultas',
            'Lainnya',
        ];

        return view('home/index', [
            'daftarProduk'   => $daftarProduk,
            'daftarKategori' => $daftarKategori,
            'unitKerja'      => $unitKerja,
        ]);
    }
}
