<?= $this->extend('layouts/app') ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/categories') ?>">Kategori</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Tambah Kategori Baru</h4>
            </div>

            <div class="card-body">
                <form action="<?= base_url('/categories/store') ?>" method="POST" id="categoryForm">
                    <?= csrf_field() ?>

                    <!-- Nama -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>"
                            id="name"
                            name="name"
                            value="<?= old('name', $kategori['name'] ?? '') ?>"
                            placeholder="Masukkan nama kategori"
                            required
                            autofocus>
                        <?php if (session('errors.name')): ?>
                            <div class="invalid-feedback"><?= session('errors.name') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea
                            class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>"
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Deskripsi kategori (opsional)"><?= old('description', $category['description'] ?? '') ?></textarea>
                        <?php if (session('errors.description')): ?>
                            <div class="invalid-feedback"><?= session('errors.description') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Aktif -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <?php
                            $aktifDefault = (bool) ($kategori['is_active'] ?? true);
                            $aktif = old('is_active', $aktifDefault) ? true : false;
                            ?>
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                <?= $aktif ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                Kategori Aktif
                            </label>
                        </div>
                        <small class="text-muted">
                            Kategori aktif akan ditampilkan dalam pilihan saat membuat produk.
                        </small>
                    </div>

                    <!-- Tombol -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('/categories') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bi bi-save"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Panel Kanan -->
    <div class="col-12 col-lg-4">
        <!-- Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <h6 class="alert-heading">Tips Kategori</h6>
                    <ul class="mb-0">
                        <li>Gunakan nama yang jelas dan mudah dipahami</li>
                        <li>Nama kategori harus unik</li>
                        <li>Deskripsi membantu penjelasan lebih detail</li>
                        <li>Kategori nonaktif tidak akan muncul dalam pilihan</li>
                    </ul>
                </div>

                <div class="alert alert-warning mb-0">
                    <h6 class="alert-heading">Perhatian</h6>
                    <p class="mb-0">Kategori yang sudah digunakan oleh produk tidak bisa dihapus.</p>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-eye"></i> Preview</h5>
            </div>
            <div class="card-body">
                <div class="preview-kategori">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar-kecil me-3">
                            <div class="avatar-isi bg-primary text-white">
                                <i class="bi bi-collection-fill"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0" id="preview-name">
                                <?= old('name', $kategori['name'] ?? 'Nama Kategori') ?: 'Nama Kategori' ?>
                            </h6>
                            <small class="text-muted">Baru dibuat</small>
                        </div>
                    </div>

                    <p class="mb-2" id="preview-description">
                        <?php
                        $desc = old('description', $kategori['description'] ?? '');
                        echo trim($desc) !== '' ? esc($desc) : '<em class="text-muted">Tidak ada deskripsi</em>';
                        ?>
                    </p>

                    <span class="badge <?= $aktif ? 'bg-success' : 'bg-secondary' ?>" id="preview-status">
                        <?= $aktif
                            ? '<i class="bi bi-check-circle"></i> Aktif'
                            : '<i class="bi bi-x-circle"></i> Nonaktif'
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputNama = document.getElementById('name');
        const inputDeskripsi = document.getElementById('description');
        const inputAktif = document.getElementById('is_active');

        const previewNama = document.getElementById('preview-name');
        const previewDeskripsi = document.getElementById('preview-description');
        const previewStatus = document.getElementById('preview-status');

        const btnSubmit = document.getElementById('btnSubmit');
        const form = document.getElementById('categoryForm');

        inputNama?.addEventListener('input', () => {
            previewNama.textContent = inputNama.value.trim() || 'Nama Kategori';
        });

        inputDeskripsi?.addEventListener('input', () => {
            const teks = inputDeskripsi.value.trim();
            previewDeskripsi.innerHTML = teks ? teks : '<em class="text-muted">Tidak ada deskripsi</em>';
        });

        inputAktif?.addEventListener('change', () => {
            if (inputAktif.checked) {
                previewStatus.classList.remove('bg-secondary');
                previewStatus.classList.add('bg-success');
                previewStatus.innerHTML = '<i class="bi bi-check-circle"></i> Aktif';
            } else {
                previewStatus.classList.remove('bg-success');
                previewStatus.classList.add('bg-secondary');
                previewStatus.innerHTML = '<i class="bi bi-x-circle"></i> Nonaktif';
            }
        });

        form?.addEventListener('submit', () => {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .preview-kategori {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 14px;
        background: #f8f9fa;
    }

    .avatar-isi {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 16px;
    }

    .btn-primary {
        background-color: #435ebe;
        border-color: #435ebe;
    }

    .btn-primary:hover {
        background-color: #364296;
        border-color: #364296;
    }
</style>
<?= $this->endSection() ?>