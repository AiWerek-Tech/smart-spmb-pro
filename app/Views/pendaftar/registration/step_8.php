<?= $this->extend('pendaftar/registration/wizard_layout') ?>

<?= $this->section('additional_css') ?>
<style>
    .doc-upload-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .doc-upload-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-color: #667eea;
    }
    .status-badge {
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 20px;
    }
    .jalur-card {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .jalur-card:hover {
        border-color: #667eea;
        background-color: #fcfdff;
    }
    .jalur-radio:checked + .jalur-card {
        border-color: #667eea;
        background-color: #f5f7ff;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('step_content') ?>
<div class="p-4">
    <h4 class="wizard-step-title"><i data-lucide="file-up"></i> Langkah 8: Unggah Dokumen & Finalisasi</h4>
    
    <?php
    // Index documents by type
    $docs = [];
    foreach ($stepData as $doc) {
        $docs[$doc['document_type']] = $doc;
    }
    
    $requirementsByType = [];
    foreach (($requirements ?? []) as $requirement) {
        $requirementsByType[$requirement['document_type']] = $requirement;
    }

    $uploadRequirementsByType = [];
    foreach (($uploadRequirements ?? $requirements ?? []) as $requirement) {
        $uploadRequirementsByType[$requirement['document_type']] = $requirement;
    }

    $requiredTypes = [];
    foreach (($requirements ?? []) as $requirement) {
        if ((int) ($requirement['is_required'] ?? 0) === 1) {
            $requiredTypes[$requirement['document_type']] = $requirement['label'];
        }
    }
    
    $isComplete = true;
    foreach (array_keys($requiredTypes) as $type) {
        if (!isset($docs[$type]) || $docs[$type]['status'] === 'rejected') {
            $isComplete = false;
            break;
        }
    }
    ?>

    <!-- 1. Documents Section -->
    <div class="mb-5">
        <h5 class="role-subsection-title"><i data-lucide="info"></i> 1. Dokumen Pendaftaran</h5>
        <div class="row">
            <?php foreach ($uploadRequirementsByType as $type => $requirementMeta): ?>
                <?php
                    $label = $requirementMeta['label'] ?? $type;
                    $isRequired = isset($requiredTypes[$type]);
                    $isRequiredInAnyJalur = (int) ($requirementMeta['is_required'] ?? 0) === 1;
                ?>
                <div class="col-md-4 mb-3">
                    <div class="doc-upload-card p-3 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                <h6 class="fw-bold mb-0"><?= esc($label) ?></h6>
                                <?php if ($isRequired): ?>
                                    <span class="badge bg-danger">Wajib</span>
                                <?php elseif ($isRequiredInAnyJalur): ?>
                                    <span class="badge bg-warning text-dark">Wajib Jalur</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary">Opsional</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted d-block mb-3">Maksimal <?= number_format(((int) ($requirementMeta['max_size_kb'] ?? 2048)) / 1024, 1) ?> MB (<?= esc(strtoupper($requirementMeta['allowed_extensions'] ?? 'jpg,jpeg,png')) ?>)</small>
                        </div>
                        
                        <div>
                            <?php if (isset($docs[$type])): ?>
                                <!-- Already Uploaded State -->
                                <?php 
                                    $ext = pathinfo($docs[$type]['file_name'], PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png']);
                                    $status = $docs[$type]['status'];
                                    
                                    $cardBg = 'bg-light';
                                    $statusBadge = '';
                                    if ($status === 'approved') {
                                        $cardBg = 'bg-success bg-opacity-10';
                                        $statusBadge = '<span class="badge bg-success text-white"><i data-lucide="check-circle" class="me-1" style="width:12px;height:12px;"></i> Berhasil (Disetujui)</span>';
                                    } elseif ($status === 'rejected') {
                                        $cardBg = 'bg-danger bg-opacity-10';
                                        $statusBadge = '<span class="badge bg-danger text-white"><i data-lucide="alert-triangle" class="me-1" style="width:12px;height:12px;"></i> Perlu Upload Ulang (Ditolak)</span>';
                                    } else {
                                        $cardBg = 'bg-info bg-opacity-10';
                                        $statusBadge = '<span class="badge bg-info text-white"><i data-lucide="clock" class="me-1" style="width:12px;height:12px;"></i> Sudah Diunggah</span>';
                                    }
                                ?>
                                <div class="<?= $cardBg ?> p-2 rounded mb-2 border" style="font-size: 0.85rem;">
                                    <?php if ($isImage): ?>
                                        <div class="text-center mb-2 bg-white p-1 rounded border">
                                            <img src="<?= base_url('writable/' . $docs[$type]['file_path']) ?>" alt="Preview" class="img-fluid rounded" style="max-height: 80px; object-fit: contain;">
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center mb-2 bg-white p-2 rounded border">
                                            <i class="text-primary" data-lucide="file-text" style="width:36px;height:36px;"></i>
                                            <span class="d-block small text-muted mt-1"><?= strtoupper($ext) ?> Document</span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="text-secondary text-truncate fw-semibold mb-1" title="<?= esc($docs[$type]['file_name']) ?>"><?= esc($docs[$type]['file_name']) ?></div>
                                    <div style="font-size: 0.8rem;">
                                        <?= $statusBadge ?>
                                        <?php if ($status === 'rejected' && !empty($docs[$type]['rejection_reason'])): ?>
                                            <div class="text-danger mt-1 font-monospace" style="font-size: 0.75rem; line-height: 1.2;">Alasan: <?= esc($docs[$type]['rejection_reason']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="<?= base_url('writable/' . $docs[$type]['file_path']) ?>" class="btn btn-sm btn-outline-primary flex-fill" target="_blank">
                                        <i data-lucide="external-link"></i> Buka
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-doc-btn" data-id="<?= $docs[$type]['id'] ?>">
                                        <i data-lucide="trash-2"></i> Hapus
                                    </button>
                                </div>
                            <?php else: ?>
                                <!-- Not Uploaded State -->
                                <div class="text-center p-3 border border-dashed rounded mb-2 bg-light">
                                    <i class="text-muted mb-2" data-lucide="file-x" style="width:32px;height:32px;"></i>
                                    <span class="badge bg-secondary mb-1">Belum Diunggah</span>
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Silakan unggah dokumen wajib</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary w-100 trigger-upload-btn" data-type="<?= esc($type) ?>">
                                    <i data-lucide="upload"></i> Unggah File
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Hidden Form for Document Upload -->
    <form id="docUploadForm" style="display: none;" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="document_type" id="upload_doc_type">
        <input type="file" name="document_file" id="upload_file_input">
    </form>

    <hr class="my-4">

    <!-- 2. Pathway & Finalization Section -->
    <div class="mb-4">
        <h5 class="role-subsection-title"><i data-lucide="clipboard-check"></i> 2. Jalur Pendaftaran & Finalisasi</h5>
        
        <?php if ($isComplete): ?>
            <form id="stepForm8" method="POST">
                <?= csrf_field() ?>
                
                <div class="alert alert-warning mb-4">
                    <i  data-lucide="alert-triangle"></i> <strong>Perhatian:</strong> Setelah memilih Jalur Pendaftaran dan mengklik <strong>Selesai & Submit</strong>, seluruh data Anda akan dikunci secara permanen dan tidak dapat diubah kembali. Pastikan seluruh data dari Langkah 1-7 sudah diisi dengan benar.
                </div>

                <label class="form-label required-field fw-bold mb-3">Pilih Jalur Pendaftaran</label>
                <div class="row">
                    <?php foreach ($jalurs as $jalur): ?>
                        <div class="col-md-6 mb-3">
                            <label class="w-100 m-0">
                                <input type="radio" name="jalur_id" value="<?= $jalur['id'] ?>" class="d-none jalur-radio" required>
                                <div class="jalur-card p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0"><?= esc($jalur['name']) ?></h6>
                                        <span class="badge bg-info">Kuota: <?= esc($jalur['quota']) ?></span>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem;"><?= esc($jalur['description']) ?></p>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">
                <i  data-lucide="lock"></i> <strong>Form Terkunci:</strong> Harap lengkapi dan unggah seluruh berkas dokumen wajib sesuai konfigurasi tahun pelajaran <?= esc($academicYear ?? '') ?> untuk membuka form pemilihan Jalur Pendaftaran dan melakukan finalisasi pendaftaran.
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Document Upload Trigger
    document.querySelectorAll('.trigger-upload-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            document.getElementById('upload_doc_type').value = type;
            document.getElementById('upload_file_input').click();
        });
    });

    // Handle File Input Change
    document.getElementById('upload_file_input')?.addEventListener('change', async function() {
        if (!this.files || this.files.length === 0) return;

        Swal.fire({
            title: 'Mengunggah Berkas...',
            text: 'Harap tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(document.getElementById('docUploadForm'));
        formData.append('document_file', this.files[0]);

        try {
            // Post via standard AJAX form upload
            const response = await fetch('<?= base_url('/pendaftar/dokumen/upload') ?>', {
                method: 'POST',
                body: formData
            });

            // Refresh page to show uploaded state
            Swal.fire({
                icon: 'success',
                title: 'Dokumen Berhasil Diunggah',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });

        } catch (error) {
            console.error('Upload error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengunggah',
                text: 'Terjadi kesalahan sistem atau ukuran file terlalu besar (Max 2MB).',
                confirmButtonColor: '#667eea'
            });
        }
    });

    // Handle Document Deletion
    document.querySelectorAll('.delete-doc-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const docId = this.getAttribute('data-id');
            
            Swal.fire({
                title: 'Hapus Dokumen?',
                text: "Anda harus mengunggah kembali dokumen ini untuk menyelesaikan pendaftaran.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Berkas...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch(`<?= base_url('/pendaftar/dokumen/') ?>${docId}/delete`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                            })
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Dokumen Terhapus',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });

                    } catch (error) {
                        console.error('Delete error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: 'Terjadi kesalahan sistem saat mencoba menghapus dokumen.',
                            confirmButtonColor: '#667eea'
                        });
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
