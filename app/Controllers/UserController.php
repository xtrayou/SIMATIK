<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class UserController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * List all users
     */
    public function index()
    {
        $this->setPageData('Manajemen User', 'Kelola pengguna sistem');

        $keyword = trim((string) ($this->request->getGet('q') ?? ''));
        $filterRole = $this->request->getGet('role');

        $builder = $this->userModel->orderBy('name', 'ASC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('name', $keyword)
                ->orLike('username', $keyword)
                ->groupEnd();
        }

        if ($filterRole && in_array($filterRole, ['admin', 'petugas'])) {
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
            'user'       => ['username' => '', 'name' => '', 'role' => 'petugas', 'is_active' => 1],
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
            'role'     => 'required|in_list[admin,petugas]',
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

        if ($this->userModel->insert($data)) {
            return redirect()->to('/users')->with('sukses', 'User berhasil ditambahkan');
        }

        return redirect()->back()->withInput()->with('galat', 'Gagal menambahkan user');
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('galat', 'User tidak ditemukan');
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
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('galat', 'User tidak ditemukan');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]|alpha_numeric",
            'name'     => 'required|min_length[3]|max_length[100]',
            'role'     => 'required|in_list[admin,petugas]',
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

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/users')->with('sukses', 'User berhasil diperbarui');
        }

        return redirect()->back()->withInput()->with('galat', 'Gagal memperbarui user');
    }

    /**
     * Delete user
     */
    public function delete($id): RedirectResponse
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('galat', 'User tidak ditemukan');
        }

        // Prevent deleting own account
        if ((int) $id === (int) session('userId')) {
            return redirect()->to('/users')->with('galat', 'Tidak dapat menghapus akun sendiri');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/users')->with('sukses', 'User berhasil dihapus');
        }

        return redirect()->to('/users')->with('galat', 'Gagal menghapus user');
    }
}
