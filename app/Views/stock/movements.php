<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title & Breadcrumbs -->
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
                            <li class="breadcrumb-item active"><?= $page_title ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100 bg-<?= $current_type === 'IN' ? 'success' : 'info' ?> text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-white-70 mb-2 d-block">Total Transaksi</span>
                                <span class="fw-bold fs-3"><?= number_format($stats['total_transactions']) ?></span>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-<?= $current_type === 'IN' ? 'arrow-down' : 'arrow-up' ?> fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100 bg-<?= $current_type === 'IN' ? 'success' : 'warning' ?> text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="text-white-70 mb-2 d-block">Total Quantity</span>
                                <span class="fw-bold fs-3"><?= number_format($stats['total_quantity']) ?></span>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-boxes fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="text-muted fw-normal mt-0">Status Operasi</h5>
                                <h3 class="mb-3 text-<?= $current_type === 'IN' ? 'success' : 'info' ?>">
                                    <i class="fas fa-<?= $current_type === 'IN' ? 'download' : 'upload' ?> me-2"></i>
                                    <?= $stats['type_label'] ?>
                                </h3>
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
                        <!-- Tab Navigation -->
                        <div class="d-flex justify-content-between align-items-center">
                            <ul class="nav nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link <?= $current_type === 'IN' ? 'active' : '' ?>" 
                                       href="<?= current_url() ?>?type=IN<?= $filters['product'] ? '&product=' . $filters['product'] : '' ?><?= $filters['category'] ? '&category=' . $filters['category'] : '' ?>">
                                        <i class="fas fa-download me-2"></i>Barang Masuk
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $current_type === 'OUT' ? 'active' : '' ?>" 
                                       href="<?= current_url() ?>?type=OUT<?= $filters['product'] ? '&product=' . $filters['product'] : '' ?><?= $filters['category'] ? '&category=' . $filters['category'] : '' ?>">
                                        <i class="fas fa-upload me-2"></i>Barang Keluar
                                    </a>
                                </li>
                            </ul>
                            
                            <div>
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                <button class="btn btn-<?= $current_type === 'IN' ? 'success' : 'warning' ?>" onclick="showAddModal()">
                                    <i class="fas fa-plus me-2"></i>Tambah <?= $current_type === 'IN' ? 'Barang Masuk' : 'Barang Keluar' ?>
                                </button>
                            </div>
                        </div>

                        <!-- Filter Form (Collapsible) -->
                        <div class="collapse mt-3" id="filterCollapse">
                            <div class="row g-3">
                                <?= form_open(current_url(), ['method' => 'get', 'class' => 'row g-3']) ?>
                                    <input type="hidden" name="type" value="<?= $current_type ?>">
                                    
                                    <div class="col-md-3">
                                        <label class="form-label">Produk</label>
                                        <select class="form-select" name="product">
                                            <option value="">Semua Produk</option>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?= $product['id'] ?>" 
                                                    <?= $filters['product'] == $product['id'] ? 'selected' : '' ?>>
                                                    <?= esc($product['name']) ?> (<?= esc($product['sku']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label">Kategori</label>
                                        <select class="form-select" name="category">
                                            <option value="">Semua Kategori</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category['id'] ?>" 
                                                    <?= $filters['category'] == $category['id'] ? 'selected' : '' ?>>
                                                    <?= esc($category['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="start_date" 
                                               value="<?= $filters['start_date'] ?>">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" name="end_date" 
                                               value="<?= $filters['end_date'] ?>">
                                    </div>
                                    
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="<?= current_url() ?>?type=<?= $current_type ?>" class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Form untuk Add Movement -->
                        <div id="addMovementSection" style="display: none;" class="mb-4">
                            <div class="border rounded p-3 bg-light">
                                <h5 class="text-<?= $current_type === 'IN' ? 'success' : 'warning' ?>">
                                    <i class="fas fa-<?= $current_type === 'IN' ? 'download' : 'upload' ?> me-2"></i>
                                    Form <?= $current_type === 'IN' ? 'Barang Masuk' : 'Barang Keluar' ?>
                                </h5>
                                
                                <?= form_open('/stock/' . strtolower($current_type) . '/store', ['id' => 'movementForm']) ?>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nomor Referensi</label>
                                            <input type="text" class="form-control" name="reference_no" 
                                                   placeholder="Auto generate jika kosong">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Catatan Global</label>
                                            <input type="text" class="form-control" name="global_notes" 
                                                   placeholder="Catatan untuk semua item">
                                        </div>
                                    </div>

                                    <div id="movementItems">
                                        <!-- Items akan ditambahkan di sini via JavaScript -->
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-outline-primary" onclick="addMovementItem()">
                                                <i class="fas fa-plus me-2"></i>Tambah Item
                                            </button>
                                            <button type="submit" class="btn btn-<?= $current_type === 'IN' ? 'success' : 'warning' ?> ms-2">
                                                <i class="fas fa-save me-2"></i>Simpan Pergerakan
                                            </button>
                                            <button type="button" class="btn btn-secondary ms-2" onclick="hideAddModal()">
                                                <i class="fas fa-times me-2"></i>Batal
                                            </button>
                                        </div>
                                    </div>
                                <?= form_close() ?>
                            </div>
                        </div>

                        <!-- Recent Movements Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>No. Referensi</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Catatan</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_movements)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                Belum ada data pergerakan untuk filter yang dipilih
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_movements as $movement): ?>
                                            <tr>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y H:i', strtotime($movement['created_at'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $movement['type'] === 'IN' ? 'success' : 'warning' ?>">
                                                        <?= esc($movement['reference_no']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?= esc($movement['product_name']) ?></strong>
                                                        <br><small class="text-muted"><?= esc($movement['product_sku']) ?></small>
                                                    </div>
                                                </td>
                                                <td><?= esc($movement['category_name']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $movement['type'] === 'IN' ? 'success' : 'danger' ?> fs-6">
                                                        <?= $movement['type'] === 'IN' ? '+' : '-' ?><?= number_format($movement['quantity']) ?>
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

                        <?php if (!empty($recent_movements) && count($recent_movements) >= 10): ?>
                            <div class="text-center mt-3">
                                <a href="<?= base_url('stock/history') ?>?type=<?= $current_type ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-2"></i>Lihat Semua Riwayat
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemIndex = 0;

function showAddModal() {
    document.getElementById('addMovementSection').style.display = 'block';
    if (itemIndex === 0) {
        addMovementItem(); // Add first item automatically
    }
}

function hideAddModal() {
    document.getElementById('addMovementSection').style.display = 'none';
    document.getElementById('movementItems').innerHTML = '';
    itemIndex = 0;
    document.getElementById('movementForm').reset();
}

function addMovementItem() {
    const currentType = '<?= $current_type ?>';
    const products = <?= json_encode($products) ?>;
    
    const itemHtml = `
        <div class="row mb-3 movement-item" id="item-${itemIndex}">
            <div class="col-md-4">
                <label class="form-label">Produk</label>
                <select class="form-select" name="movements[${itemIndex}][product_id]" required onchange="updateProductInfo(this, ${itemIndex})">
                    <option value="">Pilih Produk</option>
                    ${products.map(p => `<option value="${p.id}" data-stock="${p.current_stock}">${p.name} (${p.sku})${currentType === 'OUT' ? ' - Stok: ' + p.current_stock : ''}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jumlah</label>
                <input type="number" class="form-control" name="movements[${itemIndex}][quantity]" 
                       min="1" required id="quantity-${itemIndex}">
                <small class="text-muted" id="stock-info-${itemIndex}"></small>
            </div>
            <div class="col-md-4">
                <label class="form-label">Catatan</label>
                <input type="text" class="form-control" name="movements[${itemIndex}][notes]" 
                       placeholder="Catatan item">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger" onclick="removeMovementItem(${itemIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('movementItems').insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function removeMovementItem(index) {
    document.getElementById(`item-${index}`).remove();
}

function updateProductInfo(select, index) {
    const selectedOption = select.options[select.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');
    const stockInfo = document.getElementById(`stock-info-${index}`);
    const quantityInput = document.getElementById(`quantity-${index}`);
    
    if (stock && '<?= $current_type ?>' === 'OUT') {
        stockInfo.textContent = `Stok tersedia: ${stock}`;
        quantityInput.max = stock;
        if (parseInt(stock) === 0) {
            stockInfo.className = 'text-danger';
            quantityInput.disabled = true;
        } else {
            stockInfo.className = 'text-muted';
            quantityInput.disabled = false;
        }
    } else {
        stockInfo.textContent = '';
        quantityInput.max = '';
        quantityInput.disabled = false;
    }
}

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-success')) {
                setTimeout(() => alert.remove(), 3000);
            }
        });
    }, 100);
});
</script>

<?= $this->endSection() ?>