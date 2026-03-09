<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Permintaan Terkirim | SIMATIK' ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/static/images/logo/favicon.svg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #f8faff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(59, 91, 219, 0.14);
            max-width: 520px;
            width: 100%;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.2rem;
            color: white;
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.35);
        }

        .ref-badge {
            background: #EDF2FF;
            color: #3B5BDB;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .btn-back {
            background: linear-gradient(135deg, #3B5BDB, #4263EB);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            padding: 0.6rem 2rem;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #2B4ACB, #3B5BDB);
            color: white;
        }

        .brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #3B5BDB;
        }
    </style>
</head>

<body>
    <div class="container px-3">
        <div class="card success-card mx-auto">
            <div class="card-body p-5 text-center">

                <!-- Brand -->
                <div class="brand mb-4">
                    <i class="bi bi-box-seam-fill me-1"></i> SIMATIK
                </div>

                <!-- Icon -->
                <div class="success-icon">
                    <i class="bi bi-check-lg"></i>
                </div>

                <!-- Message -->
                <h3 class="fw-bold mb-2">Permintaan Terkirim!</h3>
                <p class="text-muted mb-4">
                    <?php if (!empty($borrower_name)): ?>
                        Terima kasih, <strong><?= esc($borrower_name) ?></strong>.<br>
                    <?php endif; ?>
                    Permintaan ATK Anda telah berhasil diajukan dan akan segera diproses oleh petugas.
                </p>

                <?php if (!empty($request_id)): ?>
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Nomor Referensi Permintaan</p>
                        <div class="ref-badge">REQ-<?= str_pad($request_id, 4, '0', STR_PAD_LEFT) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Info -->
                <div class="alert border-0 rounded-3 text-start mb-4" style="background:#f1f5f9; font-size:0.875rem;">
                    <i class="bi bi-clock-history me-2 text-primary"></i>
                    Permintaan biasanya diproses dalam <strong>1–2 hari kerja</strong>. Petugas akan menghubungi Anda melalui email jika ada informasi lebih lanjut.
                </div>

                <!-- Actions -->
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <a href="<?= base_url('/ask') ?>" class="btn btn-back">
                        <i class="bi bi-plus-lg me-1"></i> Ajukan Permintaan Baru
                    </a>
                    <a href="<?= base_url('/') ?>" class="btn btn-light border">
                        <i class="bi bi-house me-1"></i> Kembali ke Beranda
                    </a>
                </div>

                <p class="mt-4 mb-0" style="font-size:0.78rem; color:#adb5bd;">
                    &copy; <?= date('Y') ?> SIMATIK &mdash; Sistem Informasi Manajemen ATK
                </p>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
</body>

</html>