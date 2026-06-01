<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in">
    <!-- Header Page -->
    <div class="col-12 mb-4">
        <div>
            <h4 class="mb-0 text-primary">Kelola Gelombang Pendaftaran</h4>
            <p class="text-muted mb-0">Atur jadwal gelombang, tanggal buka-tutup pendaftaran, serta tanggal pengumuman hasil seleksi.</p>
        </div>
    </div>

    <!-- LEFT SIDE: Gelombang List Table -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title text-primary"><i class="me-2" data-lucide="calendar"></i> Jadwal Gelombang</h5>
                <small class="text-muted">Maksimal hanya diperbolehkan 3 gelombang aktif.</small>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Gelombang / Jalur</th>
                                <th>Tanggal Pendaftaran</th>
                                <th>Pengumuman</th>
                                <th>Status</th>
                                <th class="text-center pe-4" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($gelombang)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fs-1 mb-3" data-lucide="calendar-times"></i>
                                        <p class="mb-0">Belum ada gelombang pendaftaran yang diatur.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($gelombang as $g): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold text-dark"><?= esc($g['name']) ?></div>
                                            <span class="badge bg-light text-primary border border-primary border-opacity-10 mt-1"><?= esc($g['jalur_name']) ?></span>
                                        </td>
                                        <td>
                                            <div class="small text-dark fw-bold">
                                                <i class="text-success me-1" data-lucide="log-in"></i> Buka: <?= date('d M Y', strtotime($g['open_date'])) ?>
                                            </div>
                                            <div class="small text-danger fw-bold mt-1">
                                                <i class="me-1" data-lucide="log-out"></i> Tutup: <?= date('d M Y', strtotime($g['close_date'])) ?>
                                            </div>
                                        </td>
                                        <td class="fw-semibold small text-info">
                                            <i class="me-1" data-lucide="bullhorn"></i> <?= date('d M Y', strtotime($g['announcement_date'])) ?>
                                        </td>
                                        <td>
                                            <?php if ($g['is_active']): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <!-- Edit Trigger button (Loads values in Modal via JS) -->
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-gelombang-btn me-1" 
                                                        data-id="<?= $g['id'] ?>"
                                                        data-jalur="<?= $g['jalur_id'] ?>"
                                                        data-name="<?= esc($g['name']) ?>"
                                                        data-open="<?= $g['open_date'] ?>"
                                                        data-close="<?= $g['close_date'] ?>"
                                                        data-announce="<?= $g['announcement_date'] ?>"
                                                        data-active="<?= $g['is_active'] ?>"
                                                        title="Edit Gelombang">
                                                    <i  data-lucide="edit"></i>
                                                </button>

                                                <!-- Delete button -->
                                                <form action="<?= base_url('admin/gelombang/'.$g['id'].'/delete') ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-confirm" title="Hapus Gelombang">
                                                        <i  data-lucide="trash-2"></i>
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
        </div>
    </div>

    <!-- RIGHT SIDE: Add Gelombang Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title text-primary"><i class="me-2" data-lucide="plus"></i> Tambah Gelombang</h5>
                <small class="text-muted">Buat jadwal gelombang baru.</small>
            </div>
            
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/gelombang/store') ?>">
                    <?= csrf_field() ?>

                    <!-- Jalur -->
                    <div class="mb-3">
                        <label for="jalur_id" class="form-label fw-bold small">Jalur Pendaftaran <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="jalur_id" id="jalur_id" required>
                            <option value="" disabled selected>Pilih jalur...</option>
                            <?php foreach ($jalur as $jl): ?>
                                <option value="<?= $jl['id'] ?>" <?= old('jalur_id') == $jl['id'] ? 'selected' : '' ?>><?= esc($jl['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small">Nama Gelombang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Gelombang 1 Mandiri" required value="<?= old('name') ?>">
                    </div>

                    <!-- Open Date -->
                    <div class="mb-3">
                        <label for="open_date" class="form-label fw-bold small">Tanggal Buka <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i  data-lucide="calendar-day"></i></span>
                            <input type="text" class="form-control flatpickr" id="open_date" name="open_date" placeholder="Pilih tanggal..." required value="<?= old('open_date') ?>">
                        </div>
                    </div>

                    <!-- Close Date -->
                    <div class="mb-3">
                        <label for="close_date" class="form-label fw-bold small">Tanggal Tutup <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i  data-lucide="calendar-times"></i></span>
                            <input type="text" class="form-control flatpickr" id="close_date" name="close_date" placeholder="Pilih tanggal..." required value="<?= old('close_date') ?>">
                        </div>
                    </div>

                    <!-- Announcement Date -->
                    <div class="mb-3">
                        <label for="announcement_date" class="form-label fw-bold small">Tanggal Pengumuman <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i  data-lucide="bullhorn"></i></span>
                            <input type="text" class="form-control flatpickr" id="announcement_date" name="announcement_date" placeholder="Pilih tanggal..." required value="<?= old('announcement_date') ?>">
                        </div>
                    </div>

                    <!-- Active Switch -->
                    <div class="mb-4">
                        <div class="form-check form-switch p-0 ps-5 d-flex align-items-center">
                            <input class="form-check-input ms-n5 me-2" type="checkbox" role="switch" id="is_active" name="is_active" value="1" style="width: 2.5em; height: 1.25em;">
                            <label class="form-check-label fw-bold small" for="is_active">Aktifkan gelombang ini</label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="me-2" data-lucide="save"></i> Simpan Gelombang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= EDIT GELOMBANG MODAL ================= -->
<div class="modal fade" id="editGelombangModal" tabindex="-1" aria-labelledby="editGelombangModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGelombangModalLabel"><i class="me-1 text-primary" data-lucide="edit"></i> Edit Gelombang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGelombangForm" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <!-- Jalur -->
                    <div class="mb-3">
                        <label for="edit_jalur_id" class="form-label fw-bold small">Jalur Pendaftaran <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="jalur_id" id="edit_jalur_id" required>
                            <?php foreach ($jalur as $jl): ?>
                                <option value="<?= $jl['id'] ?>"><?= esc($jl['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="edit_name" class="form-label fw-bold small">Nama Gelombang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>

                    <!-- Open Date -->
                    <div class="mb-3">
                        <label for="edit_open_date" class="form-label fw-bold small">Tanggal Buka <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i  data-lucide="calendar-day"></i></span>
                            <input type="text" class="form-control flatpickr" id="edit_open_date" name="open_date" required>
                        </div>
                    </div>

                    <!-- Close Date -->
                    <div class="mb-3">
                        <label for="edit_close_date" class="form-label fw-bold small">Tanggal Tutup <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i  data-lucide="calendar-times"></i></span>
                            <input type="text" class="form-control flatpickr" id="edit_close_date" name="close_date" required>
                        </div>
                    </div>

                    <!-- Announcement Date -->
                    <div class="mb-3">
                        <label for="edit_announcement_date" class="form-label fw-bold small">Tanggal Pengumuman <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i  data-lucide="bullhorn"></i></span>
                            <input type="text" class="form-control flatpickr" id="edit_announcement_date" name="announcement_date" required>
                        </div>
                    </div>

                    <!-- Active Switch -->
                    <div class="mb-0">
                        <div class="form-check form-switch p-0 ps-5 d-flex align-items-center">
                            <input class="form-check-input ms-n5 me-2" type="checkbox" role="switch" id="edit_is_active" name="is_active" value="1" style="width: 2.5em; height: 1.25em;">
                            <label class="form-check-label fw-bold small" for="edit_is_active">Aktifkan gelombang ini</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="me-1" data-lucide="save"></i> Perbarui Gelombang
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
        // Edit button triggers modal populating
        $('.edit-gelombang-btn').on('click', function() {
            const id = $(this).data('id');
            const jalur = $(this).data('jalur');
            const name = $(this).data('name');
            const open = $(this).data('open');
            const close = $(this).data('close');
            const announce = $(this).data('announce');
            const active = $(this).data('active');

            // Populate forms
            $('#edit_jalur_id').val(jalur).trigger('change');
            $('#edit_name').val(name);
            $('#edit_open_date').val(open);
            $('#edit_close_date').val(close);
            $('#edit_announcement_date').val(announce);
            
            // Reflatpickr reload values
            flatpickr('#edit_open_date', { locale: 'id', dateFormat: 'Y-m-d', defaultDate: open });
            flatpickr('#edit_close_date', { locale: 'id', dateFormat: 'Y-m-d', defaultDate: close });
            flatpickr('#edit_announcement_date', { locale: 'id', dateFormat: 'Y-m-d', defaultDate: announce });

            if (active == 1) {
                $('#edit_is_active').prop('checked', true);
            } else {
                $('#edit_is_active').prop('checked', false);
            }

            // Set Form action endpoint
            $('#editGelombangForm').attr('action', `<?= base_url('admin/gelombang') ?>/${id}/update`);

            // Show Modal
            const editModal = new bootstrap.Modal(document.getElementById('editGelombangModal'));
            editModal.show();
        });
    });
</script>
<?= $this->endSection() ?>
