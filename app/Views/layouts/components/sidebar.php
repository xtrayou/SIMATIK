<div id="sidebar">
    <div class="sidebar-wrapper">
        <!-- Sidebar Header -->
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="<?= base_url('dashboard') ?>">
                        <h4><i class="bi bi-box-seam-fill"></i> SIMA<span style="color: #435ebe;">TIK</span></h4>
                        <span class="fw-bold" style="font-size: 0.75rem; color: #6c757d;">Sistem Inventaris ATK</span>
                    </a>
                </div>
                <!-- Dark Mode Toggle -->
                <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        aria-hidden="true" role="img" class="iconify iconify--system-uicons"
                        width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                        <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2" opacity=".3"></path>
                            <g transform="translate(-210 -1)">
                                <path d="M220.5 2.5v2m6.5.5l-1.5 1.5M219.5 11.5h-2m13 0h2m-5.5 5.5l1.5 1.5m-9.5-1.5l-1.5 1.5m8.5-11.5l1.5-1.5m-9.5 1.5l-1.5-1.5"></path>
                                <circle cx="220.5" cy="11.5" r="4"></circle>
                            </g>
                        </g>
                    </svg>
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                        <label class="form-check-label"></label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidebar-menu">
            <ul class="menu">

                <!-- Dashboard -->
                <li class="sidebar-title">Dashboard</li>
                <li class="sidebar-item <?= (strpos(uri_string(), 'dashboard') !== false || uri_string() == '' || uri_string() == '/') ? 'active' : '' ?>">
                    <a href="<?= base_url('dashboard') ?>" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Master Data Section -->
                <li class="sidebar-title">Master Data</li>

                <!-- Categories -->
                <li class="sidebar-item <?= (strpos(uri_string(), 'categories') !== false) ? 'active' : '' ?>">
                    <a href="<?= base_url('/categories') ?>" class='sidebar-link'>
                        <i class="bi bi-collection-fill"></i>
                        <span>Kategori Produk</span>
                        <span class="badge bg-primary ms-auto" id="category-count">
                            <?php
                            $categoryModel = new \App\Models\CategoryModel();
                            echo $categoryModel->where('is_active', true)->countAllResults();
                            ?>
                        </span>
                    </a>
                </li>

                <!-- Products -->
                <li class="sidebar-item <?= (strpos(uri_string(), 'products') !== false) ? 'active' : '' ?>">
                    <a href="<?= base_url('/products') ?>" class='sidebar-link'>
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Produk</span>
                        <span class="badge bg-success ms-auto" id="product-count">
                            <?php
                            $productModel = new \App\Models\ProductModel();
                            echo $productModel->where('is_active', true)->countAllResults();
                            ?>
                        </span>
                    </a>
                </li>

                <!-- Stock Management Section -->
                <li class="sidebar-title">Manajemen Stok</li>

                <!-- Stock Movements (Unified - No Submenu) -->
                <li class="sidebar-item <?= (strpos(uri_string(), 'stock') !== false && uri_string() != 'stock/adjustment') ? 'active' : '' ?>">
                    <a href="<?= base_url('/stock') ?>" class='sidebar-link'>
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Pergerakan Stok</span>
                    </a>
                </li>

                <!-- Stock Adjustment -->
                <li class="sidebar-item <?= (uri_string() == 'stock/adjustment') ? 'active' : '' ?>">
                    <a href="<?= base_url('/stock/adjustment') ?>" class='sidebar-link'>
                        <i class="bi bi-tools"></i>
                        <span>Penyesuaian Stok</span>
                    </a>
                </li>

                <!-- Permintaan ATK -->
                <li class="sidebar-title">Permintaan ATK</li>

                <li class="sidebar-item <?= (strpos(uri_string(), 'loans') !== false) ? 'active' : '' ?>">
                    <a href="<?= base_url('/loans') ?>" class='sidebar-link'>
                        <i class="bi bi-journal-arrow-down"></i>
                        <span>Permintaan ATK</span>
                    </a>
                </li>

                <!-- Reports Section -->
                <li class="sidebar-title">Laporan</li>

                <li class="sidebar-item has-sub <?= (strpos(uri_string(), 'reports') !== false) ? 'active' : '' ?>">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Laporan</span>
                    </a>
                    <ul class="submenu <?= (strpos(uri_string(), 'reports') !== false) ? 'submenu-open' : '' ?>">
                        <li class="submenu-item <?= (uri_string() == 'reports/stock') ? 'active' : '' ?>">
                            <a href="<?= base_url('/reports/stock') ?>" class="submenu-link">
                                <i class="bi bi-box"></i>
                                <span>Laporan Stok</span>
                            </a>
                        </li>
                        <li class="submenu-item <?= (uri_string() == 'reports/movements') ? 'active' : '' ?>">
                            <a href="<?= base_url('/reports/movements') ?>" class="submenu-link">
                                <i class="bi bi-arrow-repeat"></i>
                                <span>Laporan Pergerakan</span>
                            </a>
                        </li>
                        <li class="submenu-item <?= (uri_string() == 'reports/valuation') ? 'active' : '' ?>">
                            <a href="<?= base_url('/reports/valuation') ?>" class="submenu-link">
                                <i class="bi bi-currency-dollar"></i>
                                <span>Valuasi Inventory</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Settings Section -->
                <li class="sidebar-title">Pengaturan</li>

                <li class="sidebar-item">
                    <a href="<?= base_url('/settings') ?>" class='sidebar-link'>
                        <i class="bi bi-gear-fill"></i>
                        <span>Pengaturan Sistem</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="<?= base_url('/users') ?>" class='sidebar-link'>
                        <i class="bi bi-people-fill"></i>
                        <span>Manajemen User</span>
                    </a>
                </li>

                <!-- Help & Support -->
                <li class="sidebar-title">Bantuan</li>

                <li class="sidebar-item">
                    <a href="<?= base_url('/help') ?>" class='sidebar-link'>
                        <i class="bi bi-question-circle-fill"></i>
                        <span>Bantuan</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<style>
    .logo span {
        font-size: 1rem;
        color: #435ebe;
    }

    .theme-toggle {
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .theme-toggle {
            display: none;
        }
    }
</style>