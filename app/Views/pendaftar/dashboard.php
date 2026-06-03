<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$registrationGate = $registrationGate ?? ['is_open' => true, 'status' => 'unconfigured', 'message' => ''];
$canOpenRegistration = !empty($registration) || (bool) ($registrationGate['is_open'] ?? true);
?>
<div class="admin-page-shell role-page-shell">
    <!-- Welcome Header -->
    <div class="role-page-header">
        <div>
            <div>
                <h1 class="role-page-header__title">Dashboard Calon Siswa</h1>
                <p class="role-page-header__subtitle">Halo, <strong><?= esc($student['full_name']) ?></strong>. Pantau terus status kelulusan dan validasi berkas Anda di sini.</p>
            </div>
        </div>
    </div>

    <!-- MAIN STATUS CARD -->
    <div class="row g-3">
    <div class="col-md-8">
        <!-- 1. DRAFT STATE -->
        <?php if (empty($registration)): ?>
            <div class="card h-100" style="border-left: 4px solid var(--sp-warning);">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title text-warning m-0 d-flex align-items-center">
                        <i data-lucide="edit-3" class="me-2" style="width: 20px; height: 20px;"></i> Pendaftaran Belum Selesai
                    </h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-dark fw-semibold">Lengkapi data pendaftaran Anda sekarang juga untuk mendapatkan Nomor Pendaftaran resmi.</p>
                        <p class="text-muted small">
                            Anda diharuskan mengisi data secara lengkap yang terbagi menjadi 8 langkah (F-PD Dapodik). Data dapat disimpan secara bertahap dan otomatis.
                        </p>
                        <?php if (!$canOpenRegistration): ?>
                            <div class="alert alert-warning border-0 d-flex align-items-start mb-3">
                                <i data-lucide="calendar-clock" class="me-2 mt-1 flex-shrink-0" style="width: 18px; height: 18px;"></i>
                                <div>
                                    <strong>Jadwal Pendaftaran</strong>
                                    <p class="mb-0 small"><?= esc($registrationGate['message'] ?? 'Pendaftaran belum dibuka.') ?></p>
                                </div>
                            </div>
                        <?php elseif (($registrationGate['status'] ?? '') === 'open'): ?>
                            <div class="alert alert-success border-0 d-flex align-items-start mb-3">
                                <i data-lucide="calendar-check" class="me-2 mt-1 flex-shrink-0" style="width: 18px; height: 18px;"></i>
                                <div>
                                    <strong>Jadwal Pendaftaran Aktif</strong>
                                    <p class="mb-0 small"><?= esc($registrationGate['message'] ?? 'Pendaftaran sedang dibuka.') ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="alert alert-primary border-0 d-flex align-items-start mb-3">
                            <i data-lucide="save" class="me-2 mt-1 flex-shrink-0" style="width: 18px; height: 18px;"></i>
                            <div>
                                <strong>Draft Tersimpan</strong>
                                <p class="mb-0 small">
                                    <?= (int) ($draftStepCount ?? 0) ?> dari 8 langkah sudah memiliki data.
                                    <?php if (!empty($draftLastSavedAt)): ?>
                                        Terakhir disimpan <?= esc(date('d M Y H:i', strtotime($draftLastSavedAt))) ?>.
                                    <?php else: ?>
                                        Mulai isi formulir dan lanjutkan kapan saja.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="p-3 rounded border mb-3" style="background-color: rgba(var(--sp-primary-rgb), 0.02); border-color: var(--sp-border-color) !important;">
                            <h6 class="fw-bold small text-muted mb-2 d-flex align-items-center">
                                <i data-lucide="info" class="me-1" style="width: 14px; height: 14px; color: var(--sp-primary);"></i> Berkas Wajib yang Harus Disiapkan:
                            </h6>
                            <ul class="small mb-0 ps-3 text-muted">
                                <?php foreach (($requiredDocumentLabels ?? ['Kartu Keluarga (KK)', 'Akta Kelahiran', 'Pas Foto 3x4 Calon Siswa']) as $label): ?>
                                    <li><?= esc($label) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <?php if ($canOpenRegistration): ?>
                            <a href="<?= base_url('pendaftar/daftar/step/' . (int) ($draftContinueStep ?? 1)) ?>" class="btn btn-primary btn-lg w-100 fw-bold d-flex align-items-center justify-content-center">
                                <i data-lucide="file-text" class="me-2" style="width: 18px; height: 18px;"></i> <?= (int) ($draftStepCount ?? 0) > 1 ? 'Lanjutkan Pengisian' : 'Isi Formulir Pendaftaran Sekarang' ?>
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-lg w-100 fw-bold d-flex align-items-center justify-content-center" disabled>
                                <i data-lucide="lock" class="me-2" style="width: 18px; height: 18px;"></i> Formulir Belum Tersedia
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <!-- 2. SUBMITTED STATE -->
        <?php elseif ($registration['status'] === 'submitted'): ?>
            <div class="card h-100" style="border-left: 4px solid var(--sp-primary);">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title m-0 d-flex align-items-center">
                        <i data-lucide="clock" class="me-2" style="width: 20px; height: 20px;"></i> Berkas Sedang Direview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4 rounded border mb-4" style="background-color: rgba(var(--sp-primary-rgb), 0.02); border-color: var(--sp-border-color) !important;">
                        <span class="d-block small text-muted mb-1">Nomor Pendaftaran Resmi Anda:</span>
                        <h2 class="text-primary fw-bold font-monospace m-0" style="letter-spacing: 1px; font-family: 'Plus Jakarta Sans', sans-serif;"><?= esc($registration['registration_number']) ?></h2>
                    </div>
                    
                    <div class="alert alert-info border-0 shadow-xs mb-3 d-flex align-items-start">
                        <i data-lucide="info" class="me-2 mt-1 flex-shrink-0" style="width: 20px; height: 20px; color: var(--sp-info);"></i>
                        <div>
                            <strong class="text-dark">Pendaftaran Berhasil Dikirim!</strong>
                            <p class="mb-0 small text-dark opacity-75">Berkas fisik dan data Anda sedang dalam proses verifikasi oleh operator pendaftaran. Hasil verifikasi akan diperbarui di halaman ini secara berkala.</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="<?= base_url('pendaftar/cetak/bukti') ?>" class="btn btn-outline-primary d-inline-flex align-items-center" target="_blank">
                            <i data-lucide="download" class="me-2" style="width: 16px; height: 16px;"></i> Unduh Bukti Pendaftaran
                        </a>
                    </div>
                </div>
            </div>

        <!-- 3. VERIFIED STATE -->
        <?php elseif ($registration['status'] === 'verified'): ?>
            <div class="card h-100" style="border-left: 4px solid var(--sp-info);">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title text-info m-0 d-flex align-items-center">
                        <i data-lucide="check-square" class="me-2" style="width: 20px; height: 20px;"></i> Berkas Lolos Verifikasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4 rounded border mb-4" style="background-color: rgba(var(--sp-info-rgb), 0.02); border-color: var(--sp-border-color) !important;">
                        <span class="d-block small text-muted mb-1">Nomor Pendaftaran Resmi Anda:</span>
                        <h2 class="text-info fw-bold font-monospace m-0" style="letter-spacing: 1px; font-family: 'Plus Jakarta Sans', sans-serif;"><?= esc($registration['registration_number']) ?></h2>
                    </div>

                    <div class="alert alert-success border-0 shadow-xs mb-4 d-flex align-items-start">
                        <i data-lucide="check-circle" class="me-2 mt-1 flex-shrink-0" style="width: 20px; height: 20px; color: var(--sp-success);"></i>
                        <div>
                            <strong class="text-dark">Berkas Dinyatakan VALID!</strong>
                            <p class="mb-0 small text-dark opacity-75">Selamat! Seluruh dokumen wajib Anda telah disetujui oleh operator. Akun Anda telah siap diikutsertakan dalam seleksi kelulusan akhir sekolah.</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3 gap-2">
                        <a href="<?= base_url('pendaftar/cetak/bukti') ?>" class="btn btn-outline-primary d-inline-flex align-items-center" target="_blank">
                            <i data-lucide="download" class="me-1" style="width: 14px; height: 14px;"></i> Bukti
                        </a>
                        <a href="<?= base_url('pendaftar/cetak/kartu') ?>" class="btn btn-info text-white d-inline-flex align-items-center" target="_blank">
                            <i data-lucide="credit-card" class="me-1" style="width: 14px; height: 14px;"></i> Unduh Kartu Peserta
                        </a>
                    </div>
                </div>
            </div>

        <!-- 4. ACCEPTED STATE -->
        <?php elseif ($registration['status'] === 'accepted'): ?>
            <div class="card h-100 bg-success bg-opacity-5" style="border-left: 4px solid var(--sp-success);">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title text-success m-0 d-flex align-items-center">
                        <i data-lucide="award" class="me-2" style="width: 20px; height: 20px;"></i> Selamat! Anda Diterima
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4 bg-white rounded border mb-4" style="border-color: var(--sp-border-color) !important;">
                        <span class="d-block small text-muted mb-1">Nomor Pendaftaran:</span>
                        <h2 class="text-success fw-bold font-monospace m-0" style="letter-spacing: 1px; font-family: 'Plus Jakarta Sans', sans-serif;"><?= esc($registration['registration_number']) ?></h2>
                    </div>

                    <div class="p-4 border rounded mb-4 text-center" style="background-color: rgba(var(--sp-success-rgb), 0.05); border-color: rgba(var(--sp-success-rgb), 0.2) !important;">
                        <i data-lucide="party-popper" class="text-success mb-2" style="width: 48px; height: 48px;"></i>
                        <h4 class="role-detail-title text-success">ANDA DINYATAKAN LULUS SELEKSI!</h4>
                        <p class="text-dark mb-0 small opacity-75">
                            Selamat bergabung dengan <strong>SMA Smart SPMB Pro</strong>. Silakan unduh Kartu Peserta Anda dan ikuti panduan pendaftaran ulang resmi di pengumuman sekolah.
                        </p>
                    </div>

                    <div class="d-flex justify-content-end gap-2 flex-wrap">
                        <a href="<?= base_url('pendaftar/cetak/bukti') ?>" class="btn btn-outline-success d-inline-flex align-items-center" target="_blank">
                            <i data-lucide="download" class="me-1" style="width: 14px; height: 14px;"></i> Bukti
                        </a>
                        <a href="<?= base_url('pendaftar/cetak/kartu') ?>" class="btn btn-outline-info d-inline-flex align-items-center" target="_blank">
                            <i data-lucide="credit-card" class="me-1" style="width: 14px; height: 14px;"></i> Kartu
                        </a>
                        <a href="<?= base_url('pendaftar/cetak/skl') ?>" class="btn btn-success d-inline-flex align-items-center" target="_blank">
                            <i data-lucide="award" class="me-1" style="width: 14px; height: 14px;"></i> Cetak Surat Kelulusan (SKL)
                        </a>
                    </div>
                </div>
            </div>

        <!-- 5. REJECTED STATE -->
        <?php else: ?>
            <div class="card h-100 bg-danger bg-opacity-5" style="border-left: 4px solid var(--sp-danger);">
                <div class="card-header bg-transparent py-3">
                    <h5 class="card-title text-danger m-0 d-flex align-items-center">
                        <i data-lucide="x-circle" class="me-2" style="width: 20px; height: 20px;"></i> Hasil Seleksi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4 bg-white rounded border mb-4" style="border-color: var(--sp-border-color) !important;">
                        <span class="d-block small text-muted mb-1">Nomor Pendaftaran:</span>
                        <h2 class="text-danger fw-bold font-monospace m-0" style="letter-spacing: 1px; font-family: 'Plus Jakarta Sans', sans-serif;"><?= esc($registration['registration_number']) ?></h2>
                    </div>

                    <div class="p-4 border rounded mb-0 text-center" style="background-color: rgba(var(--sp-danger-rgb), 0.05); border-color: rgba(var(--sp-danger-rgb), 0.2) !important;">
                        <i data-lucide="frown" class="text-danger mb-2" style="width: 48px; height: 48px;"></i>
                        <h5 class="text-danger fw-bold">MOHON MAAF, ANDA BELUM LULUS</h5>
                        <p class="text-dark mb-0 small opacity-75">
                            Terima kasih telah berpartisipasi dalam proses seleksi SPMB kami. Karena keterbatasan kuota penerimaan jalur ini, mohon maaf saat ini Anda dinyatakan belum dapat bergabung. Tetap semangat!
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- SIDE PANEL: Profile Summary & Action List -->
    <div class="col-md-4">
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-transparent py-3">
                <h5 class="card-title m-0 text-dark">Informasi Akun</h5>
            </div>
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('user_name')) ?>&background=7c3aed&color=fff" class="rounded-circle border p-1" style="height: 54px; width: 54px;" alt="Avatar">
                    <div class="ms-3">
                        <h6 class="text-dark fw-bold mb-0"><?= esc(session()->get('user_name')) ?></h6>
                        <span class="small text-muted"><?= esc(session()->get('user_email')) ?></span>
                    </div>
                </div>
                
                <hr>

                <div class="list-group list-group-flush">
                    <?php if ($canOpenRegistration): ?>
                    <a href="<?= base_url('pendaftar/daftar') ?>" class="list-group-item list-group-item-action py-2 px-1 border-0 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i data-lucide="edit-3" class="text-warning me-2" style="width: 16px; height: 16px;"></i>
                            <span class="small fw-semibold text-dark">Formulir Pendaftaran</span>
                        </div>
                        <i data-lucide="chevron-right" class="text-muted" style="width: 14px; height: 14px;"></i>
                    </a>
                    <?php else: ?>
                    <div class="list-group-item py-2 px-1 border-0 d-flex justify-content-between align-items-center opacity-75">
                        <div class="d-flex align-items-center">
                            <i data-lucide="lock" class="text-muted me-2" style="width: 16px; height: 16px;"></i>
                            <span class="small fw-semibold text-muted">Formulir Pendaftaran</span>
                        </div>
                        <span class="badge bg-label-secondary border rounded">Terkunci</span>
                    </div>
                    <?php endif; ?>
                    <a href="<?= base_url('pendaftar/dokumen') ?>" class="list-group-item list-group-item-action py-2 px-1 border-0 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i data-lucide="upload-cloud" class="text-primary me-2" style="width: 16px; height: 16px;"></i>
                            <span class="small fw-semibold text-dark">Berkas Upload</span>
                        </div>
                        <span class="badge bg-label-primary border rounded"><?= $totalUploaded ?> File</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<?= $this->endSection() ?>
