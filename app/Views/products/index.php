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
                            <thead class="bg-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Informasi Produk</th>
                                    <th>Kategori</th>
                                    <th>SKU</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daftarProduk as $idx => $p): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="fw-bold text-primary"><?= esc($p['name']) ?></div>
                                        <small class="text-muted"><?= $p['unit'] ?> | Ref: <?= $p['sku'] ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= esc($p['category_name']) ?></span>
                                    </td>
                                    <td><code><?= $p['sku'] ?></code></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-2 <?= $p['current_stock'] <= $p['min_stock'] ? 'text-danger' : 'text-success' ?>">
                                                <?= number_format($p['current_stock']) ?>
                                            </span>
                                            <?php if($p['current_stock'] <= $p['min_stock']): ?>
                                                <i class="bi bi-exclamation-triangle-fill text-warning" title="Stok Rendah"></i>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted d-block">Min: <?= $p['min_stock'] ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="<?= base_url('/products/show/' . $p['id']) ?>" class="btn btn-sm btn-info text-white" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('/products/edit/' . $p['id']) ?>" class="btn btn-sm btn-warning text-white" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete" 
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