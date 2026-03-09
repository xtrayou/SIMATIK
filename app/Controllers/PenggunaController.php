<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenggunaModel;
use CodeIgniter\HTTP\RedirectResponse;

class PenggunaController extends BaseController
{
    protected PenggunaModel $modelPengguna;

    public function __construct()
    {
        $this->modelPengguna = new PenggunaModel();
    }

    /**
     * List all users
     */
    public function index()
    {
        $this->setPageData('Manajemen User', 'Kelola pengguna sistem');

        $keyword = trim((string) ($this->request->getGet('q') ?? ''));
        $filterRole = $this->request->getGet('role');

        $builder = $this->modelPengguna->orderBy('name', 'ASC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('name', $keyword)
                ->orLike('username', $keyword)
                ->groupEnd();
        }

        if ($filterRole && in_array($filterRole, ['superadmin', 'admin', 'user'])) {
            $builder->where('role', $filterRole);
        }

        $users = $builder->findAll();

        return $this->render('users/index', [
            'users'      => $users,
            'keyword'    => $keyword,
            'filterRole' => $filterRole,
        ]);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $this->setPageData('Tambah User', 'Buat pengguna baru');

        return $this->render('users/create', [
            'user'       => ['username' => '', 'name' => '', 'role' => 'user', 'is_active' => 1],
            'validation' => service('validation'),
        ]);
    }

    /**
     * Store new user
     */
    public function store()
    {
        if (!$this->validate([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]|alpha_numeric',
            'name'     => 'required|min_length[3]|max_length[100]',
            'password' => 'required|min_length[6]|max_length[255]',
            'role'     => 'required|in_list[superadmin,admin,user]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username'  => trim($this->request->getPost('username')),
            'name'      => trim($this->request->getPost('name')),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($this->modelPengguna->insert($data)) {
            return redirect()->to('/users')->with('success', 'User berhasil ditambahkan');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user');
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $user = $this->modelPengguna->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        $this->setPageData('Edit User', 'Edit: ' . $user['name']);

        return $this->render('users/edit', [
            'user'       => $user,
            'validation' => service('validation'),
        ]);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $user = $this->modelPengguna->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]|alpha_numeric",
            'name'     => 'required|min_length[3]|max_length[100]',
            'role'     => 'required|in_list[superadmin,admin,user]',
        ];

        // Password optional on update
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username'  => trim($this->request->getPost('username')),
            'name'      => trim($this->request->getPost('name')),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->modelPengguna->update($id, $data)) {
            return redirect()->to('/users')->with('success', 'User berhasil diperbarui');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui user');
    }

    /**
     * Delete user
     */
    public function delete($id): RedirectResponse
    {
        $user = $this->modelPengguna->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan');
        }

        // Prevent deleting own account
        if ((int) $id === (int) session('userId')) {
            return redirect()->to('/users')->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        if ($this->modelPengguna->delete($id)) {
            return redirect()->to('/users')->with('success', 'User berhasil dihapus');
        }

        return redirect()->to('/users')->with('error', 'Gagal menghapus user');
    }
}
