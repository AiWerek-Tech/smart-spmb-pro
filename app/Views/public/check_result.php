<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Header Section -->
<div class="sp-page-header">
    <div class="container">
        <h1>
            <i data-lucide="search" style="width:28px;height:28px;"></i>
            Cek Hasil Seleksi
        </h1>
        <p>Periksa status pendaftaran dan hasil seleksi Anda</p>
    </div>
</div>

<!-- Check Result Section -->
<section class="sp-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-header" style="background:linear-gradient(135deg,var(--sp-primary),var(--sp-accent));color:#fff;border:none;padding:var(--sp-space-lg);">
                        <h5 class="card-title mb-0" style="color:#fff!important;">
                            <i data-lucide="search" style="width:18px;height:18px;" class="me-1"></i>
                            Pencarian
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="<?= base_url('/pengumuman/cek-hasil') ?>" id="searchForm">
                            <?= csrf_field() ?>
                            
                            <p class="text-muted mb-3" style="font-size:0.9rem;">
                                Masukkan nama lengkap atau nomor pendaftaran Anda untuk melihat status seleksi.
                            </p>

                            <div class="input-group mb-3">
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg" 
                                    id="search" 
                                    name="search" 
                                    placeholder="Contoh: Ahmad Wijaya atau SPMB-2026-0001" 
                                    value="<?= esc($search ?? '') ?>"
                                    required
                                >
                                <button class="btn btn-primary btn-lg" type="submit">
                                    <i data-lucide="search" style="width:16px;height:16px;" class="me-1"></i> Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search Results -->
                <?php if (!empty($search)): ?>
                    <?php if (!empty($result)): ?>
                        <!-- Success Result -->
                        <div class="card mb-4">
                            <div class="card-header" style="background:var(--sp-success);color:#fff;border:none;padding:var(--sp-space-md) var(--sp-space-lg);">
                                <h5 class="card-title mb-0" style="color:#fff!important;">
                                    <i data-lucide="check-circle" style="width:18px;height:18px;" class="me-1"></i>
                                    Data Ditemukan
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <!-- Student Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;font-weight:700;">NAMA LENGKAP</label>
                                        <h5><?= esc($result['full_name']) ?></h5>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;font-weight:700;">NOMOR PENDAFTARAN</label>
                                        <h5><?= esc($result['registration_number']) ?></h5>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;font-weight:700;">JALUR PENDAFTARAN</label>
                                        <h5><?= esc($result['jalur_name'] ?? $result['jalur_id'] ?? '-') ?></h5>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;font-weight:700;">TANGGAL PENDAFTARAN</label>
                                        <h5><?= date('d M Y', strtotime($result['created_at'])) ?></h5>
                                    </div>
                                </div>

                                <hr>

                                <!-- Selection Status -->
                                <div class="mt-4">
                                    <label class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;font-weight:700;">STATUS SELEKSI</label>
                                    <h4 class="mt-2">
                                        <span class="badge p-3" style="background-color:<?= match($result['selection_status']) {
                                            'accepted' => 'var(--sp-success)',
                                            'rejected' => 'var(--sp-danger)',
                                            default => 'var(--sp-warning)'
                                        } ?>;color:#fff;font-size:1rem;border-radius:var(--sp-radius-md);">
                                            <i data-lucide="<?= match($result['selection_status']) {
                                                'accepted' => 'check-circle',
                                                'rejected' => 'x-circle',
                                                default => 'clock'
                                            } ?>" style="width:18px;height:18px;" class="me-1"></i>
                                            <?= match($result['selection_status']) {
                                                'accepted' => 'DITERIMA 🎉',
                                                'rejected' => 'TIDAK DITERIMA',
                                                default => 'DALAM PROSES'
                                            } ?>
                                        </span>
                                    </h4>
                                </div>

                                <?php if ($result['selection_status'] === 'rejected' && !empty($result['rejection_reason'])): ?>
                                    <div class="alert alert-warning mt-3" role="alert">
                                        <strong>Alasan:</strong> <?= esc($result['rejection_reason']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-4">
                                    <p class="text-muted" style="font-size:0.8rem;">
                                        <i data-lucide="info" style="width:14px;height:14px;" class="me-1"></i>
                                        Untuk informasi lebih lanjut, silakan hubungi panitia SPMB kami melalui kontak yang tersedia.
                                    </p>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Not Found / Result Not Published -->
                        <div class="alert alert-<?= $message === 'Pengumuman belum tersedia' ? 'info' : 'warning' ?> p-4" role="alert">
                            <h5>
                                <i data-lucide="<?= $message === 'Pengumuman belum tersedia' ? 'calendar' : 'alert-triangle' ?>" style="width:18px;height:18px;" class="me-1"></i>
                                <?= match($message) {
                                    'Data tidak ditemukan' => 'Data Tidak Ditemukan',
                                    'Pengumuman belum tersedia' => 'Pengumuman Belum Tersedia',
                                    default => 'Informasi'
                                } ?>
                            </h5>
                            <p class="mb-0">
                                <?= match($message) {
                                    'Data tidak ditemukan' => 'Maaf, data pendaftaran dengan informasi yang Anda masukkan tidak ditemukan. Silakan periksa kembali nomor pendaftaran atau nama Anda.',
                                    'Pengumuman belum tersedia' => 'Pengumuman hasil seleksi belum dipublikasikan. Silakan kembali lagi nanti untuk melihat status seleksi Anda.',
                                    default => 'Mohon maaf, terjadi kesalahan. Silakan coba kembali.'
                                } ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Initial State -->
                    <?= view('layouts/_empty_state', [
                        'emptyIcon' => 'search',
                        'emptyTitle' => 'Belum Ada Pencarian',
                        'emptyMessage' => 'Gunakan form di atas untuk mencari status pendaftaran Anda.'
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Help Section -->
<section class="sp-section-alt">
    <div class="container">
        <h2 class="sp-section-title">Panduan Pencarian</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="sp-hours-card">
                    <div class="sp-hours-card-icon">
                        <i data-lucide="user" style="width:32px;height:32px;"></i>
                    </div>
                    <h5>Cari dengan Nama</h5>
                    <p>Masukkan nama lengkap Anda seperti yang Anda daftarkan</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="sp-hours-card">
                    <div class="sp-hours-card-icon">
                        <i data-lucide="ticket" style="width:32px;height:32px;"></i>
                    </div>
                    <h5>Cari dengan Nomor</h5>
                    <p>Masukkan nomor pendaftaran Anda (format: SPMB-YYYY-NNNN)</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="sp-hours-card">
                    <div class="sp-hours-card-icon">
                        <i data-lucide="headset" style="width:32px;height:32px;"></i>
                    </div>
                    <h5>Hubungi Kami</h5>
                    <p>Jika tidak menemukan data, hubungi panitia SPMB kami</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
