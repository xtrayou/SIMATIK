<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Alert Statistics -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card shadow-sm border-0 bg-danger text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-white-50 mb-2 d-block">Stok Habis</span>
                        <span class="fw-bold fs-3"><?= $stats['out_of_stock'] ?? 0 ?></span>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-x-circle fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card shadow-sm border-0 bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-white-50 mb-2 d-block">Stok Rendah</span>
                        <span class="fw-bold fs-3"><?= $stats['low_stock'] ?? 0 ?></span>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-triangle fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card shadow-sm border-0 bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-white-50 mb-2 d-block">Stok Aman</span>
                        <span class="fw-bold fs-3"><?= $stats['normal_stock'] ?? 0 ?></span>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Stok Habis -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-danger text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-x-circle me-2"></i>Stok Habis (Kosong)</h5>
                <button class="btn btn-sm btn-outline-light" onclick="refreshAlerts()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($stokHabis)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small">
                                <tr>
                                    <th class="ps-3">Nama Produk</th>
                                    <th>SKU</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stokHabis as $p): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold"><?= esc($p['name']) ?></td>
                                        <td><code><?= $p['sku'] ?></code></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('/stock/in?product=' . $p['id']) ?>" class="btn btn-sm btn-success">
                                                <i class="bi bi-plus-lg"></i> Isi Stok
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check2-circle fs-1 text-success"></i>
                        <p class="mt-2">Tidak ada produk dengan stok habis.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stok Rendah -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Stok Rendah (Menipis)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($stokRendah)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small">
                                <tr>
                                    <th class="ps-3">Nama Produk</th>
                                    <th class="text-center">Stok Sisa</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stokRendah as $p): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold">
                                            <?= esc($p['name']) ?>
                                            <div class="text-muted small">Min: <?= $p['min_stock'] ?> <?= $p['unit'] ?></div>
                                        </td>
                                        <td class="text-center fw-bold text-danger">
                                            <?= number_format($p['current_stock']) ?> <small><?= $p['unit'] ?></small>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('/stock/in?product=' . $p['id']) ?>" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-plus-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-shield-check fs-1 text-primary"></i>
                        <p class="mt-2">Semua stok berada dalam level aman.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function refreshAlerts() {
        location.reload();
    }
</script>
<?= $this->endSection() ?>