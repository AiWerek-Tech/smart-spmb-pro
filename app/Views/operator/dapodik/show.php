<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <!-- Back button -->
        <div class="mb-3">
            <a href="<?= base_url('operator/dapodik') ?>" class="text-decoration-none">
                <i class="me-1" data-lucide="arrow-left"></i> Kembali ke Validasi Dapodik
            </a>
        </div>

        <!-- Candidate Top Summary Card -->
        <div class="card shadow-sm border mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="fw-bold text-dark mb-1"><?= esc($registration['full_name']) ?></h4>
                        <p class="text-muted mb-0">No. Pendaftaran: <strong class="text-primary"><?= esc($registration['registration_number']) ?></strong> | NIK: <?= esc($registration['nik']) ?></p>
                    </div>
                    <div class="col-md-auto text-md-end mt-2 mt-md-0">
                        <div class="d-flex align-items-center">
                            <span class="fs-3 fw-bold text-primary me-2"><?= number_format($registration['dapodik_percentage'], 0) ?>%</span>
                            <div class="progress" style="width: 100px; height: 10px;">
                                <div class="progress-bar <?= $registration['is_dapodik_ready'] ? 'bg-success' : 'bg-warning' ?>" role="progressbar" style="width: <?= $registration['dapodik_percentage'] ?>%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status banners -->
        <?php if ($registration['is_dapodik_ready']): ?>
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 py-3">
                <i class="fs-3 me-3 text-success" data-lucide="check-circle-2"></i>
                <div>
                    <h6 class="text-success fw-bold mb-1">DATA SIAP DAPODIK (100% LENGKAP)</h6>
                    <p class="mb-0 small text-dark">Seluruh 11 data Dapodik wajib telah terisi sempurna. Calon siswa ini siap disinkronisasikan ke sistem Dapodik nasional sekolah.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 py-3">
                <i class="fs-3 me-3 text-warning" data-lucide="alert-circle"></i>
                <div>
                    <h6 class="text-warning fw-bold mb-1">DATA BELUM SIAP DAPODIK (TIDAK LENGKAP)</h6>
                    <p class="mb-0 small text-dark">Ditemukan <strong><?= count($missingFields) ?> data wajib</strong> yang masih kosong. Tekan tombol koreksi di bawah untuk mengisi data.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Detailed 11 checklist card -->
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title text-primary m-0"><i class="me-2" data-lucide="clipboard-check"></i> Laporan Validasi 11 Field Dapodik Wajib</h5>
                <small class="text-muted">Setiap field harus terisi agar dapat diekspor tanpa kendala ke Dapodik.</small>
            </div>
            
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($fields as $index => $field): ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3 fs-5">
                                    <?php if ($field['is_filled']): ?>
                                        <i class="text-success"  title="Terisi" data-lucide="check-circle-2"></i>
                                    <?php else: ?>
                                        <i class="text-danger"  title="Kosong" data-lucide="x-circle"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark d-block" style="font-size: 0.9rem;"><?= esc($field['label']) ?></span>
                                    <?php if ($field['is_filled']): ?>
                                        <span class="text-muted small">Nilai: <strong class="text-dark"><?= esc($field['value']) ?></strong></span>
                                    <?php else: ?>
                                        <span class="text-danger small fw-bold">Belum terisi</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($field['is_filled']): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">OK</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Missing</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Actions inside card -->
            <div class="card-footer bg-light border-top d-flex justify-content-end p-3">
                <a href="<?= base_url('operator/registrants/'.$registration['id'].'/edit') ?>" class="btn btn-warning">
                    <i class="me-1" data-lucide="edit"></i> Koreksi Data F-PD Siswa
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
