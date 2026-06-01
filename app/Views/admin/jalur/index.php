<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in">
    <!-- Header Page -->
    <div class="col-12 mb-4">
        <div>
            <h4 class="mb-0 text-primary">Jalur Pendaftaran</h4>
            <p class="text-muted mb-0">Kelola kuota, status aktif, dan deskripsi jalur pendaftaran SPMB.</p>
        </div>
    </div>

    <!-- Jalur Cards List -->
    <?php if (empty($jalur)): ?>
        <div class="col-12 text-center py-5 text-muted bg-white rounded border shadow-xs">
            <i class="fs-1 mb-3" data-lucide="route"></i>
            <p class="mb-0">Tidak ada jalur pendaftaran yang terdaftar di database.</p>
        </div>
    <?php else: ?>
        <?php foreach ($jalur as $j): ?>
            <?php 
                $quota = (int) $j['quota'];
                $count = (int) $j['registrant_count'];
                $percent = $quota > 0 ? min(($count / $quota) * 100, 100) : 0;
                $barClass = 'bg-primary';
                if ($percent >= 90) {
                    $barClass = 'bg-danger';
                } elseif ($percent >= 70) {
                    $barClass = 'bg-warning';
                }
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border border-top-3 <?= $j['is_active'] ? 'border-primary' : 'border-secondary' ?>">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title text-dark mb-0"><?= esc($j['name']) ?></h5>
                        
                        <!-- Toggle Form -->
                        <form action="<?= base_url('admin/jalur/'.$j['id'].'/toggle') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="form-check form-switch p-0 m-0">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" onchange="this.form.submit()" <?= $j['is_active'] ? 'checked' : '' ?> title="Aktifkan/Nonaktifkan Jalur" style="width: 2.2em; height: 1.1em; cursor: pointer;">
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-body">
                        <!-- Quota stats -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Pendaftar Terisi</span>
                            <span class="fw-bold text-dark" style="font-size: 0.9rem;"><?= $count ?> / <?= $quota ?> Peserta</span>
                        </div>
                        
                        <!-- Progress bar -->
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Description -->
                        <p class="text-muted small mb-0" style="min-height: 50px;"><?= esc($j['description']) ?: 'Belum ada deskripsi jalur.' ?></p>
                    </div>

                    <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center py-2">
                        <span class="badge <?= $j['is_active'] ? 'bg-label-success' : 'bg-label-secondary' ?> rounded-pill px-2">
                            <?php if ($j['is_active']): ?>
                                <i class="me-1" data-lucide="check"></i>
                            <?php else: ?>
                                <i class="me-1" data-lucide="x"></i>
                            <?php endif; ?>
                            <?= $j['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>

                        <!-- Edit Button (loads in Modal) -->
                        <button type="button" class="btn btn-sm btn-outline-primary edit-jalur-btn" 
                                data-id="<?= $j['id'] ?>"
                                data-name="<?= esc($j['name']) ?>"
                                data-quota="<?= $quota ?>"
                                data-desc="<?= esc($j['description']) ?>"
                                title="Edit Jalur">
                            <i class="me-1" data-lucide="edit"></i> Edit
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ================= EDIT JALUR MODAL ================= -->
<div class="modal fade" id="editJalurModal" tabindex="-1" aria-labelledby="editJalurModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editJalurModalLabel"><i class="me-1 text-primary" data-lucide="edit"></i> Edit Jalur Pendaftaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editJalurForm" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <!-- Name (Read-only as unique key identity) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Jalur</label>
                        <input type="text" class="form-control bg-light" id="edit_name" readonly>
                    </div>

                    <!-- Quota -->
                    <div class="mb-3">
                        <label for="edit_quota" class="form-label fw-bold small">Kuota Penerimaan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_quota" name="quota" min="1" required>
                        <small class="text-muted">Kuota tidak boleh kurang dari pendaftar yang saat ini terdaftar.</small>
                    </div>

                    <!-- Description -->
                    <div class="mb-0">
                        <label for="edit_description" class="form-label fw-bold small">Deskripsi Jalur <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_description" name="description" rows="4" required placeholder="Tulis deskripsi persyaratan jalur..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="me-1" data-lucide="save"></i> Perbarui Jalur
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
        // Edit button click loads modal data
        $('.edit-jalur-btn').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const quota = $(this).data('quota');
            const desc = $(this).data('desc');

            // Populate forms
            $('#edit_name').val(name);
            $('#edit_quota').val(quota);
            $('#edit_description').val(desc);

            // Set Form action endpoint
            $('#editJalurForm').attr('action', `<?= base_url('admin/jalur') ?>/${id}/update`);

            // Show Modal
            const editModal = new bootstrap.Modal(document.getElementById('editJalurModal'));
            editModal.show();
        });
    });
</script>
<?= $this->endSection() ?>
