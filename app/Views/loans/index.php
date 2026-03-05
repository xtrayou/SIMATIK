<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Header & Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="mb-0 fw-bold">Daftar Permintaan ATK</h4>
                        <p class="text-muted small mb-0">Halaman pengelolaan distribusi barang ke unit kerja</p>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="GET" class="d-flex gap-2 align-items-center">
                            <select name="status" class="form-select form-select-sm" style="min-width: 150px;">
                                <option value="">Semua Status</option>
                                <option value="requested" <?= $filterStatus == 'requested' ? 'selected' : '' ?>>Diajukan</option>
                                <option value="approved" <?= $filterStatus == 'approved' ? 'selected' : '' ?>>Disetujui</option>
                                <option value="distributed" <?= $filterStatus == 'distributed' ? 'selected' : '' ?>>Didistribusikan</option>
                                <option value="cancelled" <?= $filterStatus == 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-light border">
                                <i class="bi bi-filter"></i>
                            </button>
                        </form>
                        <a href="<?= base_url('/loans/create') ?>" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-plus-lg me-1"></i> Buat Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Permintaan -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success m-3 alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($daftarPinjaman)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="loansTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" width="60">ID</th>
                                    <th>Informasi Pemohon</th>
                                    <th>Tgl Pengajuan</th>
                                    <th>Status</th>
                                    <th class="text-center" width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daftarPinjaman as $p): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">#<?= $p['id'] ?></td>
                                        <td>
                                            <div class="fw-bold"><?= esc($p['borrower_name']) ?></div>
                                            <div class="text-muted small"><?= esc($p['borrower_unit']) ?></div>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($p['loan_date'])) ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = 'bg-secondary';
                                            $statusLabel = 'Diajukan';

                                            switch ($p['status']) {
                                                case 'requested':
                                                    $badgeClass = 'bg-info';
                                                    $statusLabel = 'Diajukan';
                                                    break;
                                                case 'approved':
                                                    $badgeClass = 'bg-primary';
                                                    $statusLabel = 'Disetujui';
                                                    break;
                                                case 'distributed':
                                                    $badgeClass = 'bg-success';
                                                    $statusLabel = 'Didistribusikan';
                                                    break;
                                                case 'cancelled':
                                                    $badgeClass = 'bg-danger';
                                                    $statusLabel = 'Dibatalkan';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge rounded-pill <?= $badgeClass ?>"><?= $statusLabel ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('/loans/show/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary px-3">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Tidak ada data permintaan ditemukan.</p>
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
        $('#loansTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            order: [
                [0, 'desc']
            ]
        });
    });
</script>
<?= $this->endSection() ?>