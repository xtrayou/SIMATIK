<?= $this->extend('layouts/app') ?>

<?= $this->section('content'); ?>

<!-- Report Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">
                            <i class="bi bi-graph-up text-primary"></i> 
                            Laporan Stok Inventory
                        </h4>
                        <p class="text-muted mb-0">
                            Analisis kondisi stok per tanggal: 
                            <strong><?= date('d M Y H:i') ?></strong>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group">
                            <button class="btn btn-success" onclick="exportReport('excel')">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                            <button class="btn btn-danger" onclick="exportReport('pdf')">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </button>
                            <button class="btn btn-info" onclick="window.print()">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="stats-icon blue mb-3">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h4 class="text-primary"><?= number_format($summary['total_products']) ?></h4>
                <p class="text-muted mb-0">Total Produk</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="stats-icon green mb-3">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <h4 class="text-success"><?= format_currency($summary['total_value']) ?></h4>
                <p class="text-muted mb-0">Nilai Total</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="stats-icon purple mb-3">
                    <i class="bi bi-boxes"></i>
                </div>
                <h4 class="text-info"><?= number_format($summary['total_quantity']) ?></h4>
                <p class="text-muted mb-0">Total Quantity</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <div class="stats-icon green mb-3">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h4 class="text-success"><?= number_format($summary['normal_stock']) ?></h4>
                <p class="text-muted mb-0">Stok Normal</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <div class="stats-icon orange mb-3">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h4 class="text-warning"><?= number_format($summary['low_stock']) ?></h4>
                <p class="text-muted mb-0">Stok Rendah</p>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="card text-center border-danger">
            <div class="card-body">
                <div class="stats-icon red mb-3">
                    <i class="bi bi-x-circle"></i>
                </div>
                <h4 class="text-danger"><?= number_format($summary['out_of_stock']) ?></h4>
                <p class="text-muted mb-0">Stok Habis</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-funnel"></i> Filter & Sorting</h5>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" 
                                            <?= $filters['category'] == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="stock_status" class="form-label">Status Stok</label>
                        <select class="form-select" id="stock_status" name="stock_status">
                            <option value="">Semua Status</option>
                            <option value="normal" <?= $filters['stock_status'] == 'normal' ? 'selected' : '' ?>>
                                Normal
                            </option>
                            <option value="low_stock" <?= $filters['stock_status'] == 'low_stock' ? 'selected' : '' ?>>
                                Stok Rendah
                            </option>
                            <option value="out_of_stock" <?= $filters['stock_status'] == 'out_of_stock' ? 'selected' : '' ?>>
                                Stok Habis
                            </option>
                            <option value="overstocked" <?= $filters['stock_status'] == 'overstocked' ? 'selected' : '' ?>>
                                Overstocked
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="sort_by" class="form-label">Urutkan</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="name" <?= $filters['sort_by'] == 'name' ? 'selected' : '' ?>>Nama</option>
                            <option value="current_stock" <?= $filters['sort_by'] == 'current_stock' ? 'selected' : '' ?>>Stok</option>
                            <option value="stock_value" <?= $filters['sort_by'] == 'stock_value' ? 'selected' : '' ?>>Nilai</option>
                            <option value="category_name" <?= $filters['sort_by'] == 'category_name' ? 'selected' : '' ?>>Kategori</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="sort_order" class="form-label">Arah</label>
                        <select class="form-select" id="sort_order" name="sort_order">
                            <option value="ASC" <?= $filters['sort_order'] == 'ASC' ? 'selected' : '' ?>>A-Z / Kecil-Besar</option>
                            <option value="DESC" <?= $filters['sort_order'] == 'DESC' ? 'selected' : '' ?>>Z-A / Besar-Kecil</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="<?= base_url('/reports/stock') ?>" class="btn btn-outline-secondary btn-sm">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<!-- Category Breakdown Chart -->
<?php if (!empty($category_breakdown)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-pie-chart"></i> Breakdown per Kategori</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="categoryChart" height="300"></canvas>
                    </div>
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Produk</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($category_breakdown as $catName => $catData): ?>
                                    <tr>
                                        <td><strong><?= esc($catName) ?></strong></td>
                                        <td><?= number_format($catData['products']) ?></td>
                                        <td><?= format_currency($catData['total_value']) ?></td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif ?>
<!-- Detailed Stock Report -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Detail Laporan Stok</h5>
                <small class="text-muted">Total: <?= number_format(count($products)) ?> produk</small>
            </div>
            <div class="card-body">
                <?php if (!empty($products)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="stockReportTable">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Produk</th>
                                    <th width="15%">Kategori</th>
                                    <th width="10%">SKU</th>
                                    <th width="10%">Stok Saat Ini</th>
                                    <th width="10%">Min. Stok</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Nilai Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $index => $product): ?>
                                <tr class="<?= $product['stock_status'] == 'out_of_stock' ? 'table-danger' : 
                                             ($product['stock_status'] == 'low_stock' ? 'table-warning' : '') ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <div class="avatar-content bg-<?= $product['stock_status'] == 'out_of_stock' ? 'danger' : 
                                                                                 ($product['stock_status'] == 'low_stock' ? 'warning' : 'success') ?> text-white">
                                                    <i class="bi bi-box"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= esc($product['name']) ?></h6>
                                                <small class="text-muted"><?= $product['unit'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= esc($product['category_name']) ?></span>
                                    </td>
                                    <td>
                                        <code><?= $product['sku'] ?></code>
                                    </td>
                                    <td>
                                        <strong class="<?= $product['stock_status'] == 'out_of_stock' ? 'text-danger' : 
                                                         ($product['stock_status'] == 'low_stock' ? 'text-warning' : 'text-success') ?>">
                                            <?= number_format($product['current_stock']) ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= number_format($product['min_stock']) ?></span>
                                    </td>
                                    <td>
                                        <?= format_stock_badge($product['current_stock'], $product['min_stock']) ?>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= format_currency($product['stock_value']) ?></strong>
                                            <?php if ($product['price'] > 0): ?>
                                                <small class="d-block text-muted">
                                                    @ <?= format_currency($product['price']) ?>
                                                </small>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="4">TOTAL</th>
                                    <th><?= number_format($summary['total_quantity']) ?></th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th><?= format_currency($summary['total_value']) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Tidak ada data</h5>
                        <p class="text-muted">Ubah filter untuk melihat data yang berbeda</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('breadcrumb'); ?>
<ol class="breadcumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/'); ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/reports/stock'); ?>">Reports</a></li>
    <li class="breadcrumb-item active" aria-current="page">>Stock Report</a></li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#stockReportTable').DataTable({
        responsive: true,
        pageLength: 50,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        footerCallback: function ( row, data, start, end, display ) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over current page
            var pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
        }
    });

    // Category breakdown chart
    <?php if (!empty($category_breakdown)): ?>
    const categoryData = {
        labels: <?= json_encode(array_keys($category_breakdown)) ?>,
        datasets: [{
            data: <?= json_encode(array_column($category_breakdown, 'total_value')) ?>,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };

    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: categoryData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return context.label + ': ' + formatCurrency(value) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    <?php endif ?>

    // Auto-submit form on filter change
    $('#category, #stock_status, #sort_by, #sort_order').on('change', function() {
        $('#filterForm').submit();
    });
});

// Export functions
function exportReport(format) {
    const params = new URLSearchParams(window.location.search);
    const url = `<?= base_url('/reports/stock/export/') ?>${format}?${params.toString()}`;
    window.open(url, '_blank');
}

// Print-specific styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>
<?= $this->endSection(); ?>

<?= $this->section('styles') ?>
<style>
.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto;
}

.stats-icon.blue { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
.stats-icon.green { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
.stats-icon.purple { background-color: rgba(102, 16, 242, 0.1); color: #6610f2; }
.stats-icon.red { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
.stats-icon.orange { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }

.avatar-content {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

code {
    font-size: 0.9rem;
    color: #6f42c1;
    background-color: rgba(111, 66, 193, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
}

/* Print styles */
@media print {
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .btn-group,
    .card-header .btn {
        display: none !important;
    }
    
    .table {
        font-size: 12px;
    }
    
    .stats-icon {
        background-color: #f8f9fa !important;
        border: 1px solid #000;
    }
}

@media (max-width: 768px) {
    .stats-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
}
</style>
<?= $this->endSection() ?>