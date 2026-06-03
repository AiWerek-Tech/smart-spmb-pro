<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="admin-page-shell role-page-shell">
    <!-- Header Page -->
    <div class="role-page-header">
        <div>
            <h1 class="role-page-header__title">Berkas & Dokumen Pendukung</h1>
            <p class="role-page-header__subtitle">Unggah berkas sesuai syarat dokumen tahun pelajaran <?= esc($academicYear ?? '') ?>.</p>
        </div>
    </div>

    <!-- LEFT COLUMN: File Upload Form -->
    <div class="row g-3">
    <div class="col-lg-5">
        <div class="card shadow-sm border">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title m-0"><i class="me-2" data-lucide="upload"></i> Unggah Berkas Baru</h5>
                <small class="text-muted">Pilih jenis berkas dan file gambar/PDF yang akan diunggah.</small>
            </div>
            
            <div class="card-body">
                <form method="POST" action="<?= base_url('pendaftar/dokumen/upload') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Document Type Dropdown -->
                    <div class="mb-3">
                        <label for="document_type" class="form-label fw-bold small">Jenis Dokumen <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="document_type" id="document_type" required>
                            <option value="" disabled selected>Pilih jenis berkas...</option>
                            <?php foreach (($requirements ?? []) as $requirement): ?>
                                <option value="<?= esc($requirement['document_type']) ?>" <?= old('document_type') === $requirement['document_type'] ? 'selected' : '' ?>>
                                    <?= esc($requirement['label']) ?> <?= (int) $requirement['is_required'] ? '*Wajib' : '(Opsional)' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- File Input -->
                    <div class="mb-4">
                        <label for="document_file" class="form-label fw-bold small">Pilih File Berkas <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="document_file" name="document_file" required>
                        <small class="text-muted d-block mt-2">
                            Format dan batas ukuran mengikuti jenis dokumen yang dipilih oleh admin.
                        </small>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="me-2" data-lucide="file-up"></i> Mulai Unggah File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Uploaded Documents Checklist -->
    <div class="col-lg-7">
        <div class="card shadow-sm border">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0"><i class="me-2" data-lucide="folder"></i> Berkas Saya</h5>
                    <small class="text-muted">Status verifikasi berkas yang telah Anda unggah.</small>
                </div>
                <span class="badge bg-label-primary rounded"><?= count($documents) ?> File Terunggah</span>
            </div>
            
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($documents)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fs-1 mb-2" data-lucide="folder-open"></i>
                            <p class="mb-0">Belum ada berkas terunggah.</p>
                            <small>Silakan gunakan panel kiri untuk mulai mengunggah.</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap g-2">
                                    <div>
                                        <h6 class="text-dark fw-bold mb-1">
                                            <?php 
                                                $labels = [];
                                                foreach (($requirements ?? []) as $requirement) {
                                                    $labels[$requirement['document_type']] = $requirement['label'];
                                                }
                                                echo $labels[$doc['document_type']] ?? esc($doc['document_type']);
                                            ?>
                                        </h6>
                                        <span class="small font-monospace text-muted d-block mb-1"><?= esc($doc['file_name']) ?> (<?= number_format($doc['file_size']/1024, 1) ?> KB)</span>
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        <!-- Verification Status Badges -->
                                        <?php if ($doc['status'] === 'approved'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 me-2">
                                                <i class="me-1" data-lucide="check-circle-2"></i> Disetujui
                                            </span>
                                        <?php elseif ($doc['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 me-2">
                                                <i class="me-1" data-lucide="x-circle"></i> Ditolak
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 me-2">
                                                <i class="me-1" data-lucide="spin"></i> Peninjauan
                                            </span>
                                        <?php endif; ?>

                                        <!-- Actions -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon btn-outline-secondary p-1" type="button" data-bs-toggle="dropdown" style="width: 30px; height: 30px;">
                                                <i  data-lucide="more-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a class="dropdown-item" href="<?= base_url('operator/documents/'.$doc['id'].'/view') ?>" target="_blank">
                                                        <i  data-lucide="external-link"></i> Pratinjau File
                                                    </a>
                                                </li>
                                                <!-- Only allow deleting if not yet approved to prevent messing verified states -->
                                                <?php if ($doc['status'] !== 'approved'): ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="<?= base_url('pendaftar/dokumen/'.$doc['id'].'/delete') ?>" method="POST" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="button" class="dropdown-item text-danger delete-confirm">
                                                                <i  data-lucide="trash-2"></i> Hapus Berkas
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Display Rejection Comment overlay box -->
                                <?php if ($doc['status'] === 'rejected' && !empty($doc['rejection_reason'])): ?>
                                    <div class="mt-2 p-2 bg-danger bg-opacity-10 border border-danger border-opacity-20 rounded">
                                        <span class="text-danger small fw-bold"><i class="me-1" data-lucide="message-square-off"></i> Catatan Penolakan Operator:</span>
                                        <p class="mb-0 small text-dark mt-1"><?= esc($doc['rejection_reason']) ?></p>
                                        <small class="text-muted d-block mt-2">Silakan unggah ulang berkas pengganti yang valid pada form sebelah kiri.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<?= $this->endSection() ?>
