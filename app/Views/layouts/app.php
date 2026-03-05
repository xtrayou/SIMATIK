<?php
// File: app/Views/layouts/app.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIMATIK' ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/static/images/logo/favicon.svg') ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?= base_url('assets/static/images/logo/favicon.png') ?>" type="image/png">
    
    <!-- Mazer CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app-dark.css') ?>">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') ?>">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <style>
        /* Inventory System Custom Styles */
        .inventory-card {
            border-left: 4px solid #435ebe;
            transition: all 0.3s ease;
        }
        
        .inventory-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 94, 190, 0.15);
        }
        
        .low-stock {
            border-left-color: #dc3545 !important;
        }
        
        .alert-stock {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        /* Sidebar Active State */
        .sidebar-item.active > .sidebar-link {
            background-color: #435ebe !important;
            color: white !important;
            border-radius: 8px;
        }
        
        .sidebar-item.active > .sidebar-link i {
            color: white !important;
        }
        
        /* Avatar Content */
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
        
        /* Table Improvements */
        .table td {
            vertical-align: middle;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        /* Button Group */
        .btn-group .btn {
            margin-right: 2px;
        }
        
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        
        /* Form Enhancements */
        .form-control:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
        }
        
        .btn-primary {
            background-color: #435ebe;
            border-color: #435ebe;
        }
        
        .btn-primary:hover {
            background-color: #364296;
            border-color: #364296;
        }
        
        /* Card Statistics */
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .stats-icon.purple { background-color: rgba(102, 16, 242, 0.1); color: #6610f2; }
        .stats-icon.blue { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .stats-icon.green { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .stats-icon.red { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
        
        /* Flash Messages Enhancement */
        .alert {
            border: none;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            border-left: 4px solid #198754;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
        
        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        
        .alert-info {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            border-left: 4px solid #0d6efd;
        }
        
        /* Loading States */
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Responsive Improvements */
        @media (max-width: 768px) {
            .stats-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn-group {
                display: flex;
                flex-direction: column;
            }
            
            .btn-group .btn {
                margin-bottom: 2px;
                margin-right: 0;
            }
        }
        
        /* Dark Mode Support */
        [data-bs-theme="dark"] .inventory-card {
            border-left-color: #6196ff;
        }
        
        [data-bs-theme="dark"] .stats-icon.purple { 
            background-color: rgba(102, 16, 242, 0.2); 
        }
        
        [data-bs-theme="dark"] .stats-icon.blue { 
            background-color: rgba(13, 110, 253, 0.2); 
        }
        
        [data-bs-theme="dark"] .stats-icon.green { 
            background-color: rgba(25, 135, 84, 0.2); 
        }
        
        [data-bs-theme="dark"] .stats-icon.red { 
            background-color: rgba(220, 53, 69, 0.2); 
        }
        
        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
    </style>
    
    <!-- Section for additional styles from pages -->
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Theme initialization script -->
    <script src="<?= base_url('assets/static/js/initTheme.js') ?>"></script>
    
    <div id="app">
        <!-- Sidebar Component -->
        <?= $this->include('layouts/components/sidebar') ?>
        
        <!-- Main Content Area -->
        <div id="main" class='layout-navbar navbar-fixed'>
            
            <!-- Top Navigation Bar -->
            <?= $this->include('layouts/components/navbar') ?>
            
            <!-- Main Content -->
            <div id="main-content">
                
                <!-- Page Header -->
                <div class="page-heading">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3><?= $page_title ?? 'Dashboard' ?></h3>
                                <p class="text-subtitle text-muted"><?= $page_subtitle ?? '' ?></p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <?= $this->renderSection('breadcrumb') ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                <div class="container-fluid">
                    <?= $this->include('layouts/components/alerts') ?>
                </div>
                
                <!-- Page Content -->
                <section class="section">
                    <div class="fade-in">
                        <?= $this->renderSection('content') ?>
                    </div>
                </section>
                
            </div>

            <!-- Footer -->
            <?= $this->include('layouts/components/footer') ?>
            
        </div>
    </div>
    
    <!-- Core JavaScript Files -->
    <script src="<?= base_url('assets/static/js/components/dark.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
    
    <!-- jQuery (Required for DataTables and other plugins) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables JavaScript -->
    <script src="<?= base_url('assets/extensions/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') ?>"></script>
    
    <!-- Chart.js for Dashboard -->
    <script src="<?= base_url('assets/extensions/chart.js/chart.umd.js') ?>"></script>
    
    <!-- SweetAlert2 for Beautiful Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Global JavaScript -->
    <script>
        $(document).ready(function() {
            
            // Auto-hide flash messages after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // // Initialize all DataTables with default config
            // $('.datatable').each(function() {
            //     if (!$.fn.DataTable.isDataTable(this)) {
            //         $(this).DataTable({
            //             responsive: true,
            //             language: {
            //                 url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            //             },
            //             pageLength: 25,
            //             lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            //             dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            //             columnDefs: [
            //                 { orderable: false, targets: 'no-sort' }
            //             ]
            //         });
            //     }
            // });
            
            // Initialize all tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize all popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Global AJAX error handler
            $(document).ajaxError(function(event, xhr, settings, thrownError) {
                console.error('AJAX Error:', thrownError);
                
                if (xhr.status === 419) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Session Expired',
                        text: 'Silakan refresh halaman dan coba lagi.',
                        confirmButtonColor: '#435ebe'
                    }).then(() => {
                        location.reload();
                    });
                } else if (xhr.status >= 500) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                        confirmButtonColor: '#435ebe'
                    });
                }
            });
            
            // Form submission loading state helper
            $('form').on('submit', function() {
                const $submitBtn = $(this).find('button[type="submit"]');
                const originalText = $submitBtn.html();
                
                $submitBtn.prop('disabled', true)
                         .html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                
                // Auto enable after 10 seconds (fallback)
                setTimeout(function() {
                    $submitBtn.prop('disabled', false).html(originalText);
                }, 10000);
            });
            
            // Number formatting helper
            window.formatCurrency = function(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            };
            
            // Time ago helper
            window.timeAgo = function(dateString) {
                const now = new Date();
                const date = new Date(dateString);
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) return 'baru saja';
                if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' menit yang lalu';
                if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' jam yang lalu';
                if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 86400) + ' hari yang lalu';
                if (diffInSeconds < 31104000) return Math.floor(diffInSeconds / 2592000) + ' bulan yang lalu';
                return Math.floor(diffInSeconds / 31104000) + ' tahun yang lalu';
            };
            
            // Global loading helper
            window.showLoading = function(text = 'Loading...') {
                Swal.fire({
                    title: text,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            };
            
            // Global success helper  
            window.showSuccess = function(title, text, callback) {
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: text,
                    timer: 2000,
                    showConfirmButton: false
                }).then(callback);
            };
            
            // Global error helper
            window.showError = function(title, text) {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: text,
                    confirmButtonColor: '#435ebe'
                });
            };
            
            // Global confirm helper
            window.showConfirm = function(title, text, callback) {
                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: text,
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed && callback) {
                        callback();
                    }
                });
            };
        });
    </script>
    
    <!-- Section for additional scripts from pages -->
    <?= $this->renderSection('scripts') ?>
</body>

</html>