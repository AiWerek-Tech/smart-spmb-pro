<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="admin-page-shell role-page-shell">
    <!-- Header Page with Academic Year -->
    <div class="role-page-header">
        <div>
            <h1 class="role-page-header__title">Panel Operator</h1>
            <p class="role-page-header__subtitle">Halaman kerja verifikasi berkas dan sinkronisasi data Dapodik calon peserta didik.</p>
        </div>
        <div class="role-page-actions">
            <span class="badge bg-label-primary p-2 px-3 fs-6 rounded d-flex align-items-center"><i data-lucide="calendar" class="me-1" style="width: 16px; height: 16px;"></i> TA <?= esc($academicYear) ?></span>
        </div>
    </div>

    <!-- Operator Task Cards -->
    <div class="row g-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card stat-card-primary h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="stat-label">Pendaftar Baru</span>
                        <div class="stat-icon stat-icon-primary">
                            <i data-lucide="graduation-cap"></i>
                        </div>
                    </div>
                    <h3 class="stat-value mb-1" style="color: var(--sp-primary);"><?= number_format($stats['total_submitted'], 0, ',', '.') ?></h3>
                </div>
                <div class="mt-2">
                    <span class="fw-semibold text-success small"><i data-lucide="check" class="d-inline-block align-middle me-1" style="width: 12px; height: 12px;"></i> Sudah disubmit</span>
                    <small class="text-muted ms-1">menunggu review</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card stat-card-warning h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="stat-label">Antrean Berkas</span>
                        <div class="stat-icon stat-icon-warning">
                            <i data-lucide="folder-open"></i>
                        </div>
                    </div>
                    <h3 class="stat-value mb-1" style="color: var(--sp-warning);"><?= number_format($stats['pending_verif_docs'], 0, ',', '.') ?></h3>
                </div>
                <div class="mt-2">
                    <?php if ($stats['pending_verif_docs'] > 0): ?>
                        <span class="fw-semibold text-danger small"><i data-lucide="alert-circle" class="d-inline-block align-middle me-1" style="width: 12px; height: 12px;"></i> Butuh Verifikasi</span>
                    <?php else: ?>
                        <span class="fw-semibold text-success small"><i data-lucide="check-circle" class="d-inline-block align-middle me-1" style="width: 12px; height: 12px;"></i> Bersih</span>
                    <?php endif; ?>
                    <small class="text-muted ms-1">dokumen pending</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card stat-card-info h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="stat-label">Lolos Verifikasi</span>
                        <div class="stat-icon stat-icon-info">
                            <i data-lucide="check-square"></i>
                        </div>
                    </div>
                    <h3 class="stat-value mb-1" style="color: var(--sp-info);"><?= number_format($stats['total_verified'], 0, ',', '.') ?></h3>
                </div>
                <div class="mt-2">
                    <span class="fw-semibold text-info small"><i data-lucide="shield-check" class="d-inline-block align-middle me-1" style="width: 12px; height: 12px;"></i> Berkas Valid</span>
                    <small class="text-muted ms-1">siap seleksi akhir</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card stat-card-success h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="stat-label">Kesiapan Dapodik</span>
                        <div class="stat-icon stat-icon-success">
                            <i data-lucide="check-circle"></i>
                        </div>
                    </div>
                    <h3 class="stat-value mb-1" style="color: var(--sp-success);"><?= number_format($stats['dapodik_ready'], 0, ',', '.') ?></h3>
                </div>
                <div class="mt-2">
                    <span class="fw-semibold text-success small">
                        <?= $stats['total_submitted'] > 0 ? number_format(($stats['dapodik_ready'] / $stats['total_submitted']) * 100, 1) : 0 ?>%
                    </span>
                    <small class="text-muted ms-1">data siap sync</small>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Main Workspace Split -->
    <!-- Column 1: Shortcuts & Actions -->
    <div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-transparent">
                <h5 class="card-title m-0">Akses Pintasan</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded">
                    <a href="<?= base_url('operator/registrants') ?>" class="list-group-item list-group-item-action py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-start">
                            <i data-lucide="users" class="text-primary me-3 flex-shrink-0" style="width: 20px; height: 20px; margin-top: 2px;"></i>
                            <div>
                                <span class="fw-semibold text-dark small d-block">Kelola Calon Peserta</span>
                                <small class="text-muted" style="font-size: 0.75rem;">Lihat detail formulir & riwayat data.</small>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="text-muted" style="width: 16px; height: 16px;"></i>
                    </a>
                    <a href="<?= base_url('operator/dapodik') ?>" class="list-group-item list-group-item-action py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-start">
                            <i data-lucide="check-square" class="text-success me-3 flex-shrink-0" style="width: 20px; height: 20px; margin-top: 2px;"></i>
                            <div>
                                <span class="fw-semibold text-dark small d-block">Laporan Validasi Dapodik</span>
                                <small class="text-muted" style="font-size: 0.75rem;">Cek kelengkapan 11 field F-PD.</small>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="text-muted" style="width: 16px; height: 16px;"></i>
                    </a>
                    <a href="<?= base_url('operator/export/excel') ?>" class="list-group-item list-group-item-action py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-start">
                            <i data-lucide="file-text" class="text-info me-3 flex-shrink-0" style="width: 20px; height: 20px; margin-top: 2px;"></i>
                            <div>
                                <span class="fw-semibold text-dark small d-block">Ekspor Excel</span>
                                <small class="text-muted" style="font-size: 0.75rem;">Unduh seluruh database pendaftar SPMB.</small>
                            </div>
                        </div>
                        <i data-lucide="download" class="text-muted" style="width: 16px; height: 16px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Column 2: Recent Registrations -->
    <div class="col-lg-8">
        <div class="card shadow-sm border">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title m-0 text-dark d-flex align-items-center">
                    <i data-lucide="clock" class="me-2 text-muted" style="width: 18px; height: 18px;"></i> Pendaftaran Terbaru (Submitted)
                </h5>
                <a href="<?= base_url('operator/registrants') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Pendaftaran</th>
                                <th>Nama Peserta / NIK</th>
                                <th>Jalur</th>
                                <th>Tanggal Masuk</th>
                                <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentRegistrants)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i data-lucide="user-minus" class="d-block mx-auto mb-3" style="width: 40px; height: 40px; color: var(--sp-text-muted);"></i>
                                        <p class="mb-0">Belum ada pendaftaran yang masuk.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentRegistrants as $rr): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?= esc($rr['registration_number']) ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark mb-1"><?= esc($rr['full_name']) ?></div>
                                            <small class="text-muted">NIK: <?= esc($rr['nik']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= esc($rr['jalur_name']) ?></span>
                                        </td>
                                        <td class="small text-muted"><?= date('d M Y, H:i', strtotime($rr['submitted_at'])) ?> WIB</td>
                                        <td class="pe-4 text-center">
                                            <a href="<?= base_url('operator/registrants/'.$rr['id']) ?>" class="btn btn-sm btn-outline-primary px-3 d-inline-flex align-items-center" title="Detail Profil">
                                                Detail
                                            </a>
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
</div>
<?= $this->endSection() ?>
