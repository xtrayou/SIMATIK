<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Filter Panel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-funnel me-2"></i>Filter Riwayat Stok</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('/stock/history') ?>" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Barang</label>
                        <select name="product" class="form-select select2">
                            <option value="">Semua Barang</option>
                            <?php foreach ($daftarProduk as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $filterProduk == $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['name']) ?> (<?= $p['sku'] ?>)
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Tipe Pergerakan</label>
                        <select name="type" class="form-select">
                            <option value="">Semua</option>
                            <option value="IN" <?= $filterTipe == 'IN' ? 'selected' : '' ?>>Barang Masuk</option>
                            <option value="OUT" <?= $filterTipe == 'OUT' ? 'selected' : '' ?>>Barang Keluar</option>
                            <option value="ADJUSTMENT" <?= $filterTipe == 'ADJUSTMENT' ? 'selected' : '' ?>>Penyesuaian</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Mulai Dari</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $tglMulai ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Sampai Dengan</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $tglSelesai ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        <a href="<?= base_url('/stock/history') ?>" class="btn btn-light border" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Data Tabel -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Daftar Mutasi Stok</h5>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('/stock/in') ?>" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-plus-lg me-1"></i> Barang Masuk
                    </a>
                    <a href="<?= base_url('/stock/out') ?>" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-dash-lg me-1"></i> Barang Keluar
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($daftarMutasi)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="historyTable">
                            <thead class="bg-light small">
                                <tr>
                                    <th class="ps-4">Tgl & Waktu</th>
                                    <th>Produk</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Stok Sisa</th>
                                    <th>Referensi / Ket</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php foreach ($daftarMutasi as $mut): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold"><?= date('d/m/Y', strtotime($mut['created_at'])) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem"><?= date('H:i', strtotime($mut['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($mut['product_name']) ?></div>
                                        <code class="text-muted"><?= $mut['product_sku'] ?></code>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($mut['type'] == 'IN'): ?>
                                            <span class="badge bg-success-light text-success border-success px-2 py-1">MASUK</span>
                                        <?php elseif ($mut['type'] == 'OUT'): ?>
                                            <span class="badge bg-danger-light text-danger border-danger px-2 py-1">KELUAR</span>
                                        <?php else: ?>
                                            <span class="badge bg-info-light text-info border-info px-2 py-1">ADJ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center fw-bold <?= $mut['type'] == 'IN' ? 'text-success' : ($mut['type'] == 'OUT' ? 'text-danger' : 'text-info') ?>">
                                        <?= $mut['type'] == 'IN' ? '+' : ($mut['type'] == 'OUT' ? '-' : '±') ?><?= number_format($mut['quantity']) ?>
                                    </td>
                                    <td class="text-center fw-bold"><?= number_format($mut['current_stock']) ?> <small class="text-muted fw-normal"><?= $mut['unit'] ?></small></td>
                                    <td>
                                        <?php if ($mut['reference_no']): ?>
                                            <div class="badge bg-light text-dark border fw-normal mb-1">Ref: <?= $mut['reference_no'] ?></div>
                                        <?php endif; ?>
                                        <div class="text-muted italic"><?= esc($mut['notes']) ?: '-' ?></div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Tidak ada data riwayat mutasi.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#historyTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[0, 'desc']]
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .bg-info-light { background-color: rgba(13, 202, 240, 0.1); }
    .italic { font-style: italic; }
</style>
<?= $this->endSection() ?>
