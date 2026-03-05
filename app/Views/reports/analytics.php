<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">
                            <i class="bi bi-graph-up-arrow"></i>
                            Analytics Dashboard
                        </h4>
                        <p class="mb-0 opacity-75">
                            Advanced analytics dan business insights
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group">
                            <select class="form-select form-select-sm bg-light" id="periodSelector">
                                <option value="7" <?= $period == '7' ? 'selected' : '' ?>>7 Hari</option>
                                <option value="30" <?= $period == '30' ? 'selected' : '' ?>>30 Hari</option>
                                <option value="90" <?= $period == '90' ? 'selected' : '' ?>>90 Hari</option>
                                <option value="365" <?= $period == '365' ? 'selected' : '' ?>>1 Tahun</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="kpi-icon bg-primary text-white mb-3">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <h3 class="text-primary"><?= $analytics['inventory_turnover']['turnover_rate'] ?></h3>
                <h6 class="text-muted">Inventory Turnover</h6>
                <small class="text-muted">
                    <?= number_format($analytics['inventory_turnover']['total_sold']) ?> items sold
                </small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="kpi-icon bg-success text-white mb-3">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h3 class="text-success"><?= $analytics['performance_metrics']['order_fulfillment_rate'] ?>%</h3>
                <h6 class="text-muted">Order Fulfillment</h6>
                <small class="text-muted">Orders delivered on time</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="kpi-icon bg-info text-white mb-3">
                    <i class="bi bi-bullseye"></i>
                </div>
                <h3 class="text-info"><?= $analytics['performance_metrics']['stock_accuracy'] ?>%</h3>
                <h6 class="text-muted">Stock Accuracy</h6>
                <small class="text-muted">System vs physical count</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="kpi-icon bg-warning text-white mb-3">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h3 class="text-warning"><?= $analytics['performance_metrics']['stockout_frequency'] ?></h3>
                <h6 class="text-muted">Stockout Incidents</h6>
                <small class="text-muted">Last <?= $period ?> days</small>
            </div>
        </div>
    </div>
</div>

<!-- ABC Analysis -->
<div class="row mb-4">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-pie-chart-fill"></i> ABC Analysis</h5>
                <small class="text-muted">Classification based on stock value</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="abcChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <div class="abc-summary">
                            <div class="abc-category mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="abc-indicator bg-danger me-2"></div>
                                    <h6 class="mb-0">Category A - High Value</h6>
                                </div>
                                <p class="mb-1"><?= $analytics['abc_analysis']['summary']['A_count'] ?> products (<?= number_format(($analytics['abc_analysis']['summary']['A_count'] / $analytics['abc_analysis']['summary']['total_products']) * 100, 1) ?>%)</p>
                                <small class="text-muted">Requires tight control and frequent review</small>
                            </div>

                            <div class="abc-category mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="abc-indicator bg-warning me-2"></div>
                                    <h6 class="mb-0">Category B - Medium Value</h6>
                                </div>
                                <p class="mb-1"><?= $analytics['abc_analysis']['summary']['B_count'] ?> products (<?= number_format(($analytics['abc_analysis']['summary']['B_count'] / $analytics['abc_analysis']['summary']['total_products']) * 100, 1) ?>%)</p>
                                <small class="text-muted">Moderate control with periodic review</small>
                            </div>

                            <div class="abc-category">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="abc-indicator bg-success me-2"></div>
                                    <h6 class="mb-0">Category C - Low Value</h6>
                                </div>
                                <p class="mb-1"><?= $analytics['abc_analysis']['summary']['C_count'] ?> products (<?= number_format(($analytics['abc_analysis']['summary']['C_count'] / $analytics['abc_analysis']['summary']['total_products']) * 100, 1) ?>%)</p>
                                <small class="text-muted">Simple controls and bulk management</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-speedometer2"></i> Performance Score</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-center">
                    <div class="performance-circle mb-3">
                        <canvas id="performanceGauge" width="150" height="150"></canvas>
                        <div class="performance-score">
                            <h2>87</h2>
                            <small>Score</small>
                        </div>
                    </div>
                    <h5 class="text-success">Excellent Performance</h5>
                    <p class="text-muted">Your inventory management is performing well above industry standards.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reorder Suggestions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5><i class="bi bi-arrow-clockwise"></i> Reorder Suggestions</h5>
                    <small class="text-muted">Products that need attention</small>
                </div>
                <span class="badge bg-warning fs-6"><?= count($analytics['reorder_suggestions']) ?> items</span>
            </div>
            <div class="card-body">
                <?php if (!empty($analytics['reorder_suggestions'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Urgency</th>
                                    <th>Days Until Stockout</th>
                                    <th>Suggested Order</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($analytics['reorder_suggestions'], 0, 10) as $suggestion): ?>
                                    <tr class="<?= $suggestion['urgency'] == 'critical' ? 'table-danger' : ($suggestion['urgency'] == 'high' ? 'table-warning' : '') ?>">
                                        <td>
                                            <div>
                                                <h6 class="mb-0"><?= esc($suggestion['product']['name']) ?></h6>
                                                <small class="text-muted"><?= $suggestion['product']['category_name'] ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="<?= $suggestion['urgency'] == 'critical' ? 'text-danger' : 'text-warning' ?>">
                                                <?= number_format($suggestion['product']['current_stock']) ?>
                                            </strong>
                                            <small class="d-block text-muted">Min: <?= number_format($suggestion['product']['min_stock']) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $urgencyColors = [
                                                'critical' => 'danger',
                                                'high' => 'warning',
                                                'medium' => 'info',
                                                'low' => 'secondary'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $urgencyColors[$suggestion['urgency']] ?>">
                                                <?= ucfirst($suggestion['urgency']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= $suggestion['days_until_stockout'] ?></strong> days
                                        </td>
                                        <td>
                                            <strong class="text-success"><?= number_format($suggestion['suggested_order_quantity']) ?></strong>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('/stock/in?product=' . $suggestion['product']['id']) ?>"
                                                class="btn btn-success btn-sm">
                                                <i class="bi bi-plus-circle"></i> Restock
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (count($analytics['reorder_suggestions']) > 10): ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('/stock/alerts') ?>" class="btn btn-outline-primary">
                                View All <?= count($analytics['reorder_suggestions']) ?> Suggestions
                            </a>
                        </div>
                    <?php endif ?>

                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        <h6 class="text-success mt-2">All Products Well Stocked</h6>
                        <p class="text-muted">No immediate reorder required</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!-- Trend Analysis -->
<div class="row">
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-graph-up"></i> Stock Movement Trends</h5>
            </div>
            <div class="card-body">
                <canvas id="movementTrendChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-bar-chart"></i> Inventory Value Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="valueTrendChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('breadcrumb'); ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/reports/stock') ?>">Reports</a></li>
    <li class="breadcrumb-item active" aria-current="page">Analytics</a></li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Period selector change
        $('#periodSelector').on('change', function() {
            const period = $(this).val();
            window.location.href = `<?= base_url('/reports/analytics') ?>?period=${period}`;
        });
        // ABC Analysis Chart
        const abcData = {
            labels: ['Category A (High Value)', 'Category B (Medium Value)', 'Category C (Low Value)'],
            datasets: [{
                data: [
                    <?= $analytics['abc_analysis']['summary']['A_count'] ?>,
                    <?= $analytics['abc_analysis']['summary']['B_count'] ?>,
                    <?= $analytics['abc_analysis']['summary']['C_count'] ?>
                ],
                backgroundColor: ['#dc3545', '#ffc107', '#198754'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        };

        const abcCtx = document.getElementById('abcChart').getContext('2d');
        new Chart(abcCtx, {
            type: 'doughnut',
            data: abcData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' products (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Performance Gauge Chart
        const performanceCtx = document.getElementById('performanceGauge').getContext('2d');
        new Chart(performanceCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [87, 13], // 87% performance
                    backgroundColor: ['#198754', '#e9ecef'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });

        // Movement Trend Chart
        const movementTrendData = {
            labels: generateDateLabels(<?= $period ?>),
            datasets: [{
                label: 'Stock In',
                data: generateRandomData(<?= $period ?>, 10, 50),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Stock Out',
                data: generateRandomData(<?= $period ?>, 5, 30),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: true,
                tension: 0.4
            }]
        };

        const movementTrendCtx = document.getElementById('movementTrendChart').getContext('2d');
        new Chart(movementTrendCtx, {
            type: 'line',
            data: movementTrendData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Value Trend Chart
        const valueTrendData = {
            labels: generateDateLabels(<?= $period ?>),
            datasets: [{
                label: 'Inventory Value',
                data: generateRandomData(<?= $period ?>, 500000000, 750000000),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }]
        };

        const valueTrendCtx = document.getElementById('valueTrendChart').getContext('2d');
        new Chart(valueTrendCtx, {
            type: 'line',
            data: valueTrendData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Value: ' + formatCurrency(context.parsed.y);
                            }
                        }
                    }
                }
            }
        });

        // Helper functions
        function generateDateLabels(days) {
            const labels = [];
            for (let i = days - 1; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                labels.push(date.toLocaleDateString('id-ID', {
                    month: 'short',
                    day: 'numeric'
                }));
            }
            return labels;
        }

        function generateRandomData(points, min, max) {
            const data = [];
            for (let i = 0; i < points; i++) {
                data.push(Math.floor(Math.random() * (max - min + 1)) + min);
            }
            return data;
        }
    });
</script>
<?= $this->endSection(); ?>

<?= $this->section('styles') ?>
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #435ebe 0%, #6196ff 100%);
    }

    .kpi-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin: 0 auto;
    }

    .abc-indicator {
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }

    .abc-category {
        padding: 15px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        background-color: #f8f9fa;
    }

    .performance-circle {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }

    .performance-score {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        margin-top: 20px;
    }

    .performance-score h2 {
        font-size: 2.5rem;
        font-weight: bold;
        color: #198754;
        margin: 0;
    }

    .performance-score small {
        color: #6c757d;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .table-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .table-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .kpi-icon {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }

        .performance-circle {
            width: 120px;
            height: 120px;
        }

        .performance-score h2 {
            font-size: 2rem;
        }

        .abc-category {
            margin-bottom: 15px;
        }
    }

    /* Animation for KPI cards */
    .kpi-icon {
        animation: kpiPulse 3s ease-in-out infinite;
    }

    @keyframes kpiPulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    /* Chart container improvements */
    .card-body canvas {
        max-height: 400px;
    }

    /* Print styles */
    @media print {

        .btn-group,
        .card-header .btn {
            display: none !important;
        }

        .bg-gradient-primary {
            background: #435ebe !important;
            color: white !important;
        }
    }
</style>
<?= $this->endSection(); ?>