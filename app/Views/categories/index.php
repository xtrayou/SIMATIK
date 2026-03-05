<?= $this->extend('layouts/app') ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kategori</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Daftar Kategori</h4>
                <a href="<?= base_url('/categories/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Tambah Kategori
                </a>
            </div>

            <div class="card-body">
                <?php if (session('sukses')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= session('sukses') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endif; ?>

                <?php if (session('galat')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= session('galat') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter & Pencarian -->
                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <form action="<?= base_url('/categories') ?>" method="GET" id="formPencarian">
                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="kataKunci"
                                    name="q"
                                    value="<?= esc($kataKunci ?? '') ?>"
                                    placeholder="Cari nama atau deskripsi...">
                                <button class="btn btn-outline-secondary" type="submit" id="btnCari">
                                    <i class="bi bi-search"></i>
                                </button>
                                <?php if (!empty($kataKunci)): ?>
                                    <a href="<?= base_url('/categories') ?>" class="btn btn-outline-danger" id="btnReset">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-6 d-flex justify-content-md-end mt-2 mt-md-0 gap-2">
                        <select class="form-select form-select-sm w-auto" id="filterStatus" onchange="terapkanFilter()">
                            <option value="">Semua Status</option>
                            <option value="1" <?= ($filterStatus ?? '') == '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= ($filterStatus ?? '') == '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                        <select class="form-select form-select-sm w-auto" id="perHalaman" onchange="terapkanFilter()">
                            <option value="10" <?= ($perHalaman ?? 10) == 10 ? 'selected' : '' ?>>10 / halaman</option>
                            <option value="25" <?= ($perHalaman ?? 10) == 25 ? 'selected' : '' ?>>25 / halaman</option>
                            <option value="50" <?= ($perHalaman ?? 10) == 50 ? 'selected' : '' ?>>50 / halaman</option>
                        </select>
                    </div>
                </div>

                <!-- Tabel -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabelKategori">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>
                                    <a href="<?= base_url('/categories?urut=name&arah=' . ($arahUrut === 'asc' ? 'desc' : 'asc') . '&q=' . esc($kataKunci ?? '') . '&status=' . ($filterStatus ?? '')) ?>"
                                       class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                        Nama
                                        <?php if (($kolomUrut ?? '') === 'name'): ?>
                                            <i class="bi bi-arrow-<?= $arahUrut === 'asc' ? 'up' : 'down' ?>"></i>
                                        <?php else: ?>
                                            <i class="bi bi-arrow-down-up text-muted" style="font-size: 0.75rem;"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Deskripsi</th>
                                <th>Jumlah Produk</th>
                                <th>Status</th>
                                <th>
                                    <a href="<?= base_url('/categories?urut=created_at&arah=' . ($arahUrut === 'asc' ? 'desc' : 'asc') . '&q=' . esc($kataKunci ?? '') . '&status=' . ($filterStatus ?? '')) ?>"
                                       class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                        Dibuat
                                        <?php if (($kolomUrut ?? '') === 'created_at'): ?>
                                            <i class="bi bi-arrow-<?= $arahUrut === 'asc' ? 'up' : 'down' ?>"></i>
                                        <?php else: ?>
                                            <i class="bi bi-arrow-down-up text-muted" style="font-size: 0.75rem;"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daftarKategori)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                        <?= !empty($kataKunci) ? 'Tidak ada kategori yang cocok dengan pencarian.' : 'Belum ada kategori.' ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($daftarKategori as $urutan => $baris): ?>
                                    <tr>
                                        <td><?= $nomorAwal + $urutan ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-isi bg-primary text-white flex-shrink-0" style="width:32px;height:32px;border-radius:50%;display:grid;place-items:center;font-size:14px;">
                                                    <i class="bi bi-collection-fill"></i>
                                                </div>
                                                <span class="fw-medium"><?= esc($baris['name']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <?= !empty($baris['description']) ? esc(mb_strimwidth($baris['description'], 0, 60, '...')) : '<em>Tidak ada deskripsi</em>' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= $baris['jumlah_produk'] ?? 0 ?> produk
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($baris['is_active']): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle"></i> Nonaktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                <?= date('d M Y', strtotime($baris['created_at'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="<?= base_url('/categories/edit/' . $baris['id']) ?>"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger btn-hapus"
                                                    data-id="<?= $baris['id'] ?>"
                                                    data-nama="<?= esc($baris['name']) ?>"
                                                    data-jumlah="<?= $baris['jumlah_produk'] ?? 0 ?>"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi -->
                <?php if (!empty($paginasi)): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            Menampilkan <?= $nomorAwal ?> – <?= min($nomorAwal + count($daftarKategori) - 1, $totalData) ?>
                            dari <?= $totalData ?> kategori
                        </small>
                        <nav aria-label="Navigasi halaman">
                            <?= $paginasi ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="judulModalHapus" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="judulModalHapus">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="namaKategoriHapus"></strong>?</p>
                <div class="alert alert-danger mb-0" id="pesanDigunakanProduk" style="display:none;">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    Kategori ini sedang digunakan oleh <strong id="jumlahProdukHapus"></strong> produk dan tidak dapat dihapus.
                </div>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapus" method="POST" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger" id="btnKonfirmasiHapus">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tombol hapus
        const tombolHapus = document.querySelectorAll('.btn-hapus');
        const modalHapus  = new bootstrap.Modal(document.getElementById('modalHapus'));

        tombolHapus.forEach(tombol => {
            tombol.addEventListener('click', function() {
                const idKategori    = this.dataset.id;
                const namaKategori  = this.dataset.nama;
                const jumlahProduk  = parseInt(this.dataset.jumlah, 10);

                document.getElementById('namaKategoriHapus').textContent = namaKategori;

                const pesanDipakai     = document.getElementById('pesanDigunakanProduk');
                const jumlahProdukEl   = document.getElementById('jumlahProdukHapus');
                const btnKonfirmasi    = document.getElementById('btnKonfirmasiHapus');
                const formHapus        = document.getElementById('formHapus');

                if (jumlahProduk > 0) {
                    pesanDipakai.style.display   = 'block';
                    jumlahProdukEl.textContent    = jumlahProduk;
                    btnKonfirmasi.disabled        = true;
                } else {
                    pesanDipakai.style.display   = 'none';
                    btnKonfirmasi.disabled        = false;
                    formHapus.action = `<?= base_url('/categories/delete/') ?>${idKategori}`;
                }

                modalHapus.show();
            });
        });

        // Form hapus submit
        document.getElementById('formHapus')?.addEventListener('submit', function() {
            const btnKonfirmasi = document.getElementById('btnKonfirmasiHapus');
            btnKonfirmasi.disabled = true;
            btnKonfirmasi.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...';
        });
    });

    function terapkanFilter() {
        const kataKunci   = document.getElementById('kataKunci')?.value ?? '';
        const status      = document.getElementById('filterStatus')?.value ?? '';
        const jumlahData  = document.getElementById('perHalaman')?.value ?? '10';

        const params = new URLSearchParams();
        if (kataKunci)  params.set('q', kataKunci);
        if (status !== '') params.set('status', status);
        params.set('per_page', jumlahData);

        window.location.href = `<?= base_url('/categories') ?>?${params.toString()}`;
    }
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .table th a {
        white-space: nowrap;
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