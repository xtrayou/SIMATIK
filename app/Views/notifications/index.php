<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-bell"></i> Notifikasi</h3>
                <p class="text-subtitle text-muted">Daftar semua notifikasi sistem</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="section">
        <div class="row">
            <div class="col-12">

                <!-- Header Actions -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger fs-6"><?= $unreadCount ?> belum dibaca</span>
                        <?php else: ?>
                            <span class="badge bg-success fs-6">Semua sudah dibaca</span>
                        <?php endif ?>
                    </div>
                    <div>
                        <?php if ($unreadCount > 0): ?>
                            <a href="<?= base_url('/notifications/mark-all-read') ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-check-all"></i> Tandai Semua Dibaca
                            </a>
                        <?php endif ?>
                    </div>
                </div>

                <!-- Notification List -->
                <div class="card">
                    <div class="card-body p-0">
                        <?php if (empty($notifications)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-bell-slash fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">Tidak ada notifikasi</h5>
                                <p class="text-muted">Belum ada notifikasi untuk ditampilkan.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($notifications as $notif): ?>
                                    <a href="<?= base_url('/notifications/read/' . $notif['id']) ?>"
                                        class="list-group-item list-group-item-action <?= $notif['is_read'] ? '' : 'bg-light-primary' ?>">
                                        <div class="d-flex align-items-start py-2">
                                            <!-- Icon -->
                                            <div class="me-3">
                                                <div class="avatar avatar-md bg-light-<?= esc($notif['color']) ?> rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi <?= esc($notif['icon']) ?> text-<?= esc($notif['color']) ?> fs-5"></i>
                                                </div>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h6 class="mb-1 <?= $notif['is_read'] ? 'text-muted' : 'fw-bold' ?>">
                                                        <?= esc($notif['title']) ?>
                                                    </h6>
                                                    <small class="text-muted ms-2 text-nowrap">
                                                        <?= waktu_lalu($notif['created_at']) ?>
                                                    </small>
                                                </div>
                                                <p class="mb-1 <?= $notif['is_read'] ? 'text-muted' : '' ?>">
                                                    <?= esc($notif['message']) ?>
                                                </p>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-<?= esc($notif['color']) ?> bg-opacity-25 text-<?= esc($notif['color']) ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $notif['type'])) ?>
                                                    </span>
                                                    <?php if (!$notif['is_read']): ?>
                                                        <span class="badge bg-primary rounded-pill">Baru</span>
                                                    <?php endif ?>
                                                </div>
                                            </div>

                                            <!-- Delete button -->
                                            <div class="ms-2">
                                                <button class="btn btn-sm btn-light btn-delete-notif"
                                                    data-id="<?= $notif['id'] ?>"
                                                    onclick="event.preventDefault(); event.stopPropagation(); deleteNotification(<?= $notif['id'] ?>);">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (!empty($pager)): ?>
                        <div class="card-footer d-flex justify-content-center">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function deleteNotification(id) {
        if (!confirm('Hapus notifikasi ini?')) return;

        fetch(`<?= base_url('/notifications/delete') ?>/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    // Hapus elemen dari DOM
                    const item = document.querySelector(`[data-id="${id}"]`).closest('.list-group-item');
                    if (item) item.remove();
                }
            })
            .catch(err => console.error(err));
    }
</script>

<?= $this->endSection() ?>