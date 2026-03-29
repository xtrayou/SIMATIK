<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0">Tambah Produk Baru</h4>
                <p class="text-muted mb-0">Lengkapi informasi produk dengan detail yang benar</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('/products/store') ?>" method="POST" id="productForm">
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
                                       placeholder="Nama barang / produk"
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
                                <?php if (session('errors.category_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.category_id') ?></div>
                                <?php endif ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sku" class="form-label fw-bold">SKU / Kode Barang <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control <?= (session('errors.sku')) ? 'is-invalid' : '' ?>" 
                                           id="sku" 
                                           name="sku" 
                                           value="<?= old('sku', $produk['sku']) ?>"
                                           placeholder="Contoh: ATK-001"
                                           required>
                                    <?php if (session('errors.sku')): ?>
                                        <div class="invalid-feedback"><?= session('errors.sku') ?></div>
                                    <?php endif ?>
                                </div>
                                <small class="text-muted">Gunakan kode unik untuk identifikasi</small>
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
                                <textarea class="form-control <?= (session('errors.description')) ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="3"
                                          placeholder="Opsional: tambahkan keterangan lengkap"><?= old('description', $produk['description']) ?></textarea>
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
                                           placeholder="0" required>
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
                                           value="<?= old('cost_price', $produk['cost_price']) ?>"
                                           placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="stock_baik" class="form-label fw-bold">Stok Baik</label>
                                <input type="number" class="form-control" id="stock_baik" name="stock_baik" value="<?= old('stock_baik', $produk['stock_baik'] ?? 0) ?>" placeholder="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="stock_rusak" class="form-label fw-bold">Stok Rusak</label>
                                <input type="number" class="form-control" id="stock_rusak" name="stock_rusak" value="<?= old('stock_rusak', $produk['stock_rusak'] ?? 0) ?>" placeholder="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="min_stock" class="form-label fw-bold">Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="min_stock" name="min_stock" value="<?= old('min_stock', $produk['min_stock']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                        <a href="<?= base_url('/products') ?>" class="btn btn-light px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-5" id="btnSubmit">
                            <i class="bi bi-save me-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Panel -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2"></i>Petunjuk</h5>
                <ul class="mb-0 ps-3 small">
                    <li class="mb-2">Gunakan <strong>SKU</strong> yang konsisten agar mudah dalam pencarian.</li>
                    <li class="mb-2"><strong>Stok Minimum</strong> digunakan oleh sistem untuk memberikan notifikasi jika persediaan hampir habis.</li>
                    <li>Harga dapat diupdate sewaktu-waktu jika ada perubahan dari supplier.</li>
                </ul>
            </div>
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Preview Produk</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="display-4"><i class="bi bi-box-seam text-secondary"></i></div>
                </div>
                <h6 id="preview-name" class="text-center fw-bold mb-1">Nama Produk</h6>
                <p id="preview-category" class="text-center text-muted small mb-3">Kategori</p>
                <hr>
                <div class="row text-center small">
                    <div class="col-6">
                        <p class="mb-0 text-muted">Stok</p>
                        <p id="preview-stock" class="fw-bold fs-5">0</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-0 text-muted">Satuan</p>
                        <p id="preview-unit" class="fw-bold fs-5">Pcs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#name, #stock_baik, #stock_rusak, #unit, #category_id').on('input change', function() {
        const name = $('#name').val() || 'Nama Produk';
        const baik = parseInt($('#stock_baik').val()) || 0;
        const rusak = parseInt($('#stock_rusak').val()) || 0;
        const totalStock = baik + rusak;
        const unit = $('#unit').val();
        const category = $('#category_id option:selected').text() || 'Kategori';
        
        $('#preview-name').text(name);
        $('#preview-stock').text(totalStock);
        $('#preview-unit').text(unit);
        $('#preview-category').text(category);
    });

    $('#productForm').on('submit', function() {
        $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
    });
});
</script>
<?= $this->endSection() ?>