<?php

/**
 * Script untuk memperbaiki status permintaan yang NULL atau kosong
 * Jalankan: php fix_request_status.php
 */

// Path ke autoload
require __DIR__ . '/vendor/autoload.php';

// Bootstrap minimal CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
$paths = new Config\Paths();
$paths->systemDirectory = __DIR__ . '/vendor/codeigniter4/framework/system';

require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Inisialisasi database
$app = Config\Services::codeigniter();
$app->initialize();
$db = Config\Database::connect();

echo "🔧 Memperbaiki status permintaan...\n\n";

// Update status yang NULL atau kosong menjadi 'requested'
try {
    $sql = "UPDATE requests SET status = 'requested' WHERE status IS NULL OR status = '' OR status = 'pending'";
    $db->query($sql);
    $affectedRows = $db->affectedRows();

    echo "✅ Berhasil! {$affectedRows} data diperbaiki.\n\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Tampilkan statistik
try {
    $stats = $db->query("SELECT status, COUNT(*) as total FROM requests GROUP BY status")->getResultArray();
    echo "📊 Statistik Status Permintaan:\n";
    echo str_repeat("-", 40) . "\n";

    if (empty($stats)) {
        echo "Tidak ada data permintaan.\n";
    } else {
        foreach ($stats as $row) {
            $status = $row['status'] ?: '(kosong)';
            echo sprintf("%-20s: %d\n", $status, $row['total']);
        }
    }
} catch (Exception $e) {
    echo "Error saat mengambil statistik: " . $e->getMessage() . "\n";
}

echo "\n✨ Selesai!\n";
