<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ── Landing Page (redirect to dashboard) ────────────────────────────
$routes->get('/', 'DasborController::index', ['filter' => 'auth']);

// ── Auth ─────────────────────────────────────────────────────────────
$routes->get('login', 'AuthController::index');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// ── Dashboard ────────────────────────────────────────────────────────
$routes->get('dashboard', 'DasborController::index', ['filter' => 'auth']);
$routes->get('api/dashboard/stats', 'Api\DasborController::getStats');

//category
$routes->group('categories', function ($routes) {
    $routes->get('/', 'KategoriController::index');
    $routes->get('create', 'KategoriController::create');
    $routes->post('store', 'KategoriController::store');
    $routes->get('edit/(:num)', 'KategoriController::edit/$1');
    $routes->post('update/(:num)', 'KategoriController::update/$1');
    $routes->delete('delete/(:num)', 'KategoriController::delete/$1');
});

//product
$routes->group('products', function ($routes) {
    $routes->get('/', 'ProdukController::index');
    $routes->get('create', 'ProdukController::create');
    $routes->get('show/(:num)', 'ProdukController::show/$1');
    $routes->post('store', 'ProdukController::store');
    $routes->get('edit/(:num)', 'ProdukController::edit/$1');
    $routes->post('update/(:num)', 'ProdukController::update/$1');
    $routes->delete('delete/(:num)', 'ProdukController::delete/$1');
    $routes->post('generate-sku', 'ProdukController::generateSKU');

    // Procucts export routes
    $routes->get('export/excel', 'ProdukController::exportExcel');
    $routes->get('export/pdf', 'ProdukController::exportPDF');
    $routes->get('export/(:num)', 'ProdukController::exportSingle/$1');
});

//Stock Management
$routes->get('stock', 'StokController::movements');

$routes->group('stock', function ($routes) {
    $routes->get('movements', 'StokController::movements');
    $routes->get('in', 'StokController::stockIn');
    $routes->post('in/store', 'StokController::storeStockIn');
    $routes->get('out', 'StokController::stockOut');
    $routes->post('out/store', 'StokController::storeStockOut');
    $routes->get('history', 'StokController::history');
    $routes->get('history/export/(:alpha)', 'StokController::exportHistory/$1');
    $routes->get('adjustment', 'StokController::adjustment');
    $routes->post('adjustment/store', 'StokController::storeAdjustment');
    $routes->get('alerts', 'StokController::alerts');
    $routes->get('product/(:num)', 'StokController::getProductStock/$1');
});

//Reports
$routes->group('reports', function ($routes) {
    $routes->get('stock', 'LaporanController::stock');
    $routes->get('movements', 'LaporanController::movements');
    $routes->get('export/stock', 'LaporanController::exportStock');
    $routes->get('export/movements', 'LaporanController::exportMovements');
    $routes->get('valuation', 'LaporanController::valuation');
    $routes->get('analytics', 'LaporanController::analytics');
});

//Api routes untuk ajax
$routes->group('api', function ($routes) {
    $routes->get('products/search', 'Api\ProdukController::search');
    $routes->get('categories/active', 'Api\KategoriController::getActive');
    $routes->get('product/(:num)/info', 'Api\StokController::getProductInfo/$1');
    $routes->get('alerts/count', 'Api\StokController::getAlertsCount');
    $routes->post('bulk/in', 'Api\StokController::bulkStockIn');
    $routes->post('bulk/out', 'Api\StokController::bulkStockOut');
});

// Products API routes
$routes->group('api/products', function ($routes) {
    $routes->get('search', 'Api\ProdukController::search');
    $routes->get('stock-status/(:num)', 'Api\ProdukController::getStockStatus/$1');
    $routes->get('by-category/(:num)', 'Api\ProdukController::getByCategory/$1');
});

// Permintaan ATK
$routes->group('requests', function ($routes) {
    $routes->get('/', 'PermintaanController::index');
    $routes->get('create', 'PermintaanController::create');
    $routes->post('store', 'PermintaanController::store');
    $routes->get('show/(:num)', 'PermintaanController::show/$1');
    $routes->post('approve/(:num)', 'PermintaanController::approve/$1');
    $routes->post('distribute/(:num)', 'PermintaanController::distribute/$1');
    $routes->post('cancel/(:num)', 'PermintaanController::cancel/$1');
});

// Users Management
$routes->group('users', function ($routes) {
    $routes->get('/', 'PenggunaController::index');
    $routes->get('create', 'PenggunaController::create');
    $routes->post('store', 'PenggunaController::store');
    $routes->get('edit/(:num)', 'PenggunaController::edit/$1');
    $routes->post('update/(:num)', 'PenggunaController::update/$1');
    $routes->delete('delete/(:num)', 'PenggunaController::delete/$1');
});

// Settings
$routes->get('settings', 'PengaturanController::index');
$routes->post('settings/update', 'PengaturanController::update');

// Notifications
$routes->group('notifications', function ($routes) {
    $routes->get('/', 'NotifikasiController::index');
    $routes->get('read/(:num)', 'NotifikasiController::read/$1');
    $routes->get('mark-all-read', 'NotifikasiController::markAllRead');
    $routes->delete('delete/(:num)', 'NotifikasiController::delete/$1');
    $routes->post('clean-old', 'NotifikasiController::cleanOld');
});

// Notifications API
$routes->group('api/notifications', function ($routes) {
    $routes->get('/', 'Api\NotifikasiController::latest');
    $routes->get('count', 'Api\NotifikasiController::count');
});
