<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12 col-lg-9 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 fw-bold">Buat Permintaan ATK Baru</h4>
                <p class="text-muted small mb-0">Input data permintaan barang dari unit kerja atau prodi</p>
            </div>
            <div class="card-body p-4">
                <form method="post" action="<?= base_url('/requests/store') ?>" id="requestForm">
                    <?= csrf_field() ?>

                    <!-- Data Pemohon -->
                    <div class="mb-5">
                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person me-2"></i>Informasi Pemohon</h6>
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="borrower_name" class="form-control <?= (session('errors.borrower_name')) ? 'is-invalid' : '' ?>"
                                    placeholder="Nama pemohon" required value="<?= old('borrower_name') ?>">
                                <?php if (session('errors.borrower_name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.borrower_name') ?></div>
                                <?php endif ?>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Unit / Prodi <span class="text-danger">*</span></label>
                                <input type="text" name="borrower_unit" class="form-control <?= (session('errors.borrower_unit')) ? 'is-invalid' : '' ?>"
                                    placeholder="Contoh: Prodi Teknik Informatika" required value="<?= old('borrower_unit') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">NIM / NIP</label>
                                <input type="text" name="borrower_identifier" class="form-control"
                                    placeholder="Opsional" value="<?= old('borrower_identifier') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control <?= (session('errors.email')) ? 'is-invalid' : '' ?>"
                                    placeholder="nama@fasilkom.ac.id" required value="<?= old('email') ?>">
                                <?php if (session('errors.email')): ?>
                                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                                <?php endif ?>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tgl Permintaan</label>
                                <input type="date" name="request_date" class="form-control" required value="<?= old('request_date', date('Y-m-d')) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Barang yang Diminta -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-cart me-2"></i>Daftar Barang</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-item">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Item
                            </button>
                        </div>

                        <div id="item-container">
                            <div class="row g-2 align-items-end mb-3 item-row">
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold">Pilih Produk</label>
                                    <select name="product_id[]" class="form-select select-product" required>
                                        <option value="">- Cari Barang -</option>
                                        <?php foreach ($daftarProduk as $p): ?>
                                            <option value="<?= $p['id'] ?>" data-unit="<?= $p['unit'] ?>" data-stock="<?= $p['current_stock'] ?>">
                                                <?= esc($p['name']) ?> (Stok: <?= $p['current_stock'] ?> <?= $p['unit'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Jumlah</label>
                                    <div class="input-group">
                                        <input type="number" name="quantity[]" class="form-control" min="1" value="1" required>
                                        <span class="input-group-text small-text unit-label">Pcs</span>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-item disabled">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Catatan / Keperluan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Tujuan permintaan barang..."><?= old('notes') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <a href="<?= base_url('/requests') ?>" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold" id="btnSubmit">
                            Kirim Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Tambah Baris
        $('#add-item').on('click', function() {
            const firstRow = $('.item-row').first();
            const newRow = firstRow.clone();

            newRow.find('select').val('');
            newRow.find('input').val(1);
            newRow.find('.unit-label').text('Pcs');
            newRow.find('.remove-item').removeClass('disabled');

            $('#item-container').append(newRow);
            updateRemoveButtons();
        });

        // Hapus Baris
        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            if ($('.item-row').length <= 1) {
                $('.remove-item').addClass('disabled');
            } else {
                $('.remove-item').removeClass('disabled');
            }
        }

        // Update Label Satuan
        $(document).on('change', '.select-product', function() {
            const unit = $(this).find('option:selected').data('unit') || 'Pcs';
            $(this).closest('.item-row').find('.unit-label').text(unit);
        });

        // Form Loading
        $('#requestForm').on('submit', function() {
            $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .small-text {
        font-size: 0.8rem;
    }
</style>
<?= $this->endSection() ?>