<?php

declare(strict_types=1);

/**
 * Helper Tampilan SIMATIK
 * - Format Rupiah
 * - Badge stok (Habis / Rendah / Normal)
 * - Badge mutasi (Masuk / Keluar / Penyesuaian)
 * - Waktu relatif (baru saja, x menit lalu, dst)
 */

/* =========================
 * 1) Format Rupiah
 * ========================= */
if (!function_exists('format_rupiah')) {
    function format_rupiah(int|float|string|null $jumlah): string
    {
        $nilai = is_numeric($jumlah) ? (float) $jumlah : 0.0;
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }
}

/* Alias lama (opsional): biar view lama tetap jalan */
if (!function_exists('format_currency')) {
    function format_currency($amount): string
    {
        return format_rupiah($amount);
    }
}

/* =========================
 * 2) Badge Status Stok
 * ========================= */
if (!function_exists('badge_status_stok')) {
    /**
     * Return HTML badge Bootstrap untuk status stok.
     */
    function badge_status_stok(int|float|string $stokSaatIni, int|float|string $stokMinimum): string
    {
        $stok = (int) $stokSaatIni;
        $min  = (int) $stokMinimum;

        if ($stok <= 0) {
            return '<span class="badge bg-danger">Habis</span>';
        }

        if ($stok <= $min) {
            return '<span class="badge bg-warning text-dark">Stok Rendah</span>';
        }

        return '<span class="badge bg-success">Normal</span>';
    }
}

/* Alias lama */
if (!function_exists('format_stock_badge')) {
    function format_stock_badge($current, $minimum): string
    {
        return badge_status_stok($current, $minimum);
    }
}

/* =========================
 * 3) Badge Jenis Mutasi
 * ========================= */
if (!function_exists('badge_jenis_mutasi')) {
    /**
     * IN / OUT / ADJUSTMENT
     */
    function badge_jenis_mutasi(string|null $jenis): string
    {
        $jenis = strtoupper(trim((string) $jenis));

        $peta = [
            'IN'         => '<span class="badge bg-success"><i class="bi bi-arrow-down"></i> Masuk</span>',
            'OUT'        => '<span class="badge bg-danger"><i class="bi bi-arrow-up"></i> Keluar</span>',
            'ADJUSTMENT' => '<span class="badge bg-info text-dark"><i class="bi bi-arrow-repeat"></i> Penyesuaian</span>',
        ];

        return $peta[$jenis] ?? '<span class="badge bg-secondary">Tidak diketahui</span>';
    }
}

/* Alias lama */
if (!function_exists('format_movement_badge')) {
    function format_movement_badge($type): string
    {
        return badge_jenis_mutasi((string) $type);
    }
}

/* =========================
 * 4) Waktu Relatif
 * ========================= */
if (!function_exists('waktu_lalu')) {
    /**
     * Convert datetime ke format "x menit yang lalu".
     * Menerima string datetime (created_at) dari DB.
     */
    function waktu_lalu(string|null $datetime): string
    {
        if (!$datetime) return '-';

        $timestamp = strtotime($datetime);
        if ($timestamp === false) return '-';

        $selisih = time() - $timestamp;
        if ($selisih < 0) return 'baru saja'; // kalau jam server beda / future sedikit

        if ($selisih < 60) return 'baru saja';
        if ($selisih < 3600) return floor($selisih / 60) . ' menit yang lalu';
        if ($selisih < 86400) return floor($selisih / 3600) . ' jam yang lalu';
        if ($selisih < 2592000) return floor($selisih / 86400) . ' hari yang lalu';
        if ($selisih < 31104000) return floor($selisih / 2592000) . ' bulan yang lalu';

        return floor($selisih / 31104000) . ' tahun yang lalu';
    }
}

/* Alias lama */
if (!function_exists('time_ago')) {
    function time_ago($datetime): string
    {
        return waktu_lalu((string) $datetime);
    }
}

/* =========================
 * 5) Format Tanggal
 * ========================= */
if (!function_exists('formatDate')) {
    /**
     * Format tanggal dari database ke format Indonesia
     * Contoh: 2024-03-19 -> 19 Maret 2024
     */
    function formatDate(string|null $date): string
    {
        if (!$date) return '-';

        try {
            $timestamp = strtotime($date);
            if ($timestamp === false) return '-';

            $bulan = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            $hari = date('d', $timestamp);
            $bulanIdx = (int) date('m', $timestamp) - 1;
            $tahun = date('Y', $timestamp);

            return $hari . ' ' . $bulan[$bulanIdx] . ' ' . $tahun;
        } catch (\Exception $e) {
            return '-';
        }
    }
}

/* =========================
 * 6) Generate Kode Resi Berdasarkan Tanggal & Waktu
 * ========================= */
if (!function_exists('generateReceiptCode')) {
    /**
     * Generate kode resi berdasarkan waktu saat ini
     * Format: RES-DD-MM-YY-HH-MM-SS
     * Contoh: RES-19-03-26-14-30-45
     */
    function generateReceiptCode(): string
    {
        $dd = date('d');  // 01-31
        $mm = date('m');  // 01-12
        $yy = date('y');  // 26 (tahun 2 digit)
        $hh = date('H');  // 00-23
        $mi = date('i');  // 00-59
        $ss = date('s');  // 00-59

        return "RES-{$dd}-{$mm}-{$yy}-{$hh}-{$mi}-{$ss}";
    }
}
