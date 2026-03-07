<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SettingController extends BaseController
{
    /**
     * Default settings configuration
     */
    private function getDefaults(): array
    {
        return [
            // 1. Umum
            'app_name'          => 'SIMATIK',
            'institution'       => 'Fakultas Ilmu Komputer',
            'email'             => 'admin@fasilkom.ac.id',
            'address'           => 'Jl. Raya Kampus, Gedung Fasilkom Lt. 2',
            'timezone'          => 'Asia/Jakarta',
            'date_format'       => 'd/m/Y',
            'logo'              => '',

            // 2. Inventaris
            'low_stock_threshold'    => 10,
            'critical_stock_threshold' => 5,
            'items_per_page'         => 10,
            'default_unit'           => 'pcs',
            'auto_update_stock'      => 1,
            'notify_low_stock'       => 1,

            // 3. Permintaan
            'request_max_days'          => 7,
            'request_max_items'         => 5,
            'request_require_approval'  => 1,
            'request_late_fee'          => 0,
            'request_allow_extend'      => 0,

            // 4. Pengguna & Akses
            'enable_multi_role'      => 0,
            'default_role'           => 'petugas',
            'session_timeout'        => 30,
            'max_login_attempts'     => 5,
            'enable_audit_log'       => 1,

            // 5. Notifikasi
            'notify_email_low_stock'    => 1,
            'notify_email_new_request'     => 1,
            'notify_email_overdue'      => 1,
            'notify_dashboard'          => 1,
            'notify_due_reminder_days'  => 1,
        ];
    }

    /**
     * Load saved settings (merged with defaults)
     */
    private function loadSettings(): array
    {
        $defaults = $this->getDefaults();
        $saved = session('app_settings') ?? [];
        return array_merge($defaults, $saved);
    }

    /**
     * Show settings page
     */
    public function index()
    {
        $this->setPageData('Pengaturan Sistem', 'Konfigurasi sistem inventaris');

        return $this->render('settings/index', [
            'settings' => $this->loadSettings(),
        ]);
    }

    /**
     * Update settings
     */
    public function update()
    {
        $tab = $this->request->getPost('_tab') ?? 'general';

        $rules = $this->getRulesForTab($tab);

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('active_tab', $tab);
        }

        // Merge with existing settings
        $current = $this->loadSettings();
        $fields = array_keys($rules);

        // Handle checkboxes / toggles (not sent if unchecked)
        $toggleFields = [
            'auto_update_stock',
            'notify_low_stock',
            'request_require_approval',
            'request_allow_extend',
            'enable_multi_role',
            'enable_audit_log',
            'notify_email_low_stock',
            'notify_email_new_request',
            'notify_email_overdue',
            'notify_dashboard',
        ];

        foreach ($fields as $field) {
            if (in_array($field, $toggleFields)) {
                $current[$field] = $this->request->getPost($field) ? 1 : 0;
            } else {
                $val = $this->request->getPost($field);
                if ($val !== null) {
                    $current[$field] = trim($val);
                }
            }
        }

        // Handle logo upload
        if ($tab === 'general') {
            $logo = $this->request->getFile('logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = 'logo_' . time() . '.' . $logo->getExtension();
                $logo->move(FCPATH . 'img', $newName);
                $current['logo'] = $newName;
            }
        }

        // Save to session (in production, save to DB)
        session()->set('app_settings', $current);

        return redirect()->to('/settings')
            ->with('success', 'Pengaturan berhasil disimpan')
            ->with('active_tab', $tab);
    }

    /**
     * Get validation rules per tab
     */
    private function getRulesForTab(string $tab): array
    {
        return match ($tab) {
            'general' => [
                'app_name'    => 'required|max_length[100]',
                'institution' => 'required|max_length[200]',
                'email'       => 'required|valid_email',
                'address'     => 'permit_empty|max_length[500]',
                'timezone'    => 'required|in_list[Asia/Jakarta,Asia/Makassar,Asia/Jayapura]',
                'date_format' => 'required|in_list[d/m/Y,Y-m-d,m/d/Y,d-M-Y]',
            ],
            'inventory' => [
                'low_stock_threshold'      => 'required|integer|greater_than[0]',
                'critical_stock_threshold' => 'required|integer|greater_than[0]',
                'items_per_page'           => 'required|integer|in_list[10,25,50,100]',
                'default_unit'             => 'required|in_list[pcs,box,rim,lusin,pak,set,unit,roll]',
            ],
            'request' => [
                'request_max_days'  => 'required|integer|greater_than[0]',
                'request_max_items' => 'required|integer|greater_than[0]',
                'request_late_fee'  => 'permit_empty|integer|greater_than_equal_to[0]',
            ],
            'access' => [
                'default_role'       => 'required|in_list[petugas,admin]',
                'session_timeout'    => 'required|integer|greater_than[0]',
                'max_login_attempts' => 'required|integer|greater_than[0]',
            ],
            'notification' => [
                'notify_due_reminder_days' => 'required|integer|greater_than_equal_to[0]',
            ],
            default => [],
        };
    }
}
