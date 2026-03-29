<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Filter & Pencarian -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-funnel text-primary me-2"></i> Filter & Pencarian</h5>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="search" class="form-label fw-bold">Cari Produk</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= $filterCari ?>" placeholder="Ketik nama, SKU, atau deskripsi...">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="category" class="form-label fw-bold">Kategori</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($daftarKategori as $kat): ?>
                                    <option value="<?= $kat['id'] ?>" 
                                            <?= $filterKategori == $kat['id'] ? 'selected' : '' ?>>
                                        <?= esc($kat['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="stock_status" class="form-label fw-bold">Status Stok</label>
                            <select class="form-select" id="stock_status" name="stock_status">
                                <option value="">Semua Status</option>
                                <option value="normal" <?= $filterStok == 'normal' ? 'selected' : '' ?>>Normal</option>
                                <option value="rendah" <?= $filterStok == 'rendah' ? 'selected' : '' ?>>Stok Rendah</option>
                                <option value="habis" <?= $filterStok == 'habis' ? 'selected' : '' ?>>Stok Habis</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <div class="w-100">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-search me-1"></i> Cari
                                </button>
                                <a href="<?= base_url('/products') ?>" class="btn btn-light w-100">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Produk -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h4 class="mb-0">Daftar Produk</h4>
                    <small class="text-muted">Ditemukan <?= number_format($totalItem) ?> item</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('/products/create') ?>" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Produk
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('/products/export/excel') ?>"><i class="bi bi-file-earmark-excel text-success me-2"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/products/export/pdf') ?>"><i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('sukses')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('sukses') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($daftarProduk)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="productsTable">
                            <thead class="bg-light text-uppercase small fw-bold">
                                <tr>
                                    <th rowspan="2" width="50" class="align-middle border text-center">#</th>
                                    <th rowspan="2" class="align-middle border">Informasi Produk</th>
                                    <th rowspan="2" class="align-middle border text-center">Kategori</th>
                                    <th colspan="3" class="text-center border py-2">Hasil Stock Opname (Jumlah)</th>
                                    <th colspan="2" class="text-center border py-2">Nilai Persediaan</th>
                                    <th rowspan="2" width="130" class="text-center align-middle border">Aksi</th>
                                </tr>
                                <tr class="text-center small">
                                    <th class="border bg-white text-dark" width="70">Baik</th>
                                    <th class="border bg-white text-dark" width="70">Rusak</th>
                                    <th class="border bg-light text-primary" width="80">Total</th>
                                    <th class="border bg-white text-dark" width="120">Harga</th>
                                    <th class="border bg-light text-success" width="130">Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daftarProduk as $idx => $p): ?>
                                <?php 
                                    $totalValue = (float)$p['price'] * (int)$p['current_stock'];
                                    $hasLowStock = $p['current_stock'] <= $p['min_stock'];
                                ?>
                                <tr>
                                    <td class="text-center border-start border-end"><?= $idx + 1 ?></td>
                                    <td class="border-end">
                                        <div class="fw-bold text-dark mb-0"><?= esc($p['name']) ?></div>
                                        <div class="d-flex align-items-center gap-2">
                                            <code class="small text-muted"><?= $p['sku'] ?></code>
                                            <span class="text-muted small">| <?= esc($p['unit'] ?: 'Pcs') ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center border-end">
                                        <span class="badge bg-light text-dark border-0 small"><?= esc($p['category_name']) ?></span>
                                    </td>
                                    <td class="text-center border-end"><?= number_format($p['stock_baik'] ?? 0) ?></td>
                                    <td class="text-center border-end"><?= number_format($p['stock_rusak'] ?? 0) ?></td>
                                    <td class="text-center border-end fw-bold <?= $hasLowStock ? 'text-danger' : 'text-primary' ?>">
                                        <?= number_format($p['current_stock']) ?>
                                        <?php if($hasLowStock && $p['current_stock'] > 0): ?>
                                            <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Stok Rendah"></i>
                                        <?php elseif($p['current_stock'] <= 0): ?>
                                            <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">HABIS</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3 border-end">Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                                    <td class="text-end pe-3 border-end fw-bold text-success">Rp <?= number_format($totalValue, 0, ',', '.') ?></td>
                                    <td class="text-center border-end">
                                        <div class="btn-group">
                                            <a href="<?= base_url('/products/show/' . $p['id']) ?>" class="btn btn-sm btn-outline-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('/products/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                    data-id="<?= $p['id'] ?>" data-name="<?= esc($p['name']) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <img src="<?= base_url('assets/img/empty.svg') ?>" alt="Kosong" style="width: 150px;" class="mb-3 opacity-50">
                        <h5 class="text-muted">Data produk tidak ditemukan</h5>
                        <p class="text-muted">Coba ubah kata kunci pencarian atau filter Anda.</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        if (confirm(`Apakah Anda yakin ingin menghapus produk "${name}"?`)) {
            $.ajax({
                url: `<?= base_url('/products/delete/') ?>/${id}`,
                type: 'DELETE',
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: function(err) {
                    alert('Gagal menghapus produk. Terjadi kesalahan server.');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>