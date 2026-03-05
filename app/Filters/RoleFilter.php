<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Cek apakah user memiliki role yang diizinkan
     * Digunakan di routes: ['filter' => 'role:admin'] atau ['filter' => 'role:admin,staff']
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/')->with('loginError', 'Silakan login terlebih dahulu.');
        }

        if (!empty($arguments)) {
            $userRole = session()->get('role') ?? '';

            if (!in_array($userRole, $arguments)) {
                if ($request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON(['status' => false, 'message' => 'Akses ditolak. Role Anda tidak memiliki izin.']);
                }
                return redirect()->to('/dashboard')->with('galat', 'Akses ditolak. Role Anda tidak memiliki izin untuk halaman ini.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // ...
    }
}
