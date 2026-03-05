<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-warning border-3 border-bottom">
                <h5 class="mb-0 fw-bold"><i class="bi bi-tools text-warning me-2"></i>Penyesuaian Stok</h5>
                <p class="text-muted small mb-0">Sesuaikan stok sistem dengan jumlah fisik di bku/gudang</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('/stock/adjustment/store') ?>" method="POST" id="adjustmentForm">
                    <?= csrf_field() ?>

                    <div class="row mb-4">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Nomor Referensi</label>
                            <input type="text" class="form-control" name="reference_no" placeholder="Contoh: ADJ-2024-001">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Alasan Penyesuaian</label>
                            <input type="text" class="form-control" name="global_notes" placeholder="Contoh: Hasil Stock Opname, Koreksi kesalahan input, dll">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Pilih Barang yang Akan Disesuaikan</label>
                        <div class="input-group">
                            <select id="productPicker" class="form-select select2">
                                <option value="">- Cari Nama Barang -</option>
                                <?php foreach ($daftarProduk as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-name="<?= esc($p['name']) ?>" data-sku="<?= $p['sku'] ?>" data-stock="<?= $p['current_stock'] ?>" data-unit="<?= $p['unit'] ?>">
                                        <?= esc($p['name']) ?> (Stok: <?= $p['current_stock'] ?> <?= $p['unit'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-primary px-4" id="btn-add-item">
                                <i class="bi bi-plus-lg me-1"></i> Tambah
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle d-none" id="adjustmentTable">
                            <thead class="bg-light small">
                                <tr>
                                    <th>Produk</th>
                                    <th width="120" class="text-center">Stok Sistem</th>
                                    <th width="150" class="text-center">Stok Fisik</th>
                                    <th width="120" class="text-center">Selisih</th>
                                    <th width="80" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="rows">
                                <!-- Rows dynamic -->
                            </tbody>
                        </table>
                    </div>

                    <div id="empty-state" class="text-center py-5 border rounded bg-light">
                        <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Belum ada barang dipilih untuk disesuaikan.</p>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top d-none action-buttons">
                        <button type="submit" class="btn btn-warning px-5 fw-bold" id="btn-submit">
                            TERAPKAN PENYESUAIAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 mb-4 bg-light-warning">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Penting!</h6>
                <ul class="small ps-3 mb-0">
                    <li class="mb-2">Gunakan fitur ini hanya jika ada ketidaksesuain stok sistem dengan stok fisik.</li>
                    <li class="mb-2">Sistem akan membuat mutasi tipe <b>ADJUSTMENT</b> secara otomatis.</li>
                    <li>Pastikan angka stok fisik adalah jumlah barang yang ada saat ini.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let rowIndex = 0;

    $('#btn-add-item').on('click', function() {
        const picker = $('#productPicker');
        const opt = picker.find('option:selected');
        const pid = picker.val();
        
        if(!pid) return;

        if($(`.row-item[data-id="${pid}"]`).length > 0) {
            alert('Barang sudah ada di daftar.');
            return;
        }

        const name = opt.data('name');
        const sku = opt.data('sku');
        const stock = opt.data('stock');
        const unit = opt.data('unit');

        const row = `
            <tr class="row-item" data-id="${pid}">
                <td>
                    <input type="hidden" name="adjustments[${rowIndex}][product_id]" value="${pid}">
                    <div class="fw-bold text-primary">${name}</div>
                    <code class="small text-muted">${sku}</code>
                </td>
                <td class="text-center fw-bold text-muted">${stock} ${unit}</td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" name="adjustments[${rowIndex}][new_stock]" class="form-control text-center physical-qty" value="${stock}" min="0">
                        <span class="input-group-text">${unit}</span>
                    </div>
                    <input type="hidden" name="adjustments[${rowIndex}][notes]" class="row-notes">
                </td>
                <td class="text-center">
                    <span class="diff-badge badge bg-secondary">0</span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#rows').append(row);
        rowIndex++;
        picker.val('').trigger('change');
        toggleView();
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        toggleView();
    });

    $(document).on('input', '.physical-qty', function() {
        const row = $(this).closest('tr');
        const physical = parseInt($(this).val()) || 0;
        const system = parseInt($('#productPicker').find(`option[value="${row.data('id')}"]`).data('stock')) || 0;
        const diff = physical - system;
        
        const badge = row.find('.diff-badge');
        badge.text(diff > 0 ? '+' + diff : diff);
        
        badge.removeClass('bg-secondary bg-success bg-danger');
        if(diff == 0) badge.addClass('bg-secondary');
        else if(diff > 0) badge.addClass('bg-success');
        else badge.addClass('bg-danger');
    });

    function toggleView() {
        const count = $('#rows tr').length;
        if(count > 0) {
            $('#adjustmentTable, .action-buttons').removeClass('d-none');
            $('#empty-state').addClass('d-none');
        } else {
            $('#adjustmentTable, .action-buttons').addClass('d-none');
            $('#empty-state').removeClass('d-none');
        }
    }

    $('#adjustmentForm').on('submit', function() {
        if(!confirm('Apakah Anda yakin ingin menerapkan penyesuaian stok ini? Tindakan ini tidak dapat dibatalkan.')) return false;
        $('#btn-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.05); border-left: 4px solid #ffc107; }
</style>
<?= $this->endSection() ?>
