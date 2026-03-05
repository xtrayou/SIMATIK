<header class='mb-3'>
    <nav class="navbar navbar-expand navbar-light navbar-top">
        <div class="container-fluid">
            <!-- Sidebar Toggle Button -->
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <!-- Mobile Navbar Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <!-- Search Bar (Center) -->
                <div class="navbar-nav me-auto">
                    <div class="nav-item">
                        <form class="d-flex" role="search" id="globalSearchForm">
                            <div class="input-group">
                                <input class="form-control" type="search" placeholder="Cari produk, kategori..."
                                    aria-label="Search" id="globalSearch" style="min-width: 300px;">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Side Navigation -->
                <ul class="navbar-nav ms-auto mb-lg-0">

                    <!-- Quick Add Dropdown -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-plus-circle fs-4 text-success"></i>
                            <span class="d-none d-md-inline ms-1">Quick Add</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-lightning-fill"></i> Tambah Cepat
                                </h6>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/categories/create') ?>">
                                    <i class="bi bi-collection me-2"></i> Kategori Baru
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/products/create') ?>">
                                    <i class="bi bi-box me-2"></i> Produk Baru
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/stock/in') ?>">
                                    <i class="bi bi-arrow-down-circle text-success me-2"></i> Barang Masuk
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/stock/out') ?>">
                                    <i class="bi bi-arrow-up-circle text-danger me-2"></i> Barang Keluar
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-4"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                id="notification-badge">
                                <?php
                                $navRole = session()->get('role') ?? 'staff';
                                $navNotifModel = new \App\Models\NotificationModel();
                                $navNotifCount = 0;
                                $navNotifications = [];
                                try {
                                    $navNotifCount = $navNotifModel->countUnreadForRole($navRole);
                                    $navNotifications = $navNotifModel->getUnreadForRole($navRole, 5);
                                } catch (\Exception $e) {
                                    // Tabel belum ada, fallback ke low stock
                                    $productModel = new \App\Models\ProductModel();
                                    $navNotifCount = count($productModel->getLowStockProducts());
                                }
                                echo $navNotifCount > 0 ? $navNotifCount : '';
                                ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="min-width: 350px;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bi bi-bell"></i> Notifikasi
                                </h6>
                                <small class="text-muted"><?= $navNotifCount ?> baru</small>
                            </li>

                            <?php if (!empty($navNotifications)): ?>
                                <?php foreach ($navNotifications as $notif): ?>
                                    <li>
                                        <a class="dropdown-item notification-item py-3"
                                            href="<?= base_url('/notifications/read/' . $notif['id']) ?>">
                                            <div class="d-flex">
                                                <div class="notification-icon bg-<?= esc($notif['color']) ?> me-3">
                                                    <i class="bi <?= esc($notif['icon']) ?> text-white"></i>
                                                </div>
                                                <div class="notification-content flex-grow-1">
                                                    <h6 class="notification-title mb-1"><?= esc($notif['title']) ?></h6>
                                                    <p class="notification-text mb-1">
                                                        <?= esc($notif['message']) ?>
                                                    </p>
                                                    <small class="text-muted">
                                                        <?= waktu_lalu($notif['created_at']) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li class="d-flex justify-content-between px-3 py-2">
                                    <a class="text-primary small" href="<?= base_url('/notifications') ?>">
                                        <i class="bi bi-eye"></i> Lihat Semua
                                    </a>
                                    <a class="text-muted small" href="<?= base_url('/notifications/mark-all-read') ?>">
                                        <i class="bi bi-check-all"></i> Tandai Dibaca
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <div class="dropdown-item-text text-center py-4">
                                        <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
                                        <p class="mb-0">Tidak ada notifikasi baru</p>
                                        <small class="text-muted">Semua notifikasi sudah dibaca</small>
                                    </div>
                                </li>
                            <?php endif ?>
                        </ul>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-menu d-flex align-items-center">
                                <div class="user-name text-end me-3 d-none d-md-block">
                                    <h6 class="mb-0 text-gray-600"><?= session()->get('name') ?? 'Guest' ?></h6>
                                    <p class="mb-0 text-sm text-gray-600"><?= ucfirst(session()->get('role') ?? 'User') ?></p>
                                </div>
                                <div class="user-img d-flex align-items-center">
                                    <div class="avatar avatar-md">
                                        <img src="<?= base_url('assets/static/images/faces/1.jpg') ?>"
                                            alt="User Avatar" class="rounded-circle">
                                    </div>
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px;">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-person-circle"></i> Hello, <?= explode(' ', (session()->get('name') ?? 'User'))[0] ?>!
                                </h6>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/profile') ?>">
                                    <i class="bi bi-person me-2"></i> Profile Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/settings') ?>">
                                    <i class="bi bi-gear me-2"></i> Pengaturan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/activity-log') ?>">
                                    <i class="bi bi-clock-history me-2"></i> Log Aktivitas
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= base_url('/auth/logout') ?>"
                                    onclick="return confirm('Yakin ingin logout?')">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<style>
    /* Navbar Custom Styles */
    .notification-dropdown {
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-item:hover {
        background-color: rgba(67, 94, 190, 0.05);
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notification-title {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .notification-text {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .user-menu:hover {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .avatar img {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }

    @media (max-width: 768px) {
        .user-name {
            display: none !important;
        }

        #globalSearch {
            min-width: 200px !important;
        }

        .notification-dropdown {
            min-width: 280px !important;
        }
    }

    /* Global Search Enhancements */
    #globalSearchForm .input-group {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    #globalSearch:focus {
        border-color: #435ebe;
        box-shadow: none;
    }

    .btn-outline-primary:hover {
        background-color: #435ebe;
        border-color: #435ebe;
    }
</style>

<script>
    // Global Search Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const globalSearchForm = document.getElementById('globalSearchForm');
        const globalSearch = document.getElementById('globalSearch');

        globalSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = globalSearch.value.trim();

            if (query.length > 0) {
                // Redirect to products page with search query
                window.location.href = `<?= base_url('/products') ?>?search=${encodeURIComponent(query)}`;
            }
        });

        // Auto-suggest functionality (optional enhancement)
        let searchTimeout;
        globalSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length >= 2) {
                searchTimeout = setTimeout(function() {
                    // Implement auto-suggest here if needed
                    console.log('Searching for:', query);
                }, 300);
            }
        });
    });
</script>