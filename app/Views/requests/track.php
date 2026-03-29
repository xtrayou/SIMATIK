<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Lacak Permintaan ATK | SIMATIK' ?></title>

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
            padding: 2rem 1rem;
        }

        .track-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(59, 91, 219, 0.14);
            max-width: 520px;
            width: 100%;
            background: white;
        }

        .track-header {
            background: linear-gradient(135deg, #3B5BDB 0%, #4263EB 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }

        .track-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
        }

        .track-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0.3rem;
        }

        .track-header p {
            font-size: 0.95rem;
            opacity: 0.95;
            margin: 0;
        }

        .track-body {
            padding: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.6rem;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3B5BDB;
            box-shadow: 0 0 0 0.2rem rgba(59, 91, 219, 0.15);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group:last-child {
            margin-bottom: 0.5rem;
        }

        .btn-track {
            background: linear-gradient(135deg, #3B5BDB, #4263EB);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            padding: 0.75rem 1.5rem;
            width: 100%;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-track:hover {
            background: linear-gradient(135deg, #2B4ACB, #3B5BDB);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 91, 219, 0.35);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            pointer-events: none;
        }

        .input-icon .form-control {
            padding-right: 2.5rem;
        }

        .info-box {
            background: #f1f5f9;
            border-left: 4px solid #3B5BDB;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #495057;
        }

        .info-box i {
            color: #3B5BDB;
            margin-right: 0.5rem;
        }

        .alert {
            border: none;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .alert-danger {
            background: #fff5f5;
            color: #721c24;
        }

        .btn-back {
            text-align: center;
            display: block;
            font-size: 0.9rem;
            color: #3B5BDB;
            text-decoration: none;
            margin-top: 1rem;
        }

        .btn-back:hover {
            text-decoration: underline;
        }

        .brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #3B5BDB;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="card track-card mx-auto">
            <!-- Header -->
            <div class="track-header">
                <div class="track-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h2>Lacak Permintaan</h2>
                <p>Pantau status permintaan ATK Anda</p>
            </div>

            <!-- Body -->
            <div class="track-body">
                <!-- Brand -->
                <div class="brand text-center mb-3">
                    <i class="bi bi-box-seam-fill me-1"></i> SIMATIK
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <i class="bi bi-info-circle"></i>
                    Masukkan nomor referensi dan email untuk melacak status permintaan Anda.
                </div>

                <!-- Pesan Error -->
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="<?= base_url('track-status') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label" for="reference_no">
                            <i class="bi bi-hash me-1"></i> Nomor Referensi
                        </label>
                        <div class="input-icon">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="reference_no"
                                name="reference_no"
                                placeholder="Contoh: REQ-0001"
                                value="<?= old('reference_no') ?>"
                                required
                                pattern="^REQ-\d+$"
                            >
                            <i class="bi bi-search"></i>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Format: REQ-#### (cek di email konfirmasi Anda)
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">
                            <i class="bi bi-envelope me-1"></i> Email
                        </label>
                        <div class="input-icon">
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email"
                                name="email"
                                placeholder="Masukkan email Anda"
                                value="<?= old('email') ?>"
                                required
                            >
                            <i class="bi bi-envelope-check"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-track mt-3">
                        <i class="bi bi-search me-2"></i> Lacak Sekarang
                    </button>
                </form>

                <!-- Back Button -->
                <a href="<?= base_url('/') ?>" class="btn-back">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                </a>

                <p class="mt-4 mb-0 text-center" style="font-size:0.78rem; color:#adb5bd;">
                    &copy; <?= date('Y') ?> SIMATIK &mdash; Sistem Informasi Manajemen ATK
                </p>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
</body>

</html>
