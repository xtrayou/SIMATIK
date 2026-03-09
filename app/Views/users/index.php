<?= $this->extend('layouts/app') ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Manajemen User</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-people-fill me-2"></i>Daftar User</h4>
                <a href="<?= base_url('/users/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Tambah User
                </a>
            </div>

            <div class="card-body">
                <?php if (session('sukses')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= session('sukses') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session('galat')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?= session('galat') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter & Search -->
                <div class="row mb-3 g-2">
                    <div class="col-md-6">
                        <form action="<?= base_url('/users') ?>" method="GET">
                            <?php if ($filterRole): ?>
                                <input type="hidden" name="role" value="<?= esc($filterRole) ?>">
                            <?php endif; ?>
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari nama atau username...">
                                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                                <?php if (!empty($keyword)): ?>
                                    <a href="<?= base_url('/users') ?>" class="btn btn-outline-danger"><i class="bi bi-x-circle"></i></a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" onchange="window.location.href='<?= base_url('/users') ?>?role='+this.value<?= !empty($keyword) ? "+'&q=" . esc($keyword) . "'" : '' ?>">
                            <option value="">Semua Role</option>
                            <option value="superadmin" <?= ($filterRole === 'superadmin') ? 'selected' : '' ?>>Superadmin</option>
                            <option value="admin" <?= ($filterRole === 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="user" <?= ($filterRole === 'user') ? 'selected' : '' ?>>User/Pemohon</option>
                        </select>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Tidak ada data user
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $i => $user): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><code><?= esc($user['username']) ?></code></td>
                                        <td><?= esc($user['name']) ?></td>
                                        <td>
                                            <?php if ($user['role'] === 'superadmin'): ?>
                                                <span class="badge bg-dark"><i class="bi bi-star-fill me-1"></i>Superadmin</span>
                                            <?php elseif ($user['role'] === 'admin'): ?>
                                                <span class="badge bg-danger"><i class="bi bi-shield-fill me-1"></i>Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-info"><i class="bi bi-person-fill me-1"></i>User/Pemohon</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <?php if ((int) $user['id'] !== (int) session('userId')): ?>
                                                <form action="<?= base_url('/users/delete/' . $user['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user <?= esc($user['name']) ?>?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>