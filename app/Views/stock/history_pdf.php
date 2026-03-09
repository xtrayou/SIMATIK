<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Riwayat Mutasi Stok</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #000; }
        .header p { margin: 5px 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f2f2f2; color: #333; padding: 10px 5px; text-align: left; border: 1px solid #ddd; font-weight: bold; }
        td { padding: 8px 5px; border: 1px solid #ddd; vertical-align: top; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 8pt; font-weight: bold; }
        .bg-success { background-color: #d1e7dd; color: #0f5132; }
        .bg-danger { background-color: #f8d7da; color: #842029; }
        .bg-info { background-color: #cff4fc; color: #055160; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8pt; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN RIWAYAT MUTASI STOK</h2>
        <p>Aplikasi SIMATIK - Sistem Informasi Manajemen ATK</p>
        <p style="font-size: 9pt;">Dicetak pada: <?= date('d/m/Y H:i') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">Tanggal</th>
                <th width="25%">Produk</th>
                <th width="8%" class="text-center">Tipe</th>
                <th width="10%" class="text-center">Jumlah</th>
                <th width="10%" class="text-center">Stok Sisa</th>
                <th width="27%">Referensi / Ket</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movements as $index => $mut): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td>
                    <?= date('d/m/Y', strtotime($mut['created_at'])) ?><br>
                    <span style="color: #999; font-size: 8pt;"><?= date('H:i', strtotime($mut['created_at'])) ?></span>
                </td>
                <td>
                    <strong><?= esc($mut['product_name']) ?></strong><br>
                    <small style="color: #666;"><?= $mut['product_sku'] ?></small>
                </td>
                <td class="text-center">
                    <?php if ($mut['type'] == 'IN'): ?>
                        <span class="badge bg-success">MASUK</span>
                    <?php elseif ($mut['type'] == 'OUT'): ?>
                        <span class="badge bg-danger">KELUAR</span>
                    <?php else: ?>
                        <span class="badge bg-info">ADJ</span>
                    <?php endif; ?>
                </td>
                <td class="text-center" style="font-weight: bold; color: <?= $mut['type'] == 'IN' ? '#198754' : ($mut['type'] == 'OUT' ? '#dc3545' : '#0dcaf0') ?>">
                    <?= $mut['type'] == 'IN' ? '+' : ($mut['type'] == 'OUT' ? '-' : '±') ?><?= number_format($mut['quantity']) ?>
                </td>
                <td class="text-center"><?= number_format($mut['current_stock']) ?></td>
                <td>
                    <?php if ($mut['reference_no']): ?>
                        <small>Ref: <?= $mut['reference_no'] ?></small><br>
                    <?php endif; ?>
                    <small style="font-style: italic;"><?= esc($mut['notes']) ?: '-' ?></small>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Halaman 1 dari 1 | SIMATIK Inventory System
    </div>
</body>
</html>
