<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Cek apakah user sudah login dan memiliki permission
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            // Jika belum login, redirect ke landing page (berisi modal login)
            return redirect()->to('/')->with('loginError', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        }

        // --- Cek Inaktivitas (15 Menit = 900 detik) ---
        $lastActivity = session()->get('last_activity');
        $currentTime  = time();
        $timeout      = 900; 

        if ($lastActivity && ($currentTime - $lastActivity > $timeout)) {
            session()->destroy();
            return redirect()->to('/')->with('loginError', 'Sesi Anda telah berakhir karena tidak ada aktivitas selama 15 menit.');
        }
        
        // Update waktu aktivitas terakhir
        session()->set('last_activity', $currentTime);
        // ----------------------------------------------

        // Cek permission jika ada argument $arguments dari filter
        if (!empty($arguments)) {
            $userPermissions = session()->get('permissions') ?? [];
            $hasPermission = false;

            foreach ($arguments as $permission) {
                if (in_array($permission, $userPermissions)) {
                    $hasPermission = true;
                    break;
                }
            }

            // Admin bypass semua permission
            if (session()->get('role') === 'admin') {
                $hasPermission = true;
            }

            if (!$hasPermission) {
                if ($request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON(['status' => false, 'message' => 'Anda tidak memiliki akses untuk fitur ini.']);
                }
                return redirect()->to('/dashboard')->with('galat', 'Anda tidak memiliki akses untuk fitur ini.');
            }
        }
    }

    /**
     * Tidak ada aksi setelah request
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // ...
    }
}
