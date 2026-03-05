<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="row">
    <!-- Informasi Utama -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Detail Permintaan #<?= $pinjaman['id'] ?></h5>
                <?php 
                $badgeClass = 'bg-secondary';
                $statusLabel = 'Diajukan';
                
                switch($pinjaman['status']) {
                    case 'requested': $badgeClass = 'bg-info'; $statusLabel = 'Diajukan'; break;
                    case 'approved': $badgeClass = 'bg-primary'; $statusLabel = 'Disetujui'; break;
                    case 'distributed': $badgeClass = 'bg-success'; $statusLabel = 'Didistribusikan'; break;
                    case 'cancelled': $badgeClass = 'bg-danger'; $statusLabel = 'Dibatalkan'; break;
                }
                ?>
                <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-2 fs-6 text-uppercase"><?= $statusLabel ?></span>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6 border-end">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">Informasi Pemohon</h6>
                        <div class="mb-2">
                            <label class="text-muted small d-block">Nama Lengkap</label>
                            <span class="fw-bold fs-5"><?= esc($pinjaman['borrower_name']) ?></span>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small d-block">Unit / Prodi</label>
                            <span><?= esc($pinjaman['borrower_unit'] ?: '-') ?></span>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small d-block">NIM / NIP</label>
                            <code><?= esc($pinjaman['borrower_identifier'] ?: '-') ?></code>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">Detail Pengajuan</h6>
                        <div class="mb-2">
                            <label class="text-muted small d-block">Tanggal Permintaan</label>
                            <span class="fw-bold"><?= date('d F Y', strtotime($pinjaman['loan_date'])) ?></span>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small d-block">Email</label>
                            <a href="mailto:<?= esc($pinjaman['email']) ?>" class="text-decoration-none">
                                <i class="bi bi-envelope text-primary me-1"></i> <?= esc($pinjaman['email']) ?>
                            </a>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small d-block">Catatan</label>
                            <p class="mb-0 italic text-muted"><?= nl2br(esc((string) ($pinjaman['notes'] ?? '-'))) ?></p>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold mb-3"><i class="bi bi-cart me-2"></i>Daftar Barang yang Diminta</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Nama Produk</th>
                                <th width="150" class="text-center">SKU</th>
                                <th width="100" class="text-center">Jumlah</th>
                                <th width="100" class="text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($pinjaman['items'] ?? []) as $item): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-primary"><?= esc($item['product_name']) ?></td>
                                <td class="text-center"><code><?= esc($item['sku']) ?></code></td>
                                <td class="text-center fw-bold fs-5"><?= number_format($item['quantity']) ?></td>
                                <td class="text-center text-muted"><?= esc($item['unit'] ?? 'Pcs') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Aksi -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Kontrol Permintaan</h5>
            </div>
            <div class="card-body p-4">
                <?php if ($pinjaman['status'] == 'requested'): ?>
                    <div class="alert alert-info border-0 bg-light-info mb-4">
                        <i class="bi bi-info-circle me-2"></i> Permintaan perlu ditinjau sebelum disetujui.
                    </div>
                    <div class="d-grid gap-3">
                        <button class="btn btn-primary py-2 fw-bold" id="btn-approve">
                            <i class="bi bi-check-lg me-2"></i> SETUJUI PERMINTAAN
                        </button>
                        <button class="btn btn-outline-danger py-2" id="btn-cancel">
                            <i class="bi bi-x-lg me-2"></i> BATALKAN
                        </button>
                    </div>
                <?php elseif ($pinjaman['status'] == 'approved'): ?>
                    <div class="alert alert-warning border-0 bg-light-warning mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i> Barang siap didistribusikan. Stok akan terpotong otomatis.
                    </div>
                    <div class="d-grid gap-3">
                        <button class="btn btn-success py-2 fw-bold" id="btn-distribute">
                            <i class="bi bi-box-arrow-right me-2"></i> DISTRIBUSIKAN SEKARANG
                        </button>
                        <button class="btn btn-outline-danger py-2" id="btn-cancel">
                            <i class="bi bi-x-lg me-2"></i> BATALKAN
                        </button>
                    </div>
                <?php elseif ($pinjaman['status'] == 'distributed'): ?>
                    <div class="text-center py-4">
                        <div class="avatar avatar-xl bg-light-success text-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-check-all display-4"></i>
                        </div>
                        <h5 class="fw-bold">Selesai Didistribusi</h5>
                        <p class="text-muted small">Barang telah diserahkan ke pemohon dan stok gudang sudah diperbarui.</p>
                        <hr>
                        <button class="btn btn-light border w-100" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i> Cetak Bukti
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-slash-circle display-4"></i>
                        <p class="mt-3">Permintaan ini telah dibatalkan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function jalankanAksi(url, pesanCek) {
        if (confirm(pesanCek)) {
            const btn = event.target;
            const originalHtml = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        alert(res.message);
                        location.reload();
                    } else {
                        alert(res.message);
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan server.');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        }
    }

    $('#btn-approve').on('click', function() {
        jalankanAksi('<?= base_url('loans/approve/' . $pinjaman['id']) ?>', 'Setujui permintaan ini?');
    });

    $('#btn-distribute').on('click', function() {
        jalankanAksi('<?= base_url('loans/distribute/' . $pinjaman['id']) ?>', 'Lanjutkan distribusi? Tindakan ini akan memotong stok barang.');
    });

    $('#btn-cancel').on('click', function() {
        jalankanAksi('<?= base_url('loans/cancel/' . $pinjaman['id']) ?>', 'Apakah Anda yakin ingin membatalkan permintaan ini?');
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .bg-light-info { background-color: rgba(13, 202, 240, 0.1); }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-light-success { background-color: rgba(25, 135, 84, 0.1); }
    .italic { font-style: italic; }
</style>
<?= $this->endSection() ?>