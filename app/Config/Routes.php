<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ── Landing Page (redirect to dashboard) ────────────────────────────
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);

// ── Auth ─────────────────────────────────────────────────────────────
$routes->get('login', 'AuthController::index');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// ── Dashboard ────────────────────────────────────────────────────────
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('api/dashboard/stats', 'Api\DashboardController::getStats');

//category
$routes->group('categories', function ($routes) {
    $routes->get('/', 'CategoryController::index');
    $routes->get('create', 'CategoryController::create');
    $routes->post('store', 'CategoryController::store');
    $routes->get('edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('update/(:num)', 'CategoryController::update/$1');
    $routes->delete('delete/(:num)', 'CategoryController::delete/$1');
});

//product
$routes->group('products', function ($routes) {
    $routes->get('/', 'ProductController::index');
    $routes->get('create', 'ProductController::create');
    $routes->get('show/(:num)', 'ProductController::show/$1');
    $routes->post('store', 'ProductController::store');
    $routes->get('edit/(:num)', 'ProductController::edit/$1');
    $routes->post('update/(:num)', 'ProductController::update/$1');
    $routes->delete('delete/(:num)', 'ProductController::delete/$1');
    $routes->post('generate-sku', 'ProductController::generateSKU');

    // Procucts export routes
    $routes->get('export/excel', 'ProductController::exportExcel');
    $routes->get('export/pdf', 'ProductController::exportPDF');
    $routes->get('export/(:num)', 'ProductController::exportSingle/$1');
});

//Stock Management
$routes->get('stock', 'StockController::movements');

$routes->group('stock', function ($routes) {
    $routes->get('movements', 'StockController::movements');
    $routes->get('in', 'StockController::stockIn');
    $routes->post('in/store', 'StockController::storeStockIn');
    $routes->get('out', 'StockController::stockOut');
    $routes->post('out/store', 'StockController::storeStockOut');
    $routes->get('history', 'StockController::history');
    $routes->get('history/export/(:alpha)', 'StockController::exportHistory/$1');
    $routes->get('adjustment', 'StockController::adjustment');
    $routes->post('adjustment/store', 'StockController::storeAdjustment');
    $routes->get('alerts', 'StockController::alerts');
    $routes->get('product/(:num)', 'StockController::getProductStock/$1');
});

//Reports
$routes->group('reports', function ($routes) {
    $routes->get('stock', 'ReportController::stock');
    $routes->get('movements', 'ReportController::movements');
    $routes->get('export/stock', 'ReportController::exportStock');
    $routes->get('export/movements', 'ReportController::exportMovements');
    $routes->get('valuation', 'ReportController::valuation');
    $routes->get('analytics', 'ReportController::analytics');
});

//Api routes untuk ajax
$routes->group('api', function ($routes) {
    $routes->get('products/search', 'Api\ProductController::search');
    $routes->get('categories/active', 'Api\CategoryController::getActive');
    $routes->get('product/(:num)/info', 'Api\StockController::getProductInfo/$1');
    $routes->get('alerts/count', 'Api\StockController::getAlertsCount');
    $routes->post('bulk/in', 'Api\StockController::bulkStockIn');
    $routes->post('bulk/out', 'Api\StockController::bulkStockOut');
});

// Products API routes
$routes->group('api/products', function ($routes) {
    $routes->get('search', 'Api\ProductController::search');
    $routes->get('stock-status/(:num)', 'Api\ProductController::getStockStatus/$1');
    $routes->get('by-category/(:num)', 'Api\ProductController::getByCategory/$1');
});

// Permintaan ATK
$routes->group('loans', function ($routes) {
    $routes->get('/', 'LoanController::index');
    $routes->get('create', 'LoanController::create');
    $routes->post('store', 'LoanController::store');
    $routes->get('show/(:num)', 'LoanController::show/$1');
    $routes->post('approve/(:num)', 'LoanController::approve/$1');
    $routes->post('cancel/(:num)', 'LoanController::cancel/$1');
});

// Users Management
$routes->group('users', function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->get('create', 'UserController::create');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->delete('delete/(:num)', 'UserController::delete/$1');
});

// Settings
$routes->get('settings', 'SettingController::index');
$routes->post('settings/update', 'SettingController::update');

// Notifications
$routes->group('notifications', function ($routes) {
    $routes->get('/', 'NotificationController::index');
    $routes->get('read/(:num)', 'NotificationController::read/$1');
    $routes->get('mark-all-read', 'NotificationController::markAllRead');
    $routes->delete('delete/(:num)', 'NotificationController::delete/$1');
    $routes->post('clean-old', 'NotificationController::cleanOld');
});

// Notifications API
$routes->group('api/notifications', function ($routes) {
    $routes->get('/', 'Api\NotificationController::latest');
    $routes->get('count', 'Api\NotificationController::count');
});
