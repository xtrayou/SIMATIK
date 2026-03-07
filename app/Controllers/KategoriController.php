<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use CodeIgniter\HTTP\RedirectResponse;

class KategoriController extends BaseController
{
    protected KategoriModel $modelKategori;

    public function __construct()
    {
        $this->modelKategori = new KategoriModel();
    }

    /**
     * Map form data to array
     */
    private function getFormData(): array
    {
        return [
            'name'        => trim((string) $this->request->getPost('name')),
            'description' => trim((string) $this->request->getPost('description')),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    /**
     * Common validation logic
     */
    private function validateRequest(array $rules): ?RedirectResponse
    {
        if ($this->validate($rules)) {
            return null;
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    /**
     * Find category by ID or redirect
     */
    private function findCategoryOrRedirect(int $id)
    {
        $category = $this->modelKategori->find($id);

        if ($category) {
            return $category;
        }

        return redirect()->to('/categories')
            ->with('error', 'Kategori tidak ditemukan');
    }

    /**
     * List categories
     */
    public function index()
    {
        $this->setPageData('Kategori', 'Manajemen Kategori Produk');

        $keyword      = trim((string) ($this->request->getGet('q') ?? ''));
        $filterStatus = $this->request->getGet('status');
        $perPage      = (int) ($this->request->getGet('per_page') ?? 10);
        $orderBy      = $this->request->getGet('urut') ?? 'name';
        $orderDir     = strtolower($this->request->getGet('arah') ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $page         = (int) ($this->request->getGet('page') ?? 1);

        if (!in_array($orderBy, ['name', 'created_at', 'is_active'])) {
            $orderBy = 'name';
        }

        $totalData  = $this->modelKategori->countCategories($keyword, $filterStatus);
        $offset     = ($page - 1) * $perPage;
        $categories = $this->modelKategori->getCategoriesWithProductCount(
            $keyword, $filterStatus, $orderBy, $orderDir, $perPage, $offset
        );

        $pager = service('pager');
        $pagination = $pager->makeLinks($page, $perPage, $totalData, 'default_full');

        return $this->render('categories/index', [
            'daftarKategori' => $categories,
            'kataKunci'      => $keyword,
            'filterStatus'   => $filterStatus,
            'perHalaman'     => $perPage,
            'kolomUrut'      => $orderBy,
            'arahUrut'       => $orderDir,
            'totalData'      => $totalData,
            'nomorAwal'      => $offset + 1,
            'paginasi'       => $pagination,
        ]);
    }

    public function create()
    {
        $this->setPageData('Tambah Kategori', 'Buat kategori produk baru');

        return $this->render('categories/create', [
            'kategori'   => ['name' => '', 'description' => '', 'is_active' => true],
            'validation' => service('validation'),
        ]);
    }

    public function store()
    {
        $error = $this->validateRequest([
            'name'        => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
            'description' => 'permit_empty|max_length[500]',
        ]);
        
        if ($error) return $error;

        if ($this->modelKategori->insert($this->getFormData())) {
            return redirect()->to('/categories')->with('success', 'Kategori berhasil ditambahkan');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kategori');
    }

    public function edit($id)
    {
        $category = $this->findCategoryOrRedirect((int) $id);
        if ($category instanceof RedirectResponse) return $category;

        $this->setPageData('Edit Kategori', 'Edit Kategori: ' . $category['name']);

        return $this->render('categories/edit', [
            'kategori'   => $category,
            'validation' => service('validation'),
        ]);
    }

    public function update($id)
    {
        $category = $this->findCategoryOrRedirect((int) $id);
        if ($category instanceof RedirectResponse) return $category;

        $error = $this->validateRequest([
            'name'        => "required|min_length[3]|max_length[100]|is_unique[categories.name,id,{$id}]",
            'description' => 'permit_empty|max_length[500]',
        ]);
        
        if ($error) return $error;

        if ($this->modelKategori->update($id, $this->getFormData())) {
            return redirect()->to('/categories')->with('success', 'Kategori berhasil diperbarui');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kategori');
    }

    public function delete($id): RedirectResponse
    {
        $category = $this->modelKategori->find($id);
        if (!$category) {
            return redirect()->to('/categories')->with('error', 'Kategori tidak ditemukan');
        }

        if ($this->modelKategori->canDelete((int) $id) === false) {
            return redirect()->to('/categories')
                ->with('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh produk');
        }

        if ($this->modelKategori->delete($id)) {
            return redirect()->to('/categories')->with('success', 'Kategori berhasil dihapus');
        }

        return redirect()->to('/categories')->with('error', 'Gagal menghapus kategori');
    }
}
