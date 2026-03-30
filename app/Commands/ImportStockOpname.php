<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Config\Database;

class ImportStockOpname extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'import:stock-opname';
    protected $description = 'Import semua file Stock Opname Excel dari public/laporan bulanan ke database.';
    protected $usage       = 'import:stock-opname';
    protected $arguments   = [];
    protected $options     = [];

    public function run(array $params)
    {
        CLI::write("\n📥 Import Stock Opname dari Excel ke database");
        CLI::write("============================================\n");

        $db = Database::connect();
        $dir = FCPATH . 'laporan bulanan' . DIRECTORY_SEPARATOR;

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
        CLI::write("✅ Tabel stock_opname_archives siap digunakan.\n");

        // Validasi folder
        if (!is_dir($dir)) {
            CLI::error("❌ Folder tidak ditemukan: {$dir}");
            return;
        }

        $files = glob($dir . '*.xlsx');
        if (!$files) {
            CLI::write("⚠️  Tidak ada file .xlsx di folder laporan bulanan.\n");
            return;
        }

        // Helper: konversi token bulan
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
            CLI::write("➡️  Proses file: {$basename}");

            // Parsing nama file: "MAR 2025 - STOCK OPNAME PERSEDIAAN FASILKOM 2025.xlsx"
            $periodMonth = null;
            $periodYear  = null;

            if (preg_match('/^([A-Za-z]+)\\s+(\\d{4})/u', $basename, $m)) {
                $periodMonth = $findMonthNumber($m[1]);
                $periodYear  = (int) $m[2];
            }

            if (!$periodMonth || !$periodYear) {
                CLI::write("   ⚠️  Gagal membaca periode dari nama file, dilewati.");
                continue;
            }

            // Hapus data lama untuk idempotent
            $db->table('stock_opname_archives')
                ->where('source_file', $basename)
                ->where('period_month', $periodMonth)
                ->where('period_year', $periodYear)
                ->delete();

            try {
                $spreadsheet = IOFactory::load($file);
                $sheet       = $spreadsheet->getActiveSheet();
            } catch (\Throwable $e) {
                CLI::write("   ❌ Error baca Excel: " . $e->getMessage());
                continue;
            }

            // Data dimulai baris 12: A=No, B=Jenis Barang, C=Jumlah, D=Harga Satuan, E=Total, F=Baik, G=Rusak
            $row = 12;
            $insertedForFile = 0;

            while (true) {
                $productName = trim((string) $sheet->getCell('B' . $row)->getValue());
                if ($productName === '') {
                    break; // baris kosong = akhir data
                }

                try {
                    $quantity   = (int) $sheet->getCell('C' . $row)->getCalculatedValue();
                    $unitPrice  = (float) $sheet->getCell('D' . $row)->getCalculatedValue();
                    $totalValue = (float) $sheet->getCell('E' . $row)->getCalculatedValue();
                    $condGood   = trim((string) $sheet->getCell('F' . $row)->getValue()) !== '' ? 1 : 0;
                    $condBad    = trim((string) $sheet->getCell('G' . $row)->getValue()) !== '' ? 1 : 0;

                    // Coba mapping ke products.id
                    $productRow = $db->table('products')
                        ->select('id')
                        ->where('name', $productName)
                        ->get(1)
                        ->getRowArray();

                    $productId = $productRow['id'] ?? null;

                    $db->table('stock_opname_archives')->insert([
                        'product_id'        => $productId,
                        'product_name'      => $productName,
                        'quantity'          => $quantity,
                        'unit_price'        => $unitPrice,
                        'total_value'       => $totalValue,
                        'condition_good'    => $condGood,
                        'condition_damaged' => $condBad,
                        'period_month'      => $periodMonth,
                        'period_year'       => $periodYear,
                        'source_file'       => $basename,
                    ]);

                    $insertedForFile++;
                    $totalInserted++;
                } catch (\Throwable $e) {
                    CLI::write("   ⚠️  Baris {$row} error: " . $e->getMessage());
                }

                $row++;
            }

            CLI::write("   ✅ {$insertedForFile} baris di-import untuk {$basename} (periode {$periodMonth}/{$periodYear}).");
        }

        CLI::write("\n🎉 Selesai. Total baris yang masuk: {$totalInserted}.\n");
    }
}
