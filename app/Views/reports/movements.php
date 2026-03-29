<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0 font-size-18"><?= $page_title ?></h4>
                        <p class="text-muted mb-0"><?= $page_subtitle ?></p>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url('reports/stock') ?>">Reports</a></li>
                            <li class="breadcrumb-item active"><?= $page_title ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-white-70 mb-2 d-block">Total Pergerakan</span>
                                <span class="fw-bold fs-3"><?= number_format($summary['total_movements'] ?? 0) ?></span>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-exchange-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-white-70 mb-2 d-block">Barang Masuk</span>
                                <span class="fw-bold fs-3"><?= number_format($summary['total_in'] ?? 0) ?></span>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-down fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-white-70 mb-2 d-block">Barang Keluar</span>
                                <span class="fw-bold fs-3"><?= number_format($summary['total_out'] ?? 0) ?></span>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-up fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-white-70 mb-2 d-block">Penyesuaian</span>
                                <span class="fw-bold fs-3"><?= number_format($summary['total_adjustment'] ?? 0) ?></span>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-balance-scale fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-chart-line text-primary me-2"></i>
                                Laporan Pergerakan Stok
                            </h4>
                            <div>
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                <?php if (in_array(session()->get('role'), ['admin', 'superadmin'])): ?>
                                <div class="btn-group">
                                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-download me-2"></i>Export
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="<?= base_url('reports/export/movements?format=excel') ?>">
                                            <i class="fas fa-file-excel me-2"></i>Excel
                                        </a></li>
                                        <li><a class="dropdown-item" href="<?= base_url('reports/export/movements?format=pdf') ?>">
                                            <i class="fas fa-file-pdf me-2"></i>PDF
                                        </a></li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Filter Form -->
                        <div class="collapse mt-3" id="filterCollapse">
                            <div class="row g-3">
                                <?= form_open(current_url(), ['method' => 'get', 'class' => 'row g-3']) ?>
                                    <div class="col-md-2">
                                        <label class="form-label">Tipe</label>
                                        <select class="form-select" name="type">
                                            <option value="">Semua Tipe</option>
                                            <option value="IN" <?= ($filters['type'] ?? '') == 'IN' ? 'selected' : '' ?>>Barang Masuk</option>
                                            <option value="OUT" <?= ($filters['type'] ?? '') == 'OUT' ? 'selected' : '' ?>>Barang Keluar</option>
                                            <option value="ADJUSTMENT" <?= ($filters['type'] ?? '') == 'ADJUSTMENT' ? 'selected' : '' ?>>Penyesuaian</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label">Produk</label>
                                        <select class="form-select" name="product">
                                            <option value="">Semua Produk</option>
                                            <?php if (isset($products)): ?>
                                                <?php foreach ($products as $product): ?>
                                                    <option value="<?= $product['id'] ?>" 
                                                        <?= ($filters['product'] ?? '') == $product['id'] ? 'selected' : '' ?>>
                                                        <?= esc($product['name']) ?> (<?= esc($product['sku']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="start_date" 
                                               value="<?= $filters['start_date'] ?? '' ?>">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" name="end_date" 
                                               value="<?= $filters['end_date'] ?? '' ?>">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">Reference</label>
                                        <input type="text" class="form-control" name="reference" 
                                               value="<?= $filters['reference'] ?? '' ?>" placeholder="No. Referensi">
                                    </div>
                                    
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="<?= current_url() ?>" class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. Referensi</th>
                                        <th>Tipe</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Catatan</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($movements)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                <h5>Tidak ada data pergerakan</h5>
                                                <p>Belum ada data sesuai dengan filter yang dipilih</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($movements as $movement): ?>
                                            <tr>
                                                <td>
                                                    <small><?= date('d/m/Y H:i', strtotime($movement['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= esc($movement['reference_no']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                        $movement['type'] == 'IN' ? 'success' : 
                                                        ($movement['type'] == 'OUT' ? 'warning' : 'info') ?>">
                                                        <?php
                                                        $typeLabels = [
                                                            'IN' => 'Masuk',
                                                            'OUT' => 'Keluar',
                                                            'ADJUSTMENT' => 'Penyesuaian'
                                                        ];
                                                        echo $typeLabels[$movement['type']] ?? $movement['type'];
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?= esc($movement['product_name']) ?></strong>
                                                        <br><small class="text-muted"><?= esc($movement['product_sku']) ?></small>
                                                    </div>
                                                </td>
                                                <td><?= esc($movement['category_name'] ?? '-') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                        $movement['type'] == 'IN' ? 'success' : 
                                                        ($movement['type'] == 'OUT' ? 'danger' : 'secondary') ?> fs-6">
                                                        <?= $movement['type'] == 'IN' ? '+' : ($movement['type'] == 'OUT' ? '-' : '') ?><?= number_format($movement['quantity']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?= esc($movement['notes'] ?: '-') ?></small>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?= esc($movement['created_by']) ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination would go here -->
                        <?php if (isset($pagination)): ?>
                            <div class="d-flex justify-content-center mt-3">
                                <?= $pagination ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>