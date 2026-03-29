<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0">Edit Produk</h4>
                <p class="text-muted mb-0">Memperbarui informasi untuk produk: <strong><?= esc($produk['name']) ?></strong></p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('/products/update/' . $produk['id']) ?>" method="POST" id="productForm">
                    <?= csrf_field() ?>
                    
                    <!-- Informasi Dasar -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">
                            <i class="bi bi-info-circle me-2"></i>Informasi Dasar
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label for="name" class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= (session('errors.name')) ? 'is-invalid' : '' ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?= old('name', $produk['name']) ?>"
                                       required>
                                <?php if (session('errors.name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.name') ?></div>
                                <?php endif ?>
                            </div>
                            
                            <div class="col-md-5 mb-3">
                                <label for="category_id" class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select <?= (session('errors.category_id')) ? 'is-invalid' : '' ?>" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($daftarKategori as $kat): ?>
                                        <option value="<?= $kat['id'] ?>" 
                                                <?= old('category_id', $produk['category_id']) == $kat['id'] ? 'selected' : '' ?>>
                                            <?= esc($kat['name']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sku" class="form-label fw-bold">SKU / Kode Barang <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= (session('errors.sku')) ? 'is-invalid' : '' ?>" 
                                       id="sku" 
                                       name="sku" 
                                       value="<?= old('sku', $produk['sku']) ?>"
                                       required>
                                <?php if (session('errors.sku')): ?>
                                    <div class="invalid-feedback"><?= session('errors.sku') ?></div>
                                <?php endif ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="unit" class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="Pcs" <?= old('unit', $produk['unit']) == 'Pcs' ? 'selected' : '' ?>>Pcs</option>
                                    <option value="Box" <?= old('unit', $produk['unit']) == 'Box' ? 'selected' : '' ?>>Box</option>
                                    <option value="Pack" <?= old('unit', $produk['unit']) == 'Pack' ? 'selected' : '' ?>>Pack</option>
                                    <option value="Lusin" <?= old('unit', $produk['unit']) == 'Lusin' ? 'selected' : '' ?>>Lusin</option>
                                    <option value="Kg" <?= old('unit', $produk['unit']) == 'Kg' ? 'selected' : '' ?>>Kilogram</option>
                                    <option value="Liter" <?= old('unit', $produk['unit']) == 'Liter' ? 'selected' : '' ?>>Liter</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label fw-bold">Keterangan / Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $produk['description']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Harga & Stok -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2 text-primary">
                            <i class="bi bi-wallet2 me-2"></i>Harga & Stok
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label fw-bold">Harga Estimasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control <?= (session('errors.price')) ? 'is-invalid' : '' ?>" 
                                           id="price" 
                                           name="price" 
                                           value="<?= old('price', $produk['price']) ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="cost_price" class="form-label fw-bold">Harga Beli (HPP)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="cost_price" 
                                           name="cost_price" 
                                           value="<?= old('cost_price', $produk['cost_price']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="stock_baik" class="form-label fw-bold">Stok Baik</label>
                                <input type="number" class="form-control" id="stock_baik" name="stock_baik" value="<?= old('stock_baik', $produk['stock_baik']) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="stock_rusak" class="form-label fw-bold">Stok Rusak</label>
                                <input type="number" class="form-control" id="stock_rusak" name="stock_rusak" value="<?= old('stock_rusak', $produk['stock_rusak']) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="min_stock" class="form-label fw-bold">Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="min_stock" name="min_stock" value="<?= old('min_stock', $produk['min_stock']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                        <a href="<?= base_url('/products') ?>" class="btn btn-light px-4">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-warning px-5 text-white" id="btnSubmit">
                            <i class="bi bi-save me-1"></i> Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Statistics -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Ringkasan Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3 text-center">
                    <?php if($produk['current_stock'] <= 0): ?>
                        <span class="badge bg-danger p-2 fs-6">Stok Habis</span>
                    <?php elseif($produk['current_stock'] <= $produk['min_stock']): ?>
                        <span class="badge bg-warning p-2 fs-6">Stok Rendah</span>
                    <?php else: ?>
                        <span class="badge bg-success p-2 fs-6">Stok Tersedia</span>
                    <?php endif; ?>
                </div>
                <hr>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted">ID Produk:</span>
                    <span class="fw-bold">#<?= $produk['id'] ?></span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted">Dibuat Pada:</span>
                    <span><?= date('d/m/Y H:i', strtotime($produk['created_at'])) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Update Terakhir:</span>
                    <span><?= date('d/m/Y H:i', strtotime($produk['updated_at'])) ?></span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('/products/show/'.$produk['id']) ?>" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-eye me-1"></i> Lihat Detail
                    </a>
                    <a href="<?= base_url('/stock/history?product='.$produk['id']) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-clock-history me-1"></i> Riwayat Mutasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#productForm').on('submit', function() {
        $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memperbarui...');
    });
});
</script>
<?= $this->endSection() ?>