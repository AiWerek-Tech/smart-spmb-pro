<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="admin-page-shell role-page-shell">
    <!-- Back & Top Info -->
    <div class="role-page-header">
        <a href="<?= base_url('operator/registrants/'.$registration['id']) ?>" class="role-back-link">
            <i class="me-1" data-lucide="arrow-left"></i> Kembali ke Profil Pendaftar
        </a>
        <span class="badge bg-label-info p-2 rounded"><i class="me-1" data-lucide="folder"></i> Verifikasi Berkas Calon Siswa</span>
    </div>

    <!-- Candidate Header Card -->
    <div>
        <div class="role-summary-card">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="role-page-header__title"><?= esc($registration['full_name']) ?></h1>
                        <p class="text-muted mb-0">No. Pendaftaran: <strong class="text-primary"><?= esc($registration['registration_number']) ?></strong> | NIK: <?= esc($registration['nik']) ?> | Status Pendaftaran: 
                            <span class="badge bg-light text-primary border"><?= esc($registration['status']) ?></span>
                        </p>
                    </div>
                </div>
        </div>
    </div>

    <!-- Documents List Grid -->
    <div>
        <div class="card shadow-sm border">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title m-0"><i class="me-2" data-lucide="folder-open"></i> Daftar Dokumen Calon Siswa</h5>
                <small class="text-muted">Review dan tentukan status berkas (Setujui / Tolak).</small>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tipe Berkas</th>
                                <th>Info File</th>
                                <th>Status Berkas</th>
                                <th>Catatan / Alasan Penolakan</th>
                                <th class="text-center pe-4" style="width: 320px;">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documents)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fs-1 mb-2" data-lucide="folder-minus"></i>
                                        <p class="mb-0">Belum ada dokumen yang diunggah oleh pendaftar ini.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">
                                            <?php 
                                                $requiredTypes = [];
                                                foreach (($requirements ?? []) as $requirement) {
                                                    if ((int) ($requirement['is_required'] ?? 0) === 1) {
                                                        $requiredTypes[] = $requirement['document_type'];
                                                    }
                                                }
                                                $isMandatory = in_array($doc['document_type'], $requiredTypes, true);
                                                echo ($requirementLabels ?? [])[$doc['document_type']] ?? esc($doc['document_type']);
                                            ?>
                                            <?php if ($isMandatory): ?>
                                                <span class="text-danger small ms-1" title="Berkas Wajib">* Wajib</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="small text-dark font-monospace"><?= esc($doc['file_name']) ?></div>
                                            <div class="small text-muted"><?= number_format($doc['file_size'] / 1024, 1) ?> KB | <?= esc($doc['mime_type']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($doc['status'] === 'approved'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="check-circle-2"></i> Disetujui
                                                </span>
                                            <?php elseif ($doc['status'] === 'rejected'): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="x-circle"></i> Ditolak
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="spin"></i> Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($doc['status'] === 'rejected' && !empty($doc['rejection_reason'])): ?>
                                                <span class="text-danger small fw-semibold"><i class="me-1" data-lucide="message-square-off"></i> <?= esc($doc['rejection_reason']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="role-table-actions">
                                                <!-- Open File Link -->
                                                <a href="<?= base_url('operator/documents/'.$doc['id'].'/view') ?>" class="btn btn-sm btn-outline-primary me-1 px-2" target="_blank" title="Buka Pratinjau Dokumen">
                                                    <i  data-lucide="external-link"></i> Buka File
                                                </a>

                                                <!-- Approve action form -->
                                                <form action="<?= base_url('operator/documents/'.$registration['id'].'/verify') ?>" method="POST" class="me-1">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-sm btn-success px-2" <?= $doc['status'] === 'approved' ? 'disabled' : '' ?> title="Setujui Berkas">
                                                        <i  data-lucide="check"></i> Setuju
                                                    </button>
                                                </form>

                                                <!-- Reject trigger button (Toggles inline comment form) -->
                                                <button type="button" class="btn btn-sm btn-danger px-2 reject-trigger-btn" data-doc-id="<?= $doc['id'] ?>" <?= $doc['status'] === 'rejected' ? 'disabled' : '' ?> title="Tolak Berkas">
                                                    <i  data-lucide="x"></i> Tolak
                                                </button>
                                            </div>

                                            <!-- COLLAPSIBLE REJECTION COMMENT FORM -->
                                            <div class="mt-2 text-start d-none reject-form-container" id="reject-form-<?= $doc['id'] ?>">
                                                <form action="<?= base_url('operator/documents/'.$registration['id'].'/verify') ?>" method="POST" class="p-2 bg-light border rounded">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="document_id" value="<?= $doc['id'] ?>">
                                                    <input type="hidden" name="status" value="rejected">
                                                    
                                                    <label class="form-label small fw-bold text-danger">Alasan Penolakan Berkas <span class="text-danger">*</span></label>
                                                    <textarea class="form-control form-control-sm mb-2" name="rejection_reason" rows="2" placeholder="Contoh: Dokumen blur, tidak terbaca..." required></textarea>
                                                    
                                                    <div class="d-flex justify-content-end">
                                                        <button type="button" class="btn btn-xs btn-outline-secondary me-1 py-0 px-2 cancel-reject-btn">Batal</button>
                                                        <button type="submit" class="btn btn-xs btn-danger py-0 px-2">Kirim Tolak</button>
                                                    </div>
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
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Toggle Rejection Form Collapsible
        $('.reject-trigger-btn').on('click', function() {
            const docId = $(this).data('doc-id');
            $('.reject-form-container').addClass('d-none'); // Close others
            $(`#reject-form-${docId}`).removeClass('d-none');
        });

        // Cancel Rejection Form
        $('.cancel-reject-btn').on('click', function() {
            $(this).closest('.reject-form-container').addClass('d-none');
        });
    });
</script>
<?= $this->endSection() ?>
