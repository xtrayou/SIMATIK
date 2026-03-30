<?php

/**
 * Import semua file Stock Opname Excel di "public/laporan bulanan" ke database.
 * Jalankan dari root project:
 *   php import_stock_opname_archives.php
 */

use CodeIgniter\Boot;
use Config\Paths;
use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/vendor/autoload.php';

// Bootstrap CodeIgniter 4 sesuai mekanisme baru (mirip file spark)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// Pastikan constant path & autoloader CodeIgniter ter-boot
require FCPATH . '../app/Config/Paths.php';
/** @var class-string<Paths> $pathsClass */
$pathsClass = Paths::class;
/** @var Paths $paths */
$paths = new $pathsClass();

// Boot dalam konteks console (CLI), tanpa menjalankan full HTTP request
Boot::bootConsole($paths);

$db = Config\Database::connect();

echo "\n📥 Import Stock Opname dari Excel ke database\n";
echo "============================================\n\n";

// Buat tabel arsip jika belum ada
$createTableSql = "CREATE TABLE IF NOT EXISTS stock_opname_archives (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(18,2) NOT NULL DEFAULT 0,
    total_value DECIMAL(18,2) NOT NULL DEFAULT 0,
    condition_good TINYINT(1) NOT NULL DEFAULT 0,
    condition_damaged TINYINT(1) NOT NULL DEFAULT 0,
    period_month TINYINT UNSIGNED NOT NULL,
    period_year SMALLINT UNSIGNED NOT NULL,
    source_file VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_period (period_year, period_month),
    INDEX idx_product (product_id),
    INDEX idx_source (source_file)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$db->query($createTableSql);

echo "✅ Tabel stock_opname_archives siap digunakan.\n\n";

$dir = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'laporan bulanan' . DIRECTORY_SEPARATOR;
if (!is_dir($dir)) {
    echo "❌ Folder tidak ditemukan: {$dir}\n";
    exit(1);
}

$files = glob($dir . '*.xlsx');
if (!$files) {
    echo "⚠️  Tidak ada file .xlsx di folder laporan bulanan.\n";
    exit(0);
}

// Helper: konversi token bulan (JAN, FEB, MAR, JUNI, SEPT, DES, dll) menjadi nomor bulan
$monthMap = [
    1  => ['JAN', 'JANUARI'],
    2  => ['FEB', 'FEBRUARI'],
    3  => ['MAR', 'MARET'],
    4  => ['APR', 'APRIL'],
    5  => ['MEI'],
    6  => ['JUN', 'JUNI'],
    7  => ['JUL', 'JULI'],
    8  => ['AGT', 'AGUST', 'AGUSTUS'],
    9  => ['SEP', 'SEPT', 'SEPTEMBER'],
    10 => ['OKT', 'OKTOBER'],
    11 => ['NOV', 'NOVEMBER'],
    12 => ['DES', 'DESEMBER'],
];

$findMonthNumber = function (string $token) use ($monthMap): ?int {
    $token = strtoupper(trim($token));
    foreach ($monthMap as $num => $tokens) {
        if (in_array($token, $tokens, true)) {
            return $num;
        }
    }
    return null;
};

$totalInserted = 0;

foreach ($files as $file) {
    $basename = basename($file);
    echo "➡️  Proses file: {$basename}\n";

    // Contoh pola: "MAR 2025 - STOCK OPNAME PERSEDIAAN FASILKOM 2025.xlsx"
    $periodMonth = null;
    $periodYear  = null;

    if (preg_match('/^([A-Za-z]+)\\s+(\\d{4})/u', $basename, $m)) {
        $periodMonth = $findMonthNumber($m[1]);
        $periodYear  = (int) $m[2];
    }

    if (!$periodMonth || !$periodYear) {
        echo "   ⚠️  Gagal membaca periode dari nama file, dilewati.\n";
        continue;
    }

    // Hapus data lama untuk file + periode ini agar idempotent
    $db->table('stock_opname_archives')
        ->where('source_file', $basename)
        ->where('period_month', $periodMonth)
        ->where('period_year', $periodYear)
        ->delete();

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet       = $spreadsheet->getActiveSheet();
    } catch (Throwable $e) {
        echo "   ❌ Error baca Excel: " . $e->getMessage() . "\n";
        continue;
    }

    // Data di template export berada mulai baris 12: A=No, B=Jenis Barang, C=Jumlah, D=Harga Satuan, E=Total, F=Baik, G=Rusak
    $row = 12;
    $insertedForFile = 0;

    while (true) {
        $productName = trim((string) $sheet->getCell('B' . $row)->getValue());
        if ($productName === '') {
            break; // asumsi baris kosong = akhir data
        }

        $quantity   = (int) $sheet->getCell('C' . $row)->getCalculatedValue();
        $unitPrice  = (float) $sheet->getCell('D' . $row)->getCalculatedValue();
        $totalValue = (float) $sheet->getCell('E' . $row)->getCalculatedValue();
        $condGood   = trim((string) $sheet->getCell('F' . $row)->getValue()) !== '' ? 1 : 0;
        $condBad    = trim((string) $sheet->getCell('G' . $row)->getValue()) !== '' ? 1 : 0;

        // Coba mapping ke products.id berdasarkan nama
        $productRow = $db->table('products')
            ->select('id')
            ->where('name', $productName)
            ->get(1)
            ->getRowArray();

        $productId = $productRow['id'] ?? null;

        $db->table('stock_opname_archives')->insert([
            'product_id'       => $productId,
            'product_name'     => $productName,
            'quantity'         => $quantity,
            'unit_price'       => $unitPrice,
            'total_value'      => $totalValue,
            'condition_good'   => $condGood,
            'condition_damaged'=> $condBad,
            'period_month'     => $periodMonth,
            'period_year'      => $periodYear,
            'source_file'      => $basename,
        ]);

        $insertedForFile++;
        $totalInserted++;
        $row++;
    }

    echo "   ✅ {$insertedForFile} baris di‑import untuk {$basename} (periode {$periodMonth}/{$periodYear}).\n";
}

echo "\n🎉 Selesai. Total baris yang masuk: {$totalInserted}.\n\n";
