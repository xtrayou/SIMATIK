# KEBUTUHAN SISTEM SIMATIK

## (Sistem Informasi Manajemen Alat Tulis Kantor)

## 4.1.1 Kebutuhan Fungsional

Berdasarkan hasil analisis sistem yang telah dikembangkan, berikut adalah kebutuhan fungsional SIMATIK:

### Tabel 4.1 Kebutuhan Fungsional

| Aktor                | Fungsi                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| -------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Superadmin**       | • Login dan Logout ke sistem<br>• Pengelolaan pengguna dan role sistem<br>• Pengelolaan pengaturan sistem tingkat lanjut<br>• Monitoring dan audit trail sistem<br>• Akses penuh ke semua fitur Admin<br>• Backup dan restore database<br>• Konfigurasi keamanan sistem                                                                                                                                                                                                                                                                                                                                                                                                                           |
| **Admin/Staff**      | • Login dan Logout ke sistem<br>• Pengelolaan data kategori produk<br>• Pengelolaan data produk/barang<br>• Pengelolaan stok masuk<br>• Pengelolaan stok keluar<br>• Pengelolaan mutasi stok<br>• Pengelolaan penyesuaian stok (adjustment)<br>• Pengelolaan permintaan barang<br>• Persetujuan permintaan barang<br>• Distribusi barang yang diminta<br>• Pengelolaan data pengguna<br>• Melihat dashboard dan statistik<br>• Melihat notifikasi sistem<br>• Melihat laporan stok<br>• Melihat laporan mutasi stok<br>• Melihat laporan valuasi<br>• Melihat analytics stok<br>• Pencetakan laporan dalam format Excel<br>• Pencetakan laporan dalam format PDF                                  |
| **Pemohon/Publik**   | • Mengajukan permintaan barang melalui form publik<br>• Melihat status permintaan<br>• Melihat konfirmasi pengajuan<br>• Tracking permintaan barang                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |

---

## 4.1.2 Kebutuhan Non-Fungsional

Berdasarkan hasil analisis sistem yang telah dikembangkan, maka didapat kebutuhan non-fungsional sebagai berikut:

### 1. Kebutuhan Perangkat Lunak

#### Tabel 4.2 Kebutuhan Perangkat Lunak

| No  | Perangkat Lunak  | Spesifikasi                                                                                |
| --- | ---------------- | ------------------------------------------------------------------------------------------ |
| 1   | Sistem Operasi   | Windows 10/11 64-bit atau Linux Ubuntu 20.04+                                              |
| 2   | Web Server       | Apache 2.4+ atau Nginx 1.18+                                                               |
| 3   | PHP              | PHP 8.1 atau lebih tinggi                                                                  |
| 4   | Database         | MySQL 8.0+ atau MariaDB 10.6+                                                              |
| 5   | Framework        | CodeIgniter 4.x                                                                            |
| 6   | Web Browser      | Google Chrome 90+, Mozilla Firefox 88+, Microsoft Edge 90+                                 |
| 7   | Composer         | Composer 2.0+ (untuk dependency management)                                                |
| 8   | Library Tambahan | - PHPSpreadsheet (export Excel)<br>- Dompdf (export PDF)<br>- Bootstrap 5.x (UI Framework) |

### 2. Kebutuhan Perangkat Keras

#### Tabel 4.3 Kebutuhan Perangkat Keras

| No  | Perangkat Keras  | Spesifikasi Minimum       | Spesifikasi Rekomendasi         |
| --- | ---------------- | ------------------------- | ------------------------------- |
| 1   | Processor        | Intel Core i3 atau setara | Intel Core i5 atau lebih tinggi |
| 2   | RAM              | 4 GB                      | 8 GB atau lebih                 |
| 3   | Hard Disk        | 20 GB ruang kosong        | 50 GB ruang kosong              |
| 4   | Koneksi Internet | 1 Mbps                    | 5 Mbps atau lebih               |
| 5   | Monitor          | Resolusi 1366x768         | Resolusi 1920x1080              |

### 3. Pengguna Sistem

#### Tabel 4.4 Pengguna

| No  | Pengguna              | Deskripsi                                                                                                                                                                                                                                                                                                                                       |
| --- | --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | **Superadmin**        | Pengguna dengan hak akses tertinggi untuk mengelola seluruh sistem, termasuk:<br>• Mengelola semua pengguna dan role<br>• Konfigurasi sistem tingkat lanjut<br>• Akses ke audit trail dan log sistem<br>• Backup dan restore database<br>• Mengatur keamanan dan pengaturan kritis sistem                                                       |
| 2   | **Admin/Staff**       | Pengguna dengan hak akses untuk mengelola operasional inventori, termasuk:<br>• Mengelola data master (kategori, produk)<br>• Mengelola stok masuk, keluar, dan mutasi barang<br>• Menyetujui/menolak permintaan barang<br>• Distribusi barang<br>• Mengakses laporan dan analytics<br>• Menerima notifikasi stok menipis                       |
| 3   | **Pemohon/Publik**    | Pengguna eksternal atau internal yang dapat:<br>• Mengajukan permintaan barang<br>• Melihat status pengajuan<br>• Tracking permintaan barang                                                                                                                                                                                                    |

### 4. Kebutuhan Keamanan

#### Tabel 4.5 Kebutuhan Keamanan

| No  | Aspek Keamanan     | Deskripsi                                                                                                |
| --- | ------------------ | -------------------------------------------------------------------------------------------------------- |
| 1   | Autentikasi        | Sistem menggunakan username dan password dengan enkripsi bcrypt                                          |
| 2   | Otorisasi          | Pembatasan akses berdasarkan role (Superadmin, Admin, Pemohon) menggunakan filter dan middleware        |
| 3   | Session Management | Session timeout otomatis setelah 30 menit tidak aktif                                                    |
| 4   | Validasi Input     | Validasi semua input form untuk mencegah SQL Injection dan XSS                                           |
| 5   | CSRF Protection    | Implementasi CSRF token pada semua form                                                                  |

### 5. Kebutuhan Performa

#### Tabel 4.6 Kebutuhan Performa

| No  | Aspek Performa   | Target                                                                 |
| --- | ---------------- | ---------------------------------------------------------------------- |
| 1   | Response Time    | Halaman dimuat dalam waktu < 3 detik                                   |
| 2   | Concurrent Users | Sistem dapat menangani minimal 50 pengguna bersamaan                   |
| 3   | Database Size    | Sistem dapat mengelola data hingga 10,000 produk dan 100,000 transaksi |
| 4   | Export Time      | Waktu export laporan < 10 detik untuk 1,000 records                    |

### 6. Kebutuhan Usability

#### Tabel 4.7 Kebutuhan Usability

| No  | Aspek          | Deskripsi                                                               |
| --- | -------------- | ----------------------------------------------------------------------- |
| 1   | User Interface | Antarmuka responsif yang dapat diakses dari desktop, tablet, dan mobile |
| 2   | Navigasi       | Menu navigasi yang intuitif dan konsisten                               |
| 3   | Notifikasi     | Sistem feedback yang jelas untuk setiap aksi pengguna                   |
| 4   | Bahasa         | Menggunakan Bahasa Indonesia                                            |
| 5   | Help/Panduan   | Tooltip dan validasi message yang informatif                            |

### 7. Kebutuhan Maintenance

#### Tabel 4.8 Kebutuhan Maintenance

| No  | Aspek          | Deskripsi                                                                  |
| --- | -------------- | -------------------------------------------------------------------------- |
| 1   | Backup Data    | Backup database dilakukan secara berkala (minimal mingguan)                |
| 2   | Logging        | Sistem mencatat aktivitas penting dalam log file                           |
| 3   | Error Handling | Error ditangani dengan baik dan ditampilkan dalam pesan yang user-friendly |
| 4   | Update         | Sistem dapat di-update tanpa mengganggu data yang sudah ada                |

---

## Catatan Tambahan

Sistem SIMATIK dirancang untuk memudahkan pengelolaan inventori alat tulis kantor di lingkungan Fakultas Ilmu Komputer dengan fitur-fitur lengkap mulai dari manajemen stok, permintaan barang, hingga pelaporan yang komprehensif dengan visualisasi data dalam bentuk grafik dan dapat diekspor dalam berbagai format.
