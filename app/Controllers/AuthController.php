<?php

namespace App\Controllers;

use App\Models\PenggunaModel;
use App\Models\HakAksesModel;

class AuthController extends BaseController
{
    protected PenggunaModel $modelPengguna;

    public function __construct()
    {
        $this->modelPengguna = new PenggunaModel();
    }

    /**
     * Handle Login POST
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('loginError', 'Username dan password wajib diisi.');
        }

        $user = $this->modelPengguna->findByUsername((string)$username);

        // Verify user and password
        if ($user && password_verify((string) $password, $user['password'])) {

            // Check if user is active
            if (isset($user['is_active']) && $user['is_active'] == 0) {
                return redirect()->back()->withInput()->with('loginError', 'Akun Anda telah dinonaktifkan.');
            }

            // Set dynamic session data
            $sessionData = [
                'userId'     => $user['id'],
                'username'   => $user['username'],
                'name'       => $user['name'],
                'role'          => $user['role'] ?? 'user',
                'isLoggedIn'    => true,
                'last_activity' => time(),
            ];

            // Load permissions for user's role
            try {
                $modelHakAkses = new HakAksesModel();
                $sessionData['permissions'] = $modelHakAkses->getPermissionNamesByRole($user['role'] ?? 'staff');
            } catch (\Exception $e) {
                $sessionData['permissions'] = [];
            }

            session()->set($sessionData);

            return redirect()->to('/dashboard')->with('loginSuccess', 'Selamat datang kembali, ' . $user['name']);
        }

        return redirect()->back()->withInput()->with('loginError', 'Username atau password salah.');
    }

    /**
     * Handle Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
