<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Kartu Statistik Utama -->
<div class="row mb-4">
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card inventory-card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon purple mb-2">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Total Produk</h6>
                        <h6 class="font-extrabold mb-0" id="totalProduk"><?= number_format($total_products) ?></h6>
                        <small class="text-muted">Produk aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card inventory-card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon blue mb-2">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Kategori</h6>
                        <h6 class="font-extrabold mb-0"><?= number_format($total_categories) ?></h6>
                        <small class="text-muted">Kategori aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card inventory-card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon green mb-2">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Nilai Inventory</h6>
                        <h6 class="font-extrabold mb-0"><?= format_currency($inventory_value) ?></h6>
                        <small class="text-muted">Total valuasi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card inventory-card <?= $low_stock_count > 0 ? 'low-stock alert-stock' : '' ?>">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon <?= $low_stock_count > 0 ? 'red' : 'green' ?> mb-2">
                            <i class="bi bi-<?= $low_stock_count > 0 ? 'exclamation-triangle-fill' : 'check-circle-fill' ?>"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Stok Rendah</h6>
                        <h6 class="font-extrabold mb-0 <?= $low_stock_count > 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($low_stock_count) ?>
                        </h6>
                        <small class="text-muted">Perlu perhatian</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Cepat -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-speedometer2"></i> Statistik Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 col-md-3">
                        <div class="quick-stat">
                            <h4 class="text-info"><?= number_format($quick_stats['today_movements']) ?></h4>
                            <p class="text-muted mb-0">Transaksi Hari Ini</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-stat">
                            <h4 class="text-success"><?= number_format($quick_stats['this_week_in']) ?></h4>
                            <p class="text-muted mb-0">Barang Masuk (7 hari)</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-stat">
                            <h4 class="text-danger"><?= number_format($quick_stats['this_week_out']) ?></h4>
                            <p class="text-muted mb-0">Barang Keluar (7 hari)</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-stat">
                            <h4 class="text-warning"><?= number_format($out_of_stock_count) ?></h4>
                            <p class="text-muted mb-0">Stok Habis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Aksi Cepat -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-lightning-fill"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('/stock/in') ?>" class="btn btn-info w-100 btn-lg">
                            <i class="bi bi-arrow-down-circle"></i>
                            <div class="d-block">
                                <strong>Barang Masuk</strong>
                                <small class="d-block">Input stok baru</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('/stock/out') ?>" class="btn btn-warning w-100 btn-lg">
                            <i class="bi bi-arrow-up-circle"></i>
                            <div class="d-block">
                                <strong>Barang Keluar</strong>
                                <small class="d-block">Keluarkan stok</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Konten Utama Dashboard -->
<div class="row">
    <!-- Grafik Pergerakan Stok -->
    <div class="col-12 col-xl-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-graph-up"></i> Pergerakan Stok 6 Bulan Terakhir</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i> Opsi
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" onclick="eksporGrafik('pergerakan')">
                                <i class="bi bi-download"></i> Ekspor Grafik
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('/reports/movements') ?>">
                                <i class="bi bi-file-earmark-text"></i> Laporan Detail
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="grafikPergerakanStok" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Grafik Pie Status Stok -->
    <div class="col-12 col-xl-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-pie-chart"></i> Status Stok</h5>
            </div>
            <div class="card-body">
                <canvas id="grafikStatusStok"></canvas>
                <div class="mt-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stock-legend">
                                <div class="legend-color bg-success"></div>
                                <small>Normal</small>
                                <strong class="d-block"><?= $chart_data['stock_status_pie']['data'][2] ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stock-legend">
                                <div class="legend-color bg-warning"></div>
                                <small>Rendah</small>
                                <strong class="d-block text-warning"><?= $chart_data['stock_status_pie']['data'][1] ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stock-legend">
                                <div class="legend-color bg-danger"></div>
                                <small>Habis</small>
                                <strong class="d-block text-danger"><?= $chart_data['stock_status_pie']['data'][0] ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Aktivitas Terbaru -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h5>
                <a href="<?= base_url('/stock/history') ?>" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_movements)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="25%">Waktu</th>
                                    <th width="35%">Produk</th>
                                    <th width="20%">Tipe</th>
                                    <th width="20%">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_movements as $mutasi): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                <?= time_ago($mutasi['created_at']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= esc($mutasi['product_name']) ?></strong>
                                                <small class="d-block text-muted"><?= esc($mutasi['product_sku']) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?= format_movement_badge($mutasi['type']) ?>
                                        </td>
                                        <td>
                                            <strong class="<?= $mutasi['type'] === 'IN' ? 'text-success' : 'text-danger' ?>">
                                                <?= $mutasi['type'] === 'IN' ? '+' : '-' ?><?= number_format($mutasi['quantity']) ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted">Belum ada aktivitas</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Produk Stok Rendah -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-exclamation-triangle-fill text-warning"></i> Produk Stok Rendah</h5>
                <a href="<?= base_url('/stock/alerts') ?>" class="btn btn-sm btn-outline-warning">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($low_stock_products)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="40%">Produk</th>
                                    <th width="20%">Stok</th>
                                    <th width="20%">Min</th>
                                    <th width="20%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock_products as $produk): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <div class="avatar-content bg-warning text-white">
                                                        <i class="bi bi-box"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong><?= esc($produk['name']) ?></strong>
                                                    <small class="d-block text-muted"><?= esc($produk['category_name']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="<?= $produk['current_stock'] == 0 ? 'text-danger' : 'text-warning' ?>">
                                                <?= number_format($produk['current_stock']) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?= number_format($produk['min_stock']) ?></span>
                                        </td>
                                        <td>
                                            <?= format_stock_badge($produk['current_stock'], $produk['min_stock']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong><?= count($low_stock_products) ?></strong> produk membutuhkan restok segera.
                            <a href="<?= base_url('/stock/in') ?>" class="alert-link">Tambah stok sekarang</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        <h6 class="text-success mt-2">Semua Stok Normal</h6>
                        <p class="text-muted">Tidak ada produk dengan stok rendah</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!-- Top Produk Berdasarkan Nilai -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-trophy-fill text-warning"></i> Top Produk Berdasarkan Nilai</h5>
                <a href="<?= base_url('/reports/valuation') ?>" class="btn btn-sm btn-outline-primary">
                    Laporan Detail
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($top_products)): ?>
                    <div class="row">
                        <?php foreach ($top_products as $peringkat => $produkTeratas): ?>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rank-badge me-3">
                                                <span class="badge bg-<?= $peringkat == 0 ? 'warning' : ($peringkat == 1 ? 'secondary' : 'dark') ?> fs-6">
                                                    #<?= $peringkat + 1 ?>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?= esc($produkTeratas['name']) ?></h6>
                                                <small class="text-muted"><?= esc($produkTeratas['category_name']) ?></small>
                                                <div class="mt-2">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Stok:</small>
                                                            <strong class="d-block"><?= number_format($produkTeratas['current_stock']) ?></strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Nilai:</small>
                                                            <strong class="d-block text-success">
                                                                <?= format_currency($produkTeratas['total_value']) ?>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-graph-down fs-1 text-muted"></i>
                        <p class="text-muted">Belum ada data produk dengan nilai tinggi</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Grafik Pergerakan Stok (Line Chart) ──────────────────────
        const ctxPergerakan = document.getElementById('grafikPergerakanStok').getContext('2d');
        const grafikPergerakan = new Chart(ctxPergerakan, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_data['monthly_movements']['labels']) ?>,
                datasets: [{
                    label: 'Barang Masuk',
                    data: <?= json_encode($chart_data['monthly_movements']['stock_in']) ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Barang Keluar',
                    data: <?= json_encode($chart_data['monthly_movements']['stock_out']) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(nilai) {
                                return nilai.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // ── Grafik Status Stok (Doughnut) ───────────────────────────
        const ctxStatus = document.getElementById('grafikStatusStok').getContext('2d');
        const grafikStatus = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chart_data['stock_status_pie']['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($chart_data['stock_status_pie']['data']) ?>,
                    backgroundColor: <?= json_encode($chart_data['stock_status_pie']['colors']) ?>,
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        // ── Pembaruan otomatis setiap 30 detik ──────────────────────
        setInterval(perbaruiStatistik, 30000);

        function perbaruiStatistik() {
            fetch('<?= base_url('/api/dashboard/stats') ?>')
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        document.getElementById('totalProduk').textContent =
                            data.stats.total_products.toLocaleString('id-ID');
                    }
                })
                .catch(() => console.warn('Gagal memperbarui statistik dashboard'));
        }

        // ── Ekspor Grafik ────────────────────────────────────────────
        window.eksporGrafik = function(jenis) {
            let grafik, namaFile;

            if (jenis === 'pergerakan') {
                grafik    = grafikPergerakan;
                namaFile  = 'grafik-pergerakan-stok.png';
            } else if (jenis === 'status') {
                grafik    = grafikStatus;
                namaFile  = 'grafik-status-stok.png';
            }

            if (grafik) {
                const tautan = document.createElement('a');
                tautan.download = namaFile;
                tautan.href     = grafik.toBase64Image();
                tautan.click();
            }
        };

        // ── Notifikasi stok rendah ───────────────────────────────────
        <?php if ($low_stock_count > 0): ?>
        if ('Notification' in window) {
            Notification.requestPermission().then(izin => {
                if (izin === 'granted') {
                    setInterval(() => {
                        new Notification('Peringatan Inventori', {
                            body: '<?= $low_stock_count ?> produk memiliki stok rendah',
                            icon: '<?= base_url("assets/static/images/logo/favicon.png") ?>'
                        });
                    }, 300000); // tiap 5 menit
                }
            });
        }
        <?php endif ?>
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .quick-stat h4 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .btn-lg .d-block {
        line-height: 1.2;
    }

    .btn-lg strong {
        font-size: 1rem;
    }

    .btn-lg small {
        font-size: 0.8rem;
        opacity: 0.8;
    }

    .stock-legend {
        text-align: center;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        margin-bottom: 8px;
    }

    .avatar-content {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
    }

    .rank-badge .badge {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1rem;
        font-weight: bold;
    }

    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
    }

    @keyframes skeleton-loading {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    @media (max-width: 768px) {
        .quick-stat h4 { font-size: 1.5rem; }
        .btn-lg        { padding: 0.75rem; }
        .btn-lg strong { font-size: 0.9rem; }
        .btn-lg small  { font-size: 0.75rem; }
    }
</style>
<?= $this->endSection() ?>