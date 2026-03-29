<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Hasil Lacak Permintaan | SIMATIK' ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/static/images/logo/favicon.svg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #f8faff 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .result-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(59, 91, 219, 0.12);
            margin-bottom: 2rem;
            background: white;
        }

        .card-header {
            background: linear-gradient(135deg, #3B5BDB 0%, #4263EB 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            border: none;
        }

        .card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .status-timeline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 1.5rem;
            background: #f8fafb;
            border-radius: 0 0 16px 16px;
        }

        .timeline-step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .timeline-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 30px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }

        .timeline-step.active:not(:last-child)::after {
            background: #27ae60;
        }

        .step-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #e9ecef;
            color: #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 1.3rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .timeline-step.active .step-circle {
            background: #27ae60;
            color: white;
        }

        .timeline-step.completed .step-circle {
            background: #27ae60;
            color: white;
        }

        .step-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
        }

        .timeline-step.active .step-label,
        .timeline-step.completed .step-label {
            color: #27ae60;
        }

        .status-badge {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .info-section {
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .info-section:last-child {
            border-bottom: none;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 500;
        }

        .items-section {
            padding: 1.5rem;
        }

        .items-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .item-card {
            background: #f8fafb;
            border-left: 4px solid #3B5BDB;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .item-detail {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .item-qty {
            text-align: right;
            font-weight: 700;
            color: #3B5BDB;
            font-size: 1.1rem;
        }

        .btn-group-bottom {
            display: flex;
            gap: 0.5rem;
            padding: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-back, .btn-track-new {
            flex: 1;
            min-width: 150px;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-back {
            background: #e9ecef;
            color: #495057;
            border: 2px solid #dee2e6;
        }

        .btn-back:hover {
            background: #dee2e6;
            color: #2c3e50;
        }

        .btn-track-new {
            background: linear-gradient(135deg, #3B5BDB, #4263EB);
            color: white;
            border: none;
        }

        .btn-track-new:hover {
            background: linear-gradient(135deg, #2B4ACB, #3B5BDB);
            color: white;
        }

        .brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #3B5BDB;
            margin-bottom: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
        }

        .empty-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Brand -->
        <div class="brand text-center mb-3">
            <i class="bi bi-box-seam-fill me-1"></i> SIMATIK
        </div>

        <!-- Header Info -->
        <div class="result-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="bi bi-geo-alt me-2"></i> Detail Permintaan Anda
                </h5>
            </div>

            <!-- Status Timeline -->
            <div class="status-timeline">
                <?php
                $steps = [
                    'requested' => ['label' => 'Menunggu', 'show' => true],
                    'approved' => ['label' => 'Disetujui', 'show' => true],
                    'distributed' => ['label' => 'Dikirim', 'show' => true],
                ];
                $currentStatus = $permintaan['status'];
                $statusOrder = ['requested', 'approved', 'distributed', 'cancelled'];
                $currentIndex = array_search($currentStatus, $statusOrder);
                ?>

                <?php foreach ($steps as $status => $step): ?>
                    <?php
                    $stepIndex = array_search($status, $statusOrder);
                    $isCompleted = $stepIndex < $currentIndex;
                    $isActive = $status === $currentStatus;
                    $isCancelled = $currentStatus === 'cancelled';
                    ?>

                    <div class="timeline-step <?= $isCompleted || $isActive ? 'active' : ''; ?> <?= $isCompleted ? 'completed' : ''; ?>">
                        <div class="step-circle">
                            <?php if ($isCompleted): ?>
                                <i class="bi bi-check-lg"></i>
                            <?php else: ?>
                                <?= $stepIndex + 1 ?>
                            <?php endif; ?>
                        </div>
                        <div class="step-label"><?= $step['label'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Current Status -->
            <div style="padding: 1.5rem; text-align: center; border-top: 1px solid #e9ecef;">
                <?php
                $badge = $statusBadges[$currentStatus] ?? ['text' => 'Tidak Diketahui', 'color' => 'secondary', 'icon' => 'question-circle'];
                ?>
                <div class="status-badge badge-<?= $badge['color'] ?>">
                    <i class="bi bi-<?= $badge['icon'] ?> me-1"></i> <?= $badge['text'] ?>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    <i class="bi bi-calendar3 me-1"></i>
                    Diajukan: <strong><?= formatDate($permintaan['created_at']) ?></strong>
                </p>
            </div>
        </div>

        <!-- Detail Informasi -->
        <div class="result-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="bi bi-info-circle me-2"></i> Informasi Permintaan
                </h5>
            </div>

            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Nomor Referensi</span>
                    <span class="info-value"><?= $referenceNo ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama Pemohon</span>
                    <span class="info-value"><?= esc($permintaan['borrower_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Unit/Divisi</span>
                    <span class="info-value"><?= esc($permintaan['borrower_unit']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= esc($permintaan['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Permintaan</span>
                    <span class="info-value"><?= formatDate($permintaan['request_date']) ?></span>
                </div>
                <?php if (!empty($permintaan['notes'])): ?>
                    <div class="info-row">
                        <span class="info-label">Catatan</span>
                        <span class="info-value"><?= nl2br(esc($permintaan['notes'])) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Item -->
        <?php if (!empty($itemPermintaan)): ?>
            <div class="result-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-box-seam me-2"></i> Item Permintaan (<?= count($itemPermintaan) ?>)
                    </h5>
                </div>

                <div class="items-section">
                    <?php foreach ($itemPermintaan as $idx => $data): ?>
                        <div class="item-card">
                            <div class="item-info">
                                <div class="item-name">
                                    <?php if ($data['produk']): ?>
                                        <?= esc($data['produk']['name']) ?>
                                    <?php else: ?>
                                        <em class="text-muted">Produk tidak ditemukan</em>
                                    <?php endif; ?>
                                </div>
                                <div class="item-detail">
                                    <?php if ($data['produk']): ?>
                                        <span class="badge bg-light text-dark"><?= esc($data['produk']['sku']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="item-qty">
                                <?= $data['item']['quantity'] ?> Unit
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Info Box -->
        <div class="alert alert-info border-0 rounded" style="background: #d1ecf1; color: #0c5460; padding: 1rem 1.25rem;">
            <i class="bi bi-info-circle me-2"></i>
            Untuk pertanyaan lebih lanjut, silakan hubungi kami melalui email atau datang langsung ke kantor kami.
        </div>

        <!-- Action Buttons -->
        <div class="btn-group-bottom">
            <a href="<?= base_url('track') ?>" class="btn-track-new">
                <i class="bi bi-search me-1"></i> Lacak Permintaan Lain
            </a>
            <a href="<?= base_url('/') ?>" class="btn-back">
                <i class="bi bi-house me-1"></i> Kembali ke Beranda
            </a>
        </div>

        <p class="mt-4 mb-4 text-center" style="font-size:0.78rem; color:#adb5bd;">
            &copy; <?= date('Y') ?> SIMATIK &mdash; Sistem Informasi Manajemen ATK
        </p>
    </div>

    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
</body>

</html>
