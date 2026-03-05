<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="row">
    <!-- Header Produk -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl bg-primary-light text-primary me-4 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-box-seam fs-1"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1"><?= esc($produk['name']) ?></h2>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border"><?= esc($produk['category_name']) ?></span>
                                <code class="bg-light px-2 py-1 rounded text-primary fw-bold"><?= $produk['sku'] ?></code>
                                <span class="text-muted small">• <?= $produk['unit'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('/products/edit/' . $produk['id']) ?>" class="btn btn-warning px-4 text-white">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <a href="<?= base_url('/products') ?>" class="btn btn-light border px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4 text-center">
                        <p class="text-muted mb-2 fw-bold text-uppercase small">Sisa Stok Saat Ini</p>
                        <h1 class="display-4 fw-bold mb-0 <?= $produk['current_stock'] <= $produk['min_stock'] ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($produk['current_stock']) ?>
                        </h1>
                        <p class="mb-0 text-muted"><?= $produk['unit'] ?></p>
                        <hr>
                        <div class="row small">
                            <div class="col-6 border-end">
                                <span class="text-muted">Min. Stok</span>
                                <p class="fw-bold mb-0"><?= $produk['min_stock'] ?></p>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">Harga</span>
                                <p class="fw-bold mb-0">Rp <?= number_format($produk['price'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-primary">Ringkasan Mutasi</h6>
                        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                            <span>Total Barang Masuk:</span>
                            <span class="fw-bold text-success">+ <?= number_format($statistik['total_masuk']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                            <span>Total Barang Keluar:</span>
                            <span class="fw-bold text-danger">- <?= number_format($statistik['total_keluar']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Net Mutasi:</span>
                            <span class="fw-bold"><?= number_format($statistik['total_masuk'] - $statistik['total_keluar']) ?></span>
                        </div>
                        <div class="mt-4 d-grid gap-2">
                            <a href="<?= base_url('/stock/in?product='.$produk['id']) ?>" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-arrow-down-circle me-1"></i> Input Stok Masuk
                            </a>
                            <a href="<?= base_url('/stock/out?product='.$produk['id']) ?>" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-arrow-up-circle me-1"></i> Input Stok Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Mutasi -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">10 Mutasi Terakhir</h6>
                        <a href="<?= base_url('/stock/history?product='.$produk['id']) ?>" class="btn btn-sm btn-light border small">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if(!empty($riwayatStok)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="bg-light small">
                                        <tr>
                                            <th class="ps-4">Tanggal</th>
                                            <th>Tipe</th>
                                            <th>Jumlah</th>
                                            <th class="text-center">Sisa Stok</th>
                                            <th class="pe-4">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        <?php foreach($riwayatStok as $mut): ?>
                                            <tr>
                                                <td class="ps-4 text-muted"><?= date('d/m/Y H:i', strtotime($mut['created_at'])) ?></td>
                                                <td>
                                                    <?php if($mut['type'] == 'IN'): ?>
                                                        <span class="badge bg-success-light text-success border-success small">MASUK</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-light text-danger border-danger small">KELUAR</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="fw-bold <?= $mut['type'] == 'IN' ? 'text-success' : 'text-danger' ?>">
                                                    <?= $mut['type'] == 'IN' ? '+' : '-' ?><?= number_format($mut['quantity']) ?>
                                                </td>
                                                <td class="text-center fw-bold"><?= number_format($mut['current_stock'] ?? 0) ?></td>
                                                <td class="pe-4 italic text-muted"><?= esc($mut['notes']) ?: '-' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted italic">Belum ada riwayat mutasi stok.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Detail Lainnya -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Data Lengkap</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="text-muted small d-block mb-1">Deskripsi Produk</label>
                    <p class="mb-0"><?= esc($produk['description']) ?: '<span class="text-muted italic">Tidak ada deskripsi.</span>' ?></p>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="text-muted small d-block mb-1">HPP (Beli)</label>
                        <p class="fw-bold mb-0">Rp <?= number_format($produk['cost_price'], 0, ',', '.') ?></p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small d-block mb-1">Harga Jual</label>
                        <p class="fw-bold mb-0">Rp <?= number_format($produk['price'], 0, ',', '.') ?></p>
                    </div>
                    <?php if($produk['price'] > 0): ?>
                    <div class="col-12">
                        <?php $margin = (($produk['price'] - $produk['cost_price']) / $produk['price']) * 100; ?>
                        <div class="p-2 border rounded bg-light text-center">
                            <small class="text-muted d-block">Margin Keuntungan</small>
                            <span class="fw-bold text-success fs-5"><?= number_format($margin, 1) ?>%</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .bg-primary-light { background-color: rgba(67, 94, 190, 0.1); }
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .italic { font-style: italic; }
</style>
<?= $this->endSection() ?>