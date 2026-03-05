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
                            <i class="bi bi-currency-dollar text-success"></i>
                            Valuasi Inventory
                        </h4>
                        <p class="text-muted mb-0">
                            Analisis nilai aset inventory per tanggal:
                            <strong><?= date('d M Y H:i') ?></strong>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button class="btn btn-info" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                    <i class="bi bi-box-seam text-primary fs-4"></i>
                </div>
                <h4 class="fw-bold text-primary"><?= number_format($summary['total_products']) ?></h4>
                <p class="text-muted mb-0 small">Total Produk Aktif</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                    <i class="bi bi-cash-stack text-success fs-4"></i>
                </div>
                <h4 class="fw-bold text-success"><?= format_currency($summary['total_current_value']) ?></h4>
                <p class="text-muted mb-0 small">Nilai Jual (Harga Jual)</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                    <i class="bi bi-wallet2 text-warning fs-4"></i>
                </div>
                <h4 class="fw-bold text-warning"><?= format_currency($summary['total_cost_value']) ?></h4>
                <p class="text-muted mb-0 small">Nilai Modal (Harga Beli)</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px">
                    <i class="bi bi-graph-up-arrow text-info fs-4"></i>
                </div>
                <h4 class="fw-bold text-info"><?= format_currency($summary['total_potential_profit']) ?></h4>
                <p class="text-muted mb-0 small">Potensi Keuntungan (<?= number_format($summary['average_margin'], 1) ?>%)</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Category Valuation -->
<div class="row mb-4">
    <!-- Filter -->
    <div class="col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="bi bi-funnel me-2"></i>Filter</h6>
            </div>
            <div class="card-body">
                <form method="get" action="<?= current_url() ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $filters['category'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= esc($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-1"></i> Terapkan
                        </button>
                        <a href="<?= base_url('reports/valuation') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="col-lg-8 mb-3">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Valuasi per Kategori</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kategori</th>
                                <th class="text-center">Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Nilai Jual</th>
                                <th class="text-end">Nilai Modal</th>
                                <th class="text-end">Potensi Profit</th>
                                <th class="text-center">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($category_valuation)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Tidak ada data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($category_valuation as $catName => $catData): ?>
                                    <tr>
                                        <td class="fw-bold"><?= esc($catName) ?></td>
                                        <td class="text-center"><?= $catData['products'] ?></td>
                                        <td class="text-center"><?= number_format($catData['total_quantity']) ?></td>
                                        <td class="text-end"><?= format_currency($catData['current_value']) ?></td>
                                        <td class="text-end"><?= format_currency($catData['cost_value']) ?></td>
                                        <td class="text-end text-success fw-bold"><?= format_currency($catData['potential_profit']) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $catData['margin_percentage'] >= 20 ? 'success' : ($catData['margin_percentage'] >= 10 ? 'warning' : 'danger') ?>">
                                                <?= number_format($catData['margin_percentage'], 1) ?>%
                                            </span>
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

<!-- Product Detail Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-table me-2"></i>Detail Valuasi Produk</h6>
                <span class="badge bg-primary"><?= count($products) ?> produk</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>SKU</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-end">Harga Modal</th>
                                <th class="text-end">Nilai Jual</th>
                                <th class="text-end">Nilai Modal</th>
                                <th class="text-end">Potensi Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Tidak ada produk dengan stok aktif
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $i => $product): ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td><code><?= esc($product['sku']) ?></code></td>
                                        <td>
                                            <strong><?= esc($product['name']) ?></strong>
                                        </td>
                                        <td><span class="badge bg-light text-dark"><?= esc($product['category_name']) ?></span></td>
                                        <td class="text-center">
                                            <span class="fw-bold"><?= number_format($product['current_stock']) ?></span>
                                            <small class="text-muted"><?= esc($product['unit']) ?></small>
                                        </td>
                                        <td class="text-end"><?= format_currency($product['price']) ?></td>
                                        <td class="text-end"><?= format_currency($product['cost_price']) ?></td>
                                        <td class="text-end fw-bold"><?= format_currency($product['current_value']) ?></td>
                                        <td class="text-end"><?= format_currency($product['cost_value']) ?></td>
                                        <td class="text-end">
                                            <?php
                                                $profit = $product['potential_profit'];
                                                $margin = $product['current_value'] > 0 
                                                    ? ($profit / $product['current_value']) * 100 : 0;
                                            ?>
                                            <span class="text-<?= $profit > 0 ? 'success' : 'danger' ?> fw-bold">
                                                <?= format_currency($profit) ?>
                                            </span>
                                            <br><small class="text-muted"><?= number_format($margin, 1) ?>%</small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-dark fw-bold">
                                    <td colspan="7" class="text-end">TOTAL</td>
                                    <td class="text-end"><?= format_currency($summary['total_current_value']) ?></td>
                                    <td class="text-end"><?= format_currency($summary['total_cost_value']) ?></td>
                                    <td class="text-end text-success"><?= format_currency($summary['total_potential_profit']) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
