<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-fee-types-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Keuangan SPMB</p>
            <h1 id="admin-fee-types-title">Pengaturan Jenis Biaya</h1>
            <p class="admin-page-subtitle">Kelola seluruh jenis biaya pendaftaran, nominal, dan ketentuan pembayaran awal.</p>
        </div>
        <div class="admin-page-actions">
            <button type="button" class="btn btn-primary add-fee-btn d-flex align-items-center">
                <i class="me-1" data-lucide="plus-circle"></i> Tambah Jenis Biaya
            </button>
        </div>
    </header>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="me-2" data-lucide="check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="me-2" data-lucide="alert-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="me-2" data-lucide="alert-circle"></i> <strong>Gagal menyimpan data:</strong>
            <ul class="mb-0 mt-1 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-xs border">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Kode</th>
                        <th>Nama Biaya</th>
                        <th>Nominal</th>
                        <th>Periode</th>
                        <th>Ketentuan</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if (empty($feeTypes)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i data-lucide="wallet" class="mb-2 text-muted" style="width: 48px; height: 48px;"></i>
                                <p class="mb-0 fw-semibold">Belum ada jenis biaya pendaftaran</p>
                                <small>Silakan tambahkan jenis biaya baru untuk mulai mengonfigurasi tagihan pendaftaran.</small>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($feeTypes as $fee): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= esc($fee['sort_order']) ?></span>
                                </td>
                                <td>
                                    <code class="fw-bold"><?= esc($fee['code']) ?></code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">
                                            <i data-lucide="<?= esc($fee['icon'] ?: 'wallet') ?>" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block"><?= esc($fee['name']) ?></span>
                                            <small class="text-muted text-wrap d-block" style="max-width: 250px; font-size: 0.75rem;"><?= esc($fee['description'] ?: 'Tidak ada deskripsi') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">Rp <?= number_format((float) ($fee['amount'] ?? 0), 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-label-info"><?= esc($fee['billing_period']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <?php if ((int)$fee['is_required'] === 1): ?>
                                            <span class="badge bg-label-danger" style="font-size:0.7rem; width:fit-content;"><i class="me-1" data-lucide="alert-triangle" style="width:10px;height:10px;"></i> Wajib</span>
                                        <?php else: ?>
                                            <span class="badge bg-label-secondary" style="font-size:0.7rem; width:fit-content;">Opsional</span>
                                        <?php endif; ?>

                                        <?php if ((int)$fee['requires_payment_before_form'] === 1): ?>
                                            <span class="badge bg-label-warning text-dark" style="font-size:0.7rem; width:fit-content;"><i class="me-1" data-lucide="lock" style="width:10px;height:10px;"></i> Bayar Sebelum Form</span>
                                        <?php endif; ?>

                                        <?php if ((int)$fee['auto_invoice'] === 1): ?>
                                            <span class="badge bg-label-primary" style="font-size:0.7rem; width:fit-content;"><i class="me-1" data-lucide="refresh-cw" style="width:10px;height:10px;"></i> Auto-Invoice</span>
                                        <?php endif; ?>

                                        <?php if ((int)$fee['show_on_homepage'] === 1): ?>
                                            <span class="badge bg-label-success" style="font-size:0.7rem; width:fit-content;"><i class="me-1" data-lucide="home" style="width:10px;height:10px;"></i> Homepage</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <form action="<?= base_url('admin/fee-types/'.$fee['id'].'/toggle') ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <div class="form-check form-switch p-0 m-0 d-flex align-items-center">
                                            <input class="form-check-input ms-0" type="checkbox" role="switch" onchange="this.form.submit()" <?= (int)$fee['is_active'] === 1 ? 'checked' : '' ?> title="Aktifkan/Nonaktifkan Biaya" style="width: 2.2em; height: 1.1em; cursor: pointer;">
                                            <span class="ms-2 small <?= (int)$fee['is_active'] === 1 ? 'text-success fw-semibold' : 'text-muted' ?>">
                                                <?= (int)$fee['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                                            </span>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-fee-btn"
                                                data-id="<?= $fee['id'] ?>"
                                                data-code="<?= esc($fee['code']) ?>"
                                                data-name="<?= esc($fee['name']) ?>"
                                                data-desc="<?= esc($fee['description']) ?>"
                                                data-amount="<?= (float)$fee['amount'] ?>"
                                                data-period="<?= esc($fee['billing_period']) ?>"
                                                data-required="<?= $fee['is_required'] ?>"
                                                data-active="<?= $fee['is_active'] ?>"
                                                data-homepage="<?= $fee['show_on_homepage'] ?>"
                                                data-preform="<?= $fee['requires_payment_before_form'] ?>"
                                                data-autoinvoice="<?= $fee['auto_invoice'] ?>"
                                                data-icon="<?= esc($fee['icon']) ?>"
                                                data-sort="<?= $fee['sort_order'] ?>"
                                                title="Edit Jenis Biaya">
                                            <i data-lucide="edit-3" style="width:14px;height:14px;"></i>
                                        </button>
                                        <form action="<?= base_url('admin/fee-types/'.$fee['id'].'/delete') ?>" method="POST" class="d-inline delete-fee-form">
                                            <?= csrf_field() ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger confirm-delete-btn" title="Hapus Jenis Biaya">
                                                <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ================= ADD / EDIT FEE TYPE MODAL ================= -->
<div class="modal fade" id="feeTypeModal" tabindex="-1" aria-labelledby="feeTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content academic-year-modal">
            <div class="modal-header border-bottom">
                <div>
                    <p class="admin-panel__kicker">Keuangan SPMB</p>
                    <h2 class="admin-section-title" id="feeTypeModalLabel"><i class="me-1 text-primary" data-lucide="wallet"></i> <span id="modal_action_title">Tambah</span> Jenis Biaya</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="feeTypeForm" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Code -->
                        <div class="col-md-6">
                            <label for="fee_code" class="form-label fw-bold small">Kode Unik Biaya <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fee_code" name="code" required placeholder="Contoh: spp, pendaftaran, seragam">
                            <small class="text-muted">Gunakan huruf kecil, angka, atau tanda hubung (-).</small>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="fee_name" class="form-label fw-bold small">Nama Jenis Biaya <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fee_name" name="name" required placeholder="Contoh: Biaya Pendaftaran">
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <label for="fee_amount" class="form-label fw-bold small">Nominal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="fee_amount" name="amount" min="0" step="any" required placeholder="Contoh: 150000">
                            </div>
                        </div>

                        <!-- Billing Period -->
                        <div class="col-md-6">
                            <label for="fee_period" class="form-label fw-bold small">Periode Tagihan <span class="text-danger">*</span></label>
                            <select class="form-select" id="fee_period" name="billing_period" required>
                                <option value="Satu Kali">Satu Kali</option>
                                <option value="Bulanan">Bulanan</option>
                                <option value="Tahunan">Tahunan</option>
                            </select>
                        </div>

                        <!-- Icon -->
                        <div class="col-md-6">
                            <label for="fee_icon" class="form-label fw-bold small">Ikon Lucide</label>
                            <input type="text" class="form-control" id="fee_icon" name="icon" placeholder="Contoh: wallet, credit-card, award">
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-6">
                            <label for="fee_sort" class="form-label fw-bold small">Urutan Tampil</label>
                            <input type="number" class="form-control" id="fee_sort" name="sort_order" min="0" placeholder="100">
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="fee_description" class="form-label fw-bold small">Deskripsi Singkat</label>
                            <textarea class="form-control" id="fee_description" name="description" rows="3" placeholder="Tuliskan keterangan detail mengenai biaya ini..."></textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3 text-dark"><i data-lucide="settings" class="me-1 text-secondary" style="width:18px;height:18px;"></i> Ketentuan & Ketetapan Biaya</h6>
                    
                    <div class="row g-3">
                        <!-- Checkbox Switches -->
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fee_is_required" name="is_required" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark small" for="fee_is_required">Wajib Dibayar</label>
                                <small class="d-block text-muted">Biaya harus dilunasi oleh calon siswa agar dapat diterima.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fee_is_active" name="is_active" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark small" for="fee_is_active">Status Aktif</label>
                                <small class="d-block text-muted">Aktifkan agar biaya dapat masuk ke dalam sistem.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fee_show_on_homepage" name="show_on_homepage" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark small" for="fee_show_on_homepage">Tampilkan di Halaman Utama</label>
                                <small class="d-block text-muted">Tampilkan nominal rincian biaya ini di homepage publik.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fee_requires_payment_before_form" name="requires_payment_before_form" value="1">
                                <label class="form-check-label fw-semibold text-dark small" for="fee_requires_payment_before_form">Wajib Bayar Sebelum Pengisian Form</label>
                                <small class="d-block text-muted">Mengunci form pendaftaran hingga tagihan ini dilunasi.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fee_auto_invoice" name="auto_invoice" value="1" checked>
                                <label class="form-check-label fw-semibold text-dark small" for="fee_auto_invoice">Otomatis Terbitkan Tagihan</label>
                                <small class="d-block text-muted">Terbitkan tagihan otomatis saat pendaftaran disubmit.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="me-1" data-lucide="save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Reset modal form on add click
        $('.add-fee-btn').on('click', function() {
            $('#feeTypeForm')[0].reset();
            $('#modal_action_title').text('Tambah');
            $('#fee_code').removeAttr('readonly');
            
            // Set defaults
            $('#fee_is_required').prop('checked', true);
            $('#fee_is_active').prop('checked', true);
            $('#fee_show_on_homepage').prop('checked', true);
            $('#fee_requires_payment_before_form').prop('checked', false);
            $('#fee_auto_invoice').prop('checked', true);
            $('#fee_sort').val(100);
            $('#fee_icon').val('wallet');

            // Set Form action endpoint
            $('#feeTypeForm').attr('action', '<?= base_url('admin/fee-types/store') ?>');

            // Show Modal
            const modal = new bootstrap.Modal(document.getElementById('feeTypeModal'));
            modal.show();
        });

        // Edit button click loads data into modal
        $('.edit-fee-btn').on('click', function() {
            const id = $(this).data('id');
            const code = $(this).data('code');
            const name = $(this).data('name');
            const desc = $(this).data('desc');
            const amount = $(this).data('amount');
            const period = $(this).data('period');
            const required = parseInt($(this).data('required'));
            const active = parseInt($(this).data('active'));
            const homepage = parseInt($(this).data('homepage'));
            const preform = parseInt($(this).data('preform'));
            const autoinvoice = parseInt($(this).data('autoinvoice'));
            const icon = $(this).data('icon');
            const sort = $(this).data('sort');

            // Populate forms
            $('#fee_code').val(code).attr('readonly', 'readonly');
            $('#fee_name').val(name);
            $('#fee_description').val(desc);
            $('#fee_amount').val(amount);
            $('#fee_period').val(period);
            $('#fee_icon').val(icon);
            $('#fee_sort').val(sort);

            // Populate checkboxes
            $('#fee_is_required').prop('checked', required === 1);
            $('#fee_is_active').prop('checked', active === 1);
            $('#fee_show_on_homepage').prop('checked', homepage === 1);
            $('#fee_requires_payment_before_form').prop('checked', preform === 1);
            $('#fee_auto_invoice').prop('checked', autoinvoice === 1);

            $('#modal_action_title').text('Edit');

            // Set Form action endpoint
            $('#feeTypeForm').attr('action', `<?= base_url('admin/fee-types') ?>/${id}/update`);

            // Show Modal
            const modal = new bootstrap.Modal(document.getElementById('feeTypeModal'));
            modal.show();
        });

        // Confirm Delete Button
        $('.confirm-delete-btn').on('click', function() {
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Hapus Jenis Biaya?',
                text: "Jenis biaya yang dihapus tidak dapat dipulihkan kembali. Tindakan ini hanya berhasil jika biaya belum digunakan dalam tagihan apa pun.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
