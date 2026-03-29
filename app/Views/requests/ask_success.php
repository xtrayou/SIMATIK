<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Permintaan Terkirim | SIMATIK' ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/static/images/logo/favicon.svg') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #f8faff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .success-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(59, 91, 219, 0.14);
            max-width: 520px;
            width: 100%;
            background: white;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.2rem;
            color: white;
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.35);
            animation: scaleIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .receipt-code {
            background: linear-gradient(135deg, #3B5BDB 0%, #4263EB 100%);
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: center;
            box-shadow: 0 8px 24px rgba(59, 91, 219, 0.25);
        }

        .receipt-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .receipt-value {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .copy-btn {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.8rem;
        }

        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .ref-badge {
            background: #EDF2FF;
            color: #3B5BDB;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .btn-back {
            background: linear-gradient(135deg, #3B5BDB, #4263EB);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            padding: 0.6rem 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #2B4ACB, #3B5BDB);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 91, 219, 0.35);
        }

        .btn-light {
            background: #e9ecef;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            color: #495057;
            padding: 0.6rem 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-light:hover {
            background: #dee2e6;
            color: #2c3e50;
        }

        .brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #3B5BDB;
        }

        .info-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            font-size: 0.95rem;
            color: #155724;
        }

        .info-box i {
            margin-right: 0.5rem;
        }

        .chat-notification {
            background: #cfe2ff;
            border-left: 4px solid #0d6efd;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            font-size: 0.95rem;
            color: #084298;
        }

        .chat-notification i {
            margin-right: 0.5rem;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container px-3">
        <div class="card success-card mx-auto fade-in">
            <div class="card-body p-5 text-center">

                <!-- Brand -->
                <div class="brand mb-4">
                    <i class="bi bi-box-seam-fill me-1"></i> SIMATIK
                </div>

                <!-- Icon -->
                <div class="success-icon">
                    <i class="bi bi-check-lg"></i>
                </div>

                <!-- Message -->
                <h3 class="fw-bold mb-2">Permintaan Terkirim!</h3>
                <p class="text-muted mb-3">
                    <?php if (!empty($borrower_name)): ?>
                        Terima kasih, <strong><?= esc($borrower_name) ?></strong>.<br>
                    <?php endif; ?>
                    Permintaan ATK Anda telah berhasil diajukan dan akan segera diproses oleh petugas.
                </p>

                <!-- Nomor Referensi -->
                <?php if (!empty($request_id)): ?>
                    <div class="mb-3">
                        <p class="small text-muted mb-1">Nomor Referensi Permintaan</p>
                        <div class="ref-badge">REQ-<?= str_pad($request_id, 4, '0', STR_PAD_LEFT) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Kode Resi Berdasarkan Waktu -->
                <div class="receipt-code">
                    <div class="receipt-label">
                        <i class="bi bi-ticket-perforated me-1"></i> Kode Resi Anda
                    </div>
                    <div class="receipt-value" id="receiptCode">
                        <?= generateReceiptCode() ?>
                    </div>
                    <button class="copy-btn" onclick="copyReceiptCode()" title="Salin kode resi">
                        <i class="bi bi-clipboard me-1"></i> Salin Kode
                    </button>
                </div>

                <!-- Notifikasi Chat -->
                <div class="chat-notification">
                    <i class="bi bi-chat-dots"></i>
                    <strong>Admin akan segera membalas permohonan Anda via chat atau email</strong>
                    <p class="mb-0 mt-2" style="font-size: 0.9rem;">Harap menyimpan kode resi di atas untuk referensi Anda</p>
                </div>

                <!-- Info -->
                <div class="info-box">
                    <i class="bi bi-clock-history"></i>
                    Permintaan biasanya diproses dalam <strong>1–2 hari kerja</strong>. Status akan terus diperbarui.
                </div>

                <!-- Actions -->
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
                    <a href="<?= base_url('/track') ?>" class="btn btn-back">
                        <i class="bi bi-search me-1"></i> Lacak Status
                    </a>
                    <a href="<?= base_url('/') ?>" class="btn btn-light">
                        <i class="bi bi-house me-1"></i> Kembali ke Beranda
                    </a>
                </div>

                <p class="mt-4 mb-0" style="font-size:0.78rem; color:#adb5bd;">
                    &copy; <?= date('Y') ?> SIMATIK &mdash; Sistem Informasi Manajemen ATK
                </p>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
    <script>
        // Copy receipt code to clipboard
        function copyReceiptCode() {
            const receiptCode = document.getElementById('receiptCode').textContent.trim();
            navigator.clipboard.writeText(receiptCode).then(() => {
                // Tampil notifikasi sukses
                const btn = event.target.closest('.copy-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Tersalin!';
                btn.style.background = 'rgba(76, 175, 80, 0.3)';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = 'rgba(255, 255, 255, 0.2)';
                }, 2000);
            });
        }

        // Tampilkan pop-up sweet alert saat halaman dimuat
        window.addEventListener('load', function() {
            Swal.fire({
                title: '✅ Permintaan Berhasil!',
                html: `
                    <div style="text-align: left; font-size: 0.95rem;">
                        <p><strong>Permohonan Anda telah diterima dan tercatat dalam sistem.</strong></p>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #0d6efd;">
                            <p style="margin: 0 0 0.5rem 0; font-size: 0.85rem; color: #666;">Kode Resi:</p>
                            <p style="margin: 0; font-size: 1.2rem; font-weight: 700; color: #0d6efd; font-family: 'Courier New', monospace;">
                                ${document.getElementById('receiptCode').textContent.trim()}
                            </p>
                        </div>
                        <p><i class="bi bi-chat-dots me-1"></i> <strong>Admin akan membalas permintaan Anda via chat atau email dalam 1-2 hari kerja.</strong></p>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 0;">Simpan kode resi di atas untuk referensi Anda.</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Saya Mengerti',
                confirmButtonColor: '#3B5BDB',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    // Posisikan pop-up di tengah layar
                    Swal.getPopup().style.borderRadius = '16px';
                }
            });
        });
    </script>
</body>

</html>