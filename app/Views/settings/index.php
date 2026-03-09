<?= $this->extend('layouts/app') ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $activeTab = session('active_tab') ?? 'general'; ?>

<div class="row">
    <div class="col-12">
        <?php if (session('sukses')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= session('sukses') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tab Navigation -->
<div class="row">
    <div class="col-12">
        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'general' ? 'active' : '' ?>" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <i class="bi bi-gear me-1"></i>Umum
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'inventory' ? 'active' : '' ?>" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
                    <i class="bi bi-box-seam me-1"></i>Inventaris
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'request' ? 'active' : '' ?>" id="request-tab" data-bs-toggle="tab" data-bs-target="#request" type="button" role="tab">
                    <i class="bi bi-calendar-event me-1"></i>Permintaan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'access' ? 'active' : '' ?>" id="access-tab" data-bs-toggle="tab" data-bs-target="#access" type="button" role="tab">
                    <i class="bi bi-shield-lock me-1"></i>Pengguna & Akses
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'notification' ? 'active' : '' ?>" id="notification-tab" data-bs-toggle="tab" data-bs-target="#notification" type="button" role="tab">
                    <i class="bi bi-bell me-1"></i>Notifikasi
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Content -->
<div class="tab-content mt-0" id="settingsTabContent">

    <!-- ============================================ -->
    <!-- 1. UMUM (General) -->
    <!-- ============================================ -->
    <div class="tab-pane fade <?= $activeTab === 'general' ? 'show active' : '' ?>" id="general" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear-fill me-2 text-primary"></i>Pengaturan Umum</h5>
                <p class="text-muted mb-0 small">Konfigurasi dasar aplikasi agar bisa digunakan lintas fakultas</p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/settings/update') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_tab" value="general">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="app_name" class="form-label">Nama Aplikasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="app_name" name="app_name" value="<?= esc($settings['app_name']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="institution" class="form-label">Institusi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="institution" name="institution" value="<?= esc($settings['institution']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Admin <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc($settings['email']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="logo" class="form-label">Logo Aplikasi</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            <?php if (!empty($settings['logo'])): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url('img/' . $settings['logo']) ?>" alt="Logo" class="img-thumbnail" style="max-height:60px;">
                                    <span class="text-muted small ms-2"><?= esc($settings['logo']) ?></span>
                                </div>
                            <?php else: ?>
                                <div class="form-text">Format: JPG, PNG, SVG. Maks 2MB</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat Institusi</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?= esc($settings['address']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="timezone" class="form-label">Zona Waktu <span class="text-danger">*</span></label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="Asia/Jakarta" <?= $settings['timezone'] === 'Asia/Jakarta' ? 'selected' : '' ?>>WIB (Asia/Jakarta)</option>
                                <option value="Asia/Makassar" <?= $settings['timezone'] === 'Asia/Makassar' ? 'selected' : '' ?>>WITA (Asia/Makassar)</option>
                                <option value="Asia/Jayapura" <?= $settings['timezone'] === 'Asia/Jayapura' ? 'selected' : '' ?>>WIT (Asia/Jayapura)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_format" class="form-label">Format Tanggal <span class="text-danger">*</span></label>
                            <select class="form-select" id="date_format" name="date_format">
                                <option value="d/m/Y" <?= $settings['date_format'] === 'd/m/Y' ? 'selected' : '' ?>>dd/mm/yyyy (31/12/2026)</option>
                                <option value="Y-m-d" <?= $settings['date_format'] === 'Y-m-d' ? 'selected' : '' ?>>yyyy-mm-dd (2026-12-31)</option>
                                <option value="m/d/Y" <?= $settings['date_format'] === 'm/d/Y' ? 'selected' : '' ?>>mm/dd/yyyy (12/31/2026)</option>
                                <option value="d-M-Y" <?= $settings['date_format'] === 'd-M-Y' ? 'selected' : '' ?>>dd-Mon-yyyy (31-Dec-2026)</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Pengaturan Umum</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 2. INVENTARIS -->
    <!-- ============================================ -->
    <div class="tab-pane fade <?= $activeTab === 'inventory' ? 'show active' : '' ?>" id="inventory" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-box-seam-fill me-2 text-success"></i>Pengaturan Inventaris</h5>
                <p class="text-muted mb-0 small">Atur threshold stok, satuan, dan perilaku otomatis inventaris</p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/settings/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_tab" value="inventory">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="low_stock_threshold" class="form-label">
                                <i class="bi bi-exclamation-triangle text-warning me-1"></i>Batas Stok Rendah <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="<?= esc($settings['low_stock_threshold']) ?>" min="1">
                            <div class="form-text">Produk di bawah jumlah ini ditandai <span class="badge bg-warning text-dark">Stok Rendah</span></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="critical_stock_threshold" class="form-label">
                                <i class="bi bi-exclamation-octagon text-danger me-1"></i>Batas Stok Kritis <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="critical_stock_threshold" name="critical_stock_threshold" value="<?= esc($settings['critical_stock_threshold']) ?>" min="1">
                            <div class="form-text">Produk di bawah jumlah ini ditandai <span class="badge bg-danger">Stok Kritis</span></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="items_per_page" class="form-label">Item Per Halaman <span class="text-danger">*</span></label>
                            <select class="form-select" id="items_per_page" name="items_per_page">
                                <option value="10" <?= $settings['items_per_page'] == 10 ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= $settings['items_per_page'] == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= $settings['items_per_page'] == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $settings['items_per_page'] == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="default_unit" class="form-label">Satuan Default Barang <span class="text-danger">*</span></label>
                            <select class="form-select" id="default_unit" name="default_unit">
                                <?php
                                $units = ['pcs' => 'Pcs (Pieces)', 'box' => 'Box', 'rim' => 'Rim', 'lusin' => 'Lusin', 'pak' => 'Pak', 'set' => 'Set', 'unit' => 'Unit', 'roll' => 'Roll'];
                                foreach ($units as $val => $label):
                                ?>
                                    <option value="<?= $val ?>" <?= $settings['default_unit'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted mb-3"><i class="bi bi-toggles me-1"></i>Fitur Otomatis</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="auto_update_stock" name="auto_update_stock" value="1" <?= $settings['auto_update_stock'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="auto_update_stock">
                                    Auto Update Stok Saat Approve Peminjaman
                                </label>
                            </div>
                            <div class="form-text ms-4">Stok otomatis berkurang saat peminjaman disetujui</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notify_low_stock" name="notify_low_stock" value="1" <?= $settings['notify_low_stock'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="notify_low_stock">
                                    Aktifkan Notifikasi Stok Rendah
                                </label>
                            </div>
                            <div class="form-text ms-4">Kirim notifikasi saat stok di bawah batas</div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Simpan Pengaturan Inventaris</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 3. PEMINJAMAN -->
    <!-- ============================================ -->
    <div class="tab-pane fade <?= $activeTab === 'request' ? 'show active' : '' ?>" id="request" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-event-fill me-2 text-info"></i>Pengaturan Permintaan</h5>
                <p class="text-muted mb-0 small">Atur durasi, batas item, persetujuan, dan denda permintaan</p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/settings/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_tab" value="request">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="request_max_days" class="form-label">Maks. Hari Permintaan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="request_max_days" name="request_max_days" value="<?= esc($settings['request_max_days']) ?>" min="1">
                                <span class="input-group-text">hari</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="request_max_items" class="form-label">Maks. Item per Transaksi <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="request_max_items" name="request_max_items" value="<?= esc($settings['request_max_items']) ?>" min="1">
                                <span class="input-group-text">barang</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="request_late_fee" class="form-label">Denda Keterlambatan per Hari</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="request_late_fee" name="request_late_fee" value="<?= esc($settings['request_late_fee']) ?>" min="0">
                            <span class="input-group-text">/ hari</span>
                        </div>
                        <div class="form-text">Isi 0 jika tidak ada denda</div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted mb-3"><i class="bi bi-toggles me-1"></i>Kebijakan Permintaan</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="request_require_approval" name="request_require_approval" value="1" <?= $settings['request_require_approval'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="request_require_approval">
                                    <i class="bi bi-check2-circle me-1"></i>Perlu Persetujuan Admin
                                </label>
                            </div>
                            <div class="form-text ms-4">Permintaan harus disetujui admin sebelum bisa diambil</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="request_allow_extend" name="request_allow_extend" value="1" <?= $settings['request_allow_extend'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="request_allow_extend">
                                    <i class="bi bi-arrow-repeat me-1"></i>Boleh Perpanjang Permintaan
                                </label>
                            </div>
                            <div class="form-text ms-4">Pemohon bisa mengajukan perpanjangan waktu</div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-info text-white"><i class="bi bi-save me-1"></i>Simpan Pengaturan Permintaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 4. PENGGUNA & AKSES -->
    <!-- ============================================ -->
    <div class="tab-pane fade <?= $activeTab === 'access' ? 'show active' : '' ?>" id="access" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-lock-fill me-2 text-danger"></i>Pengguna & Akses</h5>
                <p class="text-muted mb-0 small">Konfigurasi keamanan, role default, dan audit log</p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/settings/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_tab" value="access">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="default_role" class="form-label">Default Role User Baru <span class="text-danger">*</span></label>
                            <select class="form-select" id="default_role" name="default_role">
                                <option value="user" <?= $settings['default_role'] === 'user' ? 'selected' : '' ?>>User/Pemohon</option>
                                <option value="admin" <?= $settings['default_role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="superadmin" <?= $settings['default_role'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                            </select>
                            <div class="form-text">Role yang otomatis diberikan saat membuat user baru</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="session_timeout" class="form-label">Session Timeout <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="<?= esc($settings['session_timeout']) ?>" min="5">
                                <span class="input-group-text">menit</span>
                            </div>
                            <div class="form-text">User otomatis logout setelah tidak aktif selama waktu ini</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_login_attempts" class="form-label">Maks. Percobaan Login <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" value="<?= esc($settings['max_login_attempts']) ?>" min="1">
                                <span class="input-group-text">kali</span>
                            </div>
                            <div class="form-text">Akun diblokir sementara setelah gagal login sebanyak ini</div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted mb-3"><i class="bi bi-toggles me-1"></i>Fitur Keamanan</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enable_multi_role" name="enable_multi_role" value="1" <?= $settings['enable_multi_role'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enable_multi_role">
                                    <i class="bi bi-people me-1"></i>Aktifkan Multi Role
                                </label>
                            </div>
                            <div class="form-text ms-4">User bisa memiliki lebih dari satu role</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enable_audit_log" name="enable_audit_log" value="1" <?= $settings['enable_audit_log'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enable_audit_log">
                                    <i class="bi bi-journal-text me-1"></i>Aktifkan Log Aktivitas (Audit Log)
                                </label>
                            </div>
                            <div class="form-text ms-4">Catat semua aktivitas user (login, CRUD, dll)</div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger"><i class="bi bi-save me-1"></i>Simpan Pengaturan Akses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 5. NOTIFIKASI -->
    <!-- ============================================ -->
    <div class="tab-pane fade <?= $activeTab === 'notification' ? 'show active' : '' ?>" id="notification" role="tabpanel">
        <div class="card border-top-0 rounded-top-0">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bell-fill me-2 text-warning"></i>Pengaturan Notifikasi</h5>
                <p class="text-muted mb-0 small">Atur kapan dan bagaimana notifikasi dikirim ke pengguna</p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/settings/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_tab" value="notification">

                    <h6 class="text-muted mb-3"><i class="bi bi-envelope me-1"></i>Kirim Email Saat:</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-2 d-block"></i>
                                    <h6>Stok Rendah</h6>
                                    <p class="text-muted small mb-3">Email saat produk mencapai batas stok rendah</p>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="notify_email_low_stock" name="notify_email_low_stock" value="1" <?= $settings['notify_email_low_stock'] ? 'checked' : '' ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-box-arrow-up-right text-info fs-1 mb-2 d-block"></i>
                                    <h6>Permintaan Baru</h6>
                                    <p class="text-muted small mb-3">Email saat ada pengajuan permintaan baru</p>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="notify_email_new_request" name="notify_email_new_request" value="1" <?= $settings['notify_email_new_request'] ? 'checked' : '' ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border h-100">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-clock-history text-danger fs-1 mb-2 d-block"></i>
                                    <h6>Pengembalian Terlambat</h6>
                                    <p class="text-muted small mb-3">Email saat peminjaman melewati batas waktu</p>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="notify_email_overdue" name="notify_email_overdue" value="1" <?= $settings['notify_email_overdue'] ? 'checked' : '' ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted mb-3"><i class="bi bi-display me-1"></i>Notifikasi Dashboard</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notify_dashboard" name="notify_dashboard" value="1" <?= $settings['notify_dashboard'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="notify_dashboard">
                                    <i class="bi bi-bell me-1"></i>Aktifkan Notifikasi Dashboard
                                </label>
                            </div>
                            <div class="form-text ms-4">Tampilkan notifikasi real-time di dashboard admin</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="notify_due_reminder_days" class="form-label">
                                <i class="bi bi-alarm me-1"></i>Reminder Sebelum Jatuh Tempo
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">H -</span>
                                <input type="number" class="form-control" id="notify_due_reminder_days" name="notify_due_reminder_days" value="<?= esc($settings['notify_due_reminder_days']) ?>" min="0">
                                <span class="input-group-text">hari</span>
                            </div>
                            <div class="form-text">Isi 0 untuk menonaktifkan reminder. Contoh: 1 = H-1 sebelum jatuh tempo</div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Simpan Pengaturan Notifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>