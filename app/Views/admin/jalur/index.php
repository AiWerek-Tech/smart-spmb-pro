<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-jalur-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Data SPMB</p>
            <h1 id="admin-jalur-title">Jalur Pendaftaran</h1>
            <p class="admin-page-subtitle">Kelola kuota, status aktif, dan deskripsi jalur pendaftaran SPMB.</p>
        </div>
        <div class="admin-page-actions">
            <span class="sp-status-pill"><i data-lucide="calendar-range"></i> <?= esc($activeYear ?? '-') ?></span>
        </div>
    </header>

    <?php if (empty($jalur)): ?>
        <section class="admin-secondary-panel">
            <?= view('components/empty_state', ['icon' => 'route', 'title' => 'Belum ada jalur pendaftaran', 'text' => 'Tidak ada jalur pendaftaran yang terdaftar di database.']) ?>
        </section>
    <?php else: ?>
        <div class="admin-data-grid" aria-label="Daftar jalur pendaftaran">
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
                <article class="admin-secondary-panel admin-route-card <?= $j['is_active'] ? 'is-active' : '' ?>">
                    <div class="admin-route-card__header">
                        <h2 class="admin-route-card__title"><?= esc($j['name']) ?></h2>
                        <form action="<?= base_url('admin/jalur/'.$j['id'].'/toggle') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="form-check form-switch p-0 m-0">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" onchange="this.form.submit()" <?= $j['is_active'] ? 'checked' : '' ?> title="Aktifkan/Nonaktifkan Jalur" style="width: 2.2em; height: 1.1em; cursor: pointer;">
                            </div>
                        </form>
                    </div>

                    <div class="admin-quota-meter">
                        <div class="admin-quota-meter__top">
                            <span>Pendaftar Terisi</span>
                            <strong><?= $count ?> / <?= $quota ?> Peserta</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <p class="admin-route-card__description"><?= esc($j['description']) ?: 'Belum ada deskripsi jalur.' ?></p>

                    <div class="admin-route-card__footer">
                        <span class="badge <?= $j['is_active'] ? 'bg-label-success' : 'bg-label-secondary' ?> rounded-pill px-2">
                            <?php if ($j['is_active']): ?>
                                <i class="me-1" data-lucide="check"></i>
                            <?php else: ?>
                                <i class="me-1" data-lucide="x"></i>
                            <?php endif; ?>
                            <?= $j['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>

                        <button type="button" class="btn btn-outline-primary edit-jalur-btn"
                                data-id="<?= $j['id'] ?>"
                                data-name="<?= esc($j['name']) ?>"
                                data-quota="<?= $quota ?>"
                                data-desc="<?= esc($j['description']) ?>"
                                title="Edit Jalur">
                            <i class="me-1" data-lucide="edit"></i> Edit
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- ================= EDIT JALUR MODAL ================= -->
<div class="modal fade" id="editJalurModal" tabindex="-1" aria-labelledby="editJalurModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content academic-year-modal">
            <div class="modal-header">
                <div>
                    <p class="admin-panel__kicker">Data SPMB</p>
                    <h2 class="admin-section-title" id="editJalurModalLabel"><i class="me-1 text-primary" data-lucide="edit"></i> Edit Jalur Pendaftaran</h2>
                </div>
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
