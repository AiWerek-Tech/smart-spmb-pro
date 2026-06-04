<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$registrationGate = $registrationGate ?? ['is_open' => true, 'status' => 'unconfigured', 'message' => ''];
$canOpenRegistration = !empty($registration) || (bool) ($registrationGate['is_open'] ?? true);
$settingModel = new \App\Models\SettingModel();
$footerWhatsapp = preg_replace('/[^0-9]/', '', (string)$settingModel->getValue('whatsapp', '6282190822641'));
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
                                    <?php if (!empty($registrationGate['gelombang'])): ?>
                                        <div class="mt-2 small">
                                            <span class="badge bg-success me-1"><?= esc($registrationGate['gelombang']['name'] ?? 'Gelombang') ?></span>
                                            <span class="text-dark">Tahun Pelajaran <strong><?= esc($academicYear ?? '-') ?></strong></span>
                                            <?php if (!empty($registrationGate['gelombang']['open_date']) && !empty($registrationGate['gelombang']['close_date'])): ?>
                                                <br><span class="text-muted">Periode: <?= date('d M Y', strtotime($registrationGate['gelombang']['open_date'])) ?> — <?= date('d M Y', strtotime($registrationGate['gelombang']['close_date'])) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
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
                        <?php endif; ?>
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

        <!-- TAGIHAN & PEMBAYARAN -->
        <div class="card shadow-sm border mb-4 mt-3">
            <div class="card-header bg-transparent py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 text-dark d-flex align-items-center">
                    <i data-lucide="credit-card" class="me-2 text-primary" style="width: 20px; height: 20px;"></i> Tagihan & Status Pembayaran
                </h5>
                <span class="badge bg-label-secondary border rounded"><?= count($invoices ?? []) ?> Tagihan</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($invoices)): ?>
                    <div class="text-center py-5 text-muted">
                        <i data-lucide="wallet" class="mb-2 text-muted" style="width: 48px; height: 48px;"></i>
                        <p class="mb-0 fw-semibold">Belum ada tagihan diterbitkan</p>
                        <small class="text-muted">Tagihan akan otomatis terbit setelah Anda mengirimkan pendaftaran atau jika terdapat biaya pendaftaran awal.</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Rincian Biaya</th>
                                    <th>Total Tagihan</th>
                                    <th>Sudah Dibayar</th>
                                    <th>Status</th>
                                    <th class="text-end" style="padding-right: 24px;">Rincian</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td>
                                            <code class="fw-bold text-dark"><?= esc($inv['invoice_number']) ?></code>
                                            <?php if ((int)$inv['registration_id'] === 0): ?>
                                                <span class="badge bg-label-warning text-dark ms-1" style="font-size: 0.65rem;">Biaya Awal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <?php if (!empty($inv['items'])): ?>
                                                    <?php foreach ($inv['items'] as $item): ?>
                                                        <span class="small text-dark fw-semibold">• <?= esc($item['name']) ?> (Rp <?= number_format($item['total_amount'], 0, ',', '.') ?>)</span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-muted small">Tidak ada rincian item</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></span>
                                        </td>
                                        <td>
                                            <span class="text-success small">Rp <?= number_format($inv['paid_amount'], 0, ',', '.') ?></span>
                                        </td>
                                        <td>
                                            <?php if ($inv['status'] === 'paid'): ?>
                                                <span class="badge bg-label-success"><i class="me-1" data-lucide="check-circle" style="width:12px;height:12px;"></i> Lunas</span>
                                            <?php elseif ($inv['has_pending_payment']): ?>
                                                <span class="badge bg-label-info text-dark"><i class="me-1" data-lucide="clock" style="width:12px;height:12px;"></i> Menunggu Verifikasi</span>
                                            <?php elseif ($inv['status'] === 'partial'): ?>
                                                <span class="badge bg-label-warning text-dark"><i class="me-1" data-lucide="help-circle" style="width:12px;height:12px;"></i> Sebagian</span>
                                            <?php else: ?>
                                                <span class="badge bg-label-danger"><i class="me-1" data-lucide="alert-circle" style="width:12px;height:12px;"></i> Belum Bayar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end" style="padding-right: 24px;">
                                            <?php if ($inv['status'] !== 'paid'): ?>
                                                <?php if ($inv['has_pending_payment']): ?>
                                                    <span class="badge bg-label-info text-dark">Sedang Diverifikasi</span>
                                                <?php else: ?>
                                                    <div class="d-inline-flex gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-primary show-payment-instructions" data-number="<?= esc($inv['invoice_number']) ?>" data-total="Rp <?= number_format($inv['total_amount'] - $inv['paid_amount'], 0, ',', '.') ?>">
                                                            Cara Bayar
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary confirm-payment-btn" data-id="<?= esc($inv['id']) ?>" data-number="<?= esc($inv['invoice_number']) ?>" data-balance="<?= esc($inv['total_amount'] - $inv['paid_amount']) ?>" data-balance-formatted="Rp <?= number_format($inv['total_amount'] - $inv['paid_amount'], 0, ',', '.') ?>">
                                                            Konfirmasi Bayar
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-success" disabled>
                                                    <i data-lucide="check" style="width:14px;height:14px;"></i> Lunas
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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

        <!-- DETAIL PENERIMAAN -->
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-transparent py-3 d-flex align-items-center gap-2">
                <i data-lucide="info" class="text-primary" style="width: 20px; height: 20px;"></i>
                <h5 class="card-title m-0 text-dark">Detail Penerimaan</h5>
            </div>
            <div class="card-body p-3">
                <ul class="list-group list-group-flush mb-0">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 bg-transparent">
                        <span class="text-muted small">Tahun Pelajaran</span>
                        <span class="fw-bold small text-dark"><?= esc($academicYear) ?></span>
                    </li>
                    <?php if (!empty($registrationGate['gelombang'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 bg-transparent">
                            <span class="text-muted small">Gelombang Aktif</span>
                            <span class="badge bg-label-primary border"><?= esc($registrationGate['gelombang']['name']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 bg-transparent">
                            <span class="text-muted small">Masa Pendaftaran</span>
                            <span class="fw-bold small text-dark" style="font-size:0.72rem;">
                                <?= date('d M Y', strtotime($registrationGate['gelombang']['open_date'])) ?> s/d <?= date('d M Y', strtotime($registrationGate['gelombang']['close_date'])) ?>
                            </span>
                        </li>
                    <?php else: ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 bg-transparent">
                            <span class="text-muted small">Gelombang Aktif</span>
                            <span class="text-muted small">-</span>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($registration['jalur_name'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 bg-transparent">
                            <span class="text-muted small">Jalur Pilihan</span>
                            <span class="badge bg-label-info border"><?= esc($registration['jalur_name']) ?></span>
                        </li>
                    <?php elseif (!empty($registration['jalur_id'])): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 bg-transparent">
                            <span class="text-muted small">Jalur Pilihan</span>
                            <span class="badge bg-label-info border">ID: <?= esc($registration['jalur_id']) ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="paymentConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentConfirmForm" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-warning border-0 small mb-3">
                        Silakan unggah bukti transfer bank / struk pembayaran Anda. Panitia akan memverifikasi berkas dalam 1-2 hari kerja.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Invoice</label>
                        <input type="text" class="form-control" id="confirm_invoice_number" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required-field" for="confirm_payment_method_id">Metode Pembayaran Tujuan</label>
                        <select class="form-select" id="confirm_payment_method_id" name="payment_method_id" required>
                            <option value="">-- Pilih Rekening Tujuan --</option>
                            <?php foreach (($paymentMethods ?? []) as $method): ?>
                                <option value="<?= esc($method['id']) ?>"><?= esc($method['name']) ?> - <?= esc($method['account_number']) ?> a.n <?= esc($method['account_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required-field" for="confirm_amount">Nominal yang Ditransfer</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="confirm_amount" name="amount" min="1" step="1" required>
                        </div>
                        <div class="help-text text-muted small mt-1">Sisa tagihan: <span id="confirm_balance_text" class="fw-bold"></span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required-field" for="confirm_proof_file">Upload Bukti Transfer (Gambar / PDF, max 2MB)</label>
                        <input type="file" class="form-control" id="confirm_proof_file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="confirm_notes">Catatan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="confirm_notes" name="notes" rows="2" placeholder="Contoh: Transfer dari rekening BNI atas nama..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Bukti Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '.confirm-payment-btn', function() {
            const invoiceId = $(this).data('id');
            const invoiceNum = $(this).data('number');
            const balance = $(this).data('balance');
            const balanceFormatted = $(this).data('balance-formatted');
            
            $('#confirm_invoice_number').val(invoiceNum);
            $('#confirm_amount').val(balance).attr('max', balance);
            $('#confirm_balance_text').text(balanceFormatted);
            $('#paymentConfirmForm').attr('action', `<?= base_url('pendaftar/tagihan') ?>/${invoiceId}/konfirmasi`);
            
            $('#paymentConfirmModal').modal('show');
        });

        const paymentMethods = <?= json_encode($paymentMethods ?? []) ?>;

        $(document).on('click', '.show-payment-instructions', function() {
            const num = $(this).data('number');
            const total = $(this).data('total');
            
            let methodsHtml = '';
            paymentMethods.forEach((method, idx) => {
                let details = '';
                let iconName = 'landmark';
                
                if (method.code === 'QRIS') {
                    iconName = 'qr-code';
                    details = `
                        <div class="text-center my-3 bg-white p-2 rounded border d-inline-block w-100">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(method.account_number)}" alt="QRIS QR Code" class="img-fluid" style="width:180px;height:180px;">
                            <div class="small fw-bold text-dark mt-2">${method.account_name}</div>
                            <div class="small text-muted font-monospace">${method.account_number}</div>
                        </div>
                    `;
                } else if (method.code === 'BANK_TRANSFER') {
                    iconName = 'landmark';
                    details = `
                        <div class="bg-light p-2 rounded border mb-2">
                            <span class="small text-muted d-block">Tujuan Transfer:</span>
                            <span class="fw-bold text-dark">${method.account_number}</span>
                            <span class="small text-muted d-block mt-1">Atas Nama:</span>
                            <span class="fw-bold text-dark">${method.account_name}</span>
                        </div>
                    `;
                } else if (method.code === 'CASH') {
                    iconName = 'banknote';
                }
                
                const formattedInstructions = method.instructions.replace(/\n/g, '<br>');

                methodsHtml += `
                    <div class="accordion-item mb-2 border rounded">
                        <h2 class="accordion-header" id="heading-${idx}">
                            <button class="accordion-button collapsed py-2 px-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${idx}" aria-expanded="false" style="font-size:0.85rem;">
                                <i data-lucide="${iconName}" class="me-2" style="width:16px;height:16px;"></i> ${method.name}
                            </button>
                        </h2>
                        <div id="collapse-${idx}" class="accordion-collapse collapse" data-bs-parent="#paymentMethodsAccordion">
                            <div class="accordion-body py-3 px-3 bg-light bg-opacity-10 small text-start">
                                ${details}
                                <div class="mb-2 text-dark" style="line-height:1.4;">${formattedInstructions}</div>
                                <p class="mb-0 text-muted small border-top pt-2 mt-2">${method.description}</p>
                            </div>
                        </div>
                    </div>
                `;
            });

            Swal.fire({
                title: 'Instruksi Pembayaran',
                html: `
                    <div class="text-start mb-3">
                        <p class="small text-muted mb-3">Pilih salah satu metode pembayaran untuk Invoice <strong>${num}</strong> sebesar <strong>${total}</strong>:</p>
                        <div class="accordion" id="paymentMethodsAccordion">
                            ${methodsHtml}
                        </div>
                        <div class="mt-3 text-center">
                            <span class="small text-muted">Butuh bantuan? Hubungi panitia via WhatsApp: <a href="https://wa.me/${encodeURIComponent('<?= esc($footerWhatsapp) ?>')}" target="_blank" class="fw-bold text-primary">Chat WhatsApp</a></span>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#6366f1',
                didOpen: () => {
                    if (typeof lucide !== 'undefined') {
                        try { lucide.createIcons(); } catch (e) {}
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
