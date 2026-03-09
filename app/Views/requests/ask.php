<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Ajukan Permintaan ATK | SIMATIK' ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/static/images/logo/favicon.svg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #f8faff 100%);
            min-height: 100vh;
        }

        .ask-header {
            background: linear-gradient(135deg, #3B5BDB 0%, #4263EB 100%);
            color: white;
            padding: 2rem 0 3.5rem;
        }

        .ask-header .brand {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .ask-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(59, 91, 219, 0.12);
            margin-top: -2rem;
        }

        .section-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #3B5BDB;
            border-bottom: 2px solid #eef2ff;
            padding-bottom: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #343a40;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #3B5BDB, #4263EB);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.65rem 2.5rem;
            letter-spacing: 0.3px;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #2B4ACB, #3B5BDB);
        }

        .item-row {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid #e8ecff;
        }

        .info-banner {
            background: #EDF2FF;
            border-left: 4px solid #3B5BDB;
            border-radius: 0 8px 8px 0;
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            color: #3B5BDB;
        }

        .footer-note {
            font-size: 0.8rem;
            color: #868e96;
        }

        @media (max-width: 576px) {
            .ask-header {
                padding: 1.5rem 0 3rem;
            }
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="ask-header">
        <div class="container">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-box-seam-fill fs-4"></i>
                <span class="brand">SIMA<span style="color:#93b4ff">TIK</span></span>
                <span class="badge bg-white text-primary ms-2" style="font-size:0.75rem;">Sistem Inventaris ATK</span>
            </div>
            <h2 class="fw-bold mb-1">Formulir Permintaan ATK</h2>
            <p class="mb-0 opacity-75">Ajukan kebutuhan alat tulis kantor Anda melalui formulir berikut</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">

                <!-- Info Banner -->
                <div class="info-banner mt-4 mb-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Permintaan akan diproses oleh petugas. Pastikan data yang diisi sudah benar sebelum mengirim.
                </div>

                <!-- Alerts -->
                <?php if (session('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-1">
                            <?php foreach (session('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        <?= esc(session('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="card ask-card">
                    <div class="card-body p-4 p-md-5">
                        <form method="post" action="<?= base_url('/ask/store') ?>" id="askForm">
                            <?= csrf_field() ?>

                            <!-- Data Pemohon -->
                            <div class="mb-4">
                                <div class="section-title"><i class="bi bi-person-fill me-2"></i>Informasi Pemohon</div>
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="borrower_name"
                                            class="form-control <?= session('errors.borrower_name') ? 'is-invalid' : '' ?>"
                                            placeholder="Nama lengkap pemohon"
                                            value="<?= old('borrower_name') ?>" required>
                                        <?php if (session('errors.borrower_name')): ?>
                                            <div class="invalid-feedback"><?= session('errors.borrower_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Unit / Prodi <span class="text-danger">*</span></label>
                                        <input type="text" name="borrower_unit"
                                            class="form-control <?= session('errors.borrower_unit') ? 'is-invalid' : '' ?>"
                                            placeholder="Contoh: Prodi Teknik Informatika"
                                            value="<?= old('borrower_unit') ?>" required>
                                        <?php if (session('errors.borrower_unit')): ?>
                                            <div class="invalid-feedback"><?= session('errors.borrower_unit') ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">NIM / NIP <span class="text-muted fw-normal">(opsional)</span></label>
                                        <input type="text" name="borrower_identifier"
                                            class="form-control"
                                            placeholder="Nomor induk"
                                            value="<?= old('borrower_identifier') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email"
                                            class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                            placeholder="nama@kampus.ac.id"
                                            value="<?= old('email') ?>" required>
                                        <?php if (session('errors.email')): ?>
                                            <div class="invalid-feedback"><?= session('errors.email') ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tanggal Permintaan <span class="text-danger">*</span></label>
                                        <input type="date" name="request_date"
                                            class="form-control" required
                                            value="<?= old('request_date', date('Y-m-d')) ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Barang -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="section-title mb-0"><i class="bi bi-cart-fill me-2"></i>Daftar Barang yang Diminta</div>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="add-item">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah Barang
                                    </button>
                                </div>

                                <?php if (empty($daftarProduk)): ?>
                                    <div class="alert alert-warning border-0 rounded-3">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        Belum ada produk tersedia. Silakan hubungi petugas.
                                    </div>
                                <?php else: ?>
                                    <div id="item-container">
                                        <div class="item-row row g-2 align-items-end">
                                            <div class="col-md-7">
                                                <label class="form-label small">Pilih Produk</label>
                                                <select name="product_id[]" class="form-select select-product" required>
                                                    <option value="">— Pilih Barang —</option>
                                                    <?php foreach ($daftarProduk as $p): ?>
                                                        <option value="<?= $p['id'] ?>"
                                                            data-unit="<?= esc($p['unit']) ?>"
                                                            data-stock="<?= $p['current_stock'] ?>">
                                                            <?= esc($p['name']) ?> &nbsp;·&nbsp; Stok: <?= $p['current_stock'] ?> <?= esc($p['unit']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Jumlah</label>
                                                <div class="input-group">
                                                    <input type="number" name="quantity[]" class="form-control" min="1" value="1" required>
                                                    <span class="input-group-text unit-label small">Pcs</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-danger w-100 remove-item disabled">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Catatan -->
                            <div class="mb-4">
                                <div class="section-title"><i class="bi bi-chat-left-text me-2"></i>Keterangan</div>
                                <label class="form-label">Keperluan / Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Jelaskan tujuan penggunaan atau keterangan tambahan..."><?= old('notes') ?></textarea>
                            </div>

                            <!-- Submit -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <p class="footer-note mb-0">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Data Anda hanya digunakan untuk keperluan administrasi ATK.
                                </p>
                                <button type="submit" class="btn btn-primary btn-primary-custom text-white" id="btnSubmit">
                                    <i class="bi bi-send-fill me-2"></i>Kirim Permintaan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Footer -->
                <p class="text-center footer-note mt-4">
                    &copy; <?= date('Y') ?> SIMATIK &mdash; Sistem Informasi Manajemen ATK
                </p>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Tambah baris barang
            document.getElementById('add-item')?.addEventListener('click', function() {
                const container = document.getElementById('item-container');
                const firstRow = container.querySelector('.item-row');
                const newRow = firstRow.cloneNode(true);

                newRow.querySelector('select').value = '';
                newRow.querySelector('input[type="number"]').value = 1;
                newRow.querySelector('.unit-label').textContent = 'Pcs';
                newRow.querySelector('.remove-item').classList.remove('disabled');

                container.appendChild(newRow);
                updateRemoveButtons();
            });

            // Hapus baris
            document.getElementById('item-container')?.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-item');
                if (!btn || btn.classList.contains('disabled')) return;
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    btn.closest('.item-row').remove();
                    updateRemoveButtons();
                }
            });

            function updateRemoveButtons() {
                const rows = document.querySelectorAll('.item-row');
                rows.forEach(function(row) {
                    const btn = row.querySelector('.remove-item');
                    if (rows.length <= 1) {
                        btn.classList.add('disabled');
                    } else {
                        btn.classList.remove('disabled');
                    }
                });
            }

            // Update label satuan saat produk dipilih
            document.getElementById('item-container')?.addEventListener('change', function(e) {
                if (e.target.classList.contains('select-product')) {
                    const selected = e.target.options[e.target.selectedIndex];
                    const unit = selected.dataset.unit || 'Pcs';
                    e.target.closest('.item-row').querySelector('.unit-label').textContent = unit;
                }
            });

            // Loading state saat submit
            document.getElementById('askForm')?.addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmit');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            });
        });
    </script>
</body>

</html>