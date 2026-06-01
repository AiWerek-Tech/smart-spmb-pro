<?php
/**
 * @var string $search
 * @var array $result
 * @var string $message
 * @var array $announcements
 * @var \CodeIgniter\Pager\Pager $pager
 */
?>
<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Header Section -->
<div class="sp-page-header">
    <div class="container animate-fade-up">
        <h1 class="fw-bold">
            <i data-lucide="megaphone" style="width:28px;height:28px;" class="me-2"></i>
            Pengumuman
        </h1>
        <p class="opacity-90">Informasi penting dan pengumuman terbaru</p>
    </div>
</div>

<!-- Check Result Section -->
<section class="sp-section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 animate-fade-up">
                <div class="glass-panel rounded-4 overflow-hidden border-0 shadow-sm">
                    <div class="card-header bg-primary bg-opacity-10 p-4 border-bottom border-primary border-opacity-10">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i data-lucide="search" style="width:20px;height:20px;" class="me-2"></i>
                            Cek Hasil Seleksi
                        </h5>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <p class="text-muted mb-4">Masukkan nama lengkap atau nomor pendaftaran Anda untuk melihat status seleksi terbaru.</p>
                        <form method="POST" action="<?= base_url('/pengumuman/cek-hasil') ?>" class="mb-4">
                            <?= csrf_field() ?>
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-end-0 text-muted ps-4">
                                    <i data-lucide="user" style="width:20px;height:20px;"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control border-start-0 ps-2 fw-medium" 
                                    id="search" 
                                    name="search" 
                                    placeholder="Nama atau Nomor Pendaftaran" 
                                    value="<?= esc($search ?? '') ?>"
                                    required
                                    style="font-size: 1rem;"
                                >
                                <button class="btn btn-primary px-4 fw-bold" type="submit">
                                    Cari Hasil
                                </button>
                            </div>
                        </form>

                        <!-- Search Result -->
                        <?php if (!empty($search)): ?>
                            <div class="animate-fade-up">
                                <?php if (!empty($result)): ?>
                                    <div class="alert bg-success bg-opacity-10 border-success border-opacity-25 text-dark p-4 rounded-4" role="alert">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <div class="bg-success text-white p-2 rounded-circle">
                                                <i data-lucide="check" style="width:20px;height:20px;"></i>
                                            </div>
                                            <h5 class="mb-0 fw-bold text-success">Data Ditemukan</h5>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label class="small fw-bold text-muted text-uppercase mb-1 d-block">Nama Lengkap</label>
                                                <div class="fw-bold"><?= esc($result['full_name'] ?? '') ?></div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="small fw-bold text-muted text-uppercase mb-1 d-block">Nomor Pendaftaran</label>
                                                <div class="fw-bold text-primary"><?= esc($result['registration_number'] ?? '') ?></div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="small fw-bold text-muted text-uppercase mb-1 d-block">Jalur Pendaftaran</label>
                                                <div class="fw-bold"><?= esc($result['jalur_name'] ?? 'Reguler') ?></div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="small fw-bold text-muted text-uppercase mb-1 d-block">Status Seleksi</label>
                                                <?php $status = $result['selection_status'] ?? 'pending'; ?>
                                                <span class="badge rounded-pill px-3 py-2 <?= $status === 'accepted' ? 'bg-success' : ($status === 'rejected' ? 'bg-danger' : 'bg-warning') ?>">
                                                    <?= match($status) {
                                                        'accepted' => 'DITERIMA',
                                                        'rejected' => 'TIDAK DITERIMA',
                                                        default => 'DALAM PROSES'
                                                    } ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert bg-warning bg-opacity-10 border-warning border-opacity-25 text-dark p-4 rounded-4" role="alert">
                                        <div class="d-flex align-items-center gap-2 text-warning mb-2">
                                            <i data-lucide="alert-circle" style="width:20px;height:20px;"></i>
                                            <span class="fw-bold">Data Tidak Ditemukan</span>
                                        </div>
                                        <p class="mb-0 small text-muted"><?= esc($message ?? 'Maaf, data pendaftaran tidak ditemukan. Pastikan nama atau nomor yang Anda masukkan benar.') ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Announcements Section -->
<section class="sp-section-alt">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Daftar Pengumuman</h2>

        <?php if (!empty($announcements)): ?>
            <div class="row g-4">
                <?php foreach ($announcements as $index => $announcement): ?>
                    <div class="col-md-6 animate-fade-up delay-<?= ($index % 2) + 1 ?>">
                        <div class="glass-panel p-4 rounded-4 h-100 hover-lift border-0 shadow-sm d-flex flex-column">
                            <div class="mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 small fw-bold mb-3">
                                    <i data-lucide="calendar" style="width:14px;height:14px;" class="me-1"></i>
                                    <?= date('d M Y', strtotime($announcement['published_at'])) ?>
                                </span>
                                <h5 class="fw-bold mb-3 text-dark"><?= esc($announcement['title'] ?? '') ?></h5>
                                <p class="text-muted small mb-4" style="line-height:1.7;">
                                    <?= character_limiter(strip_tags($announcement['content'] ?? ''), 150) ?>
                                </p>
                            </div>
                            <div class="mt-auto">
                                <button type="button" class="btn btn-link text-primary p-0 fw-bold text-decoration-none d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#announcementModal<?= $announcement['id'] ?>">
                                    Baca Selengkapnya <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Announcement Modal -->
                    <div class="modal fade" id="announcementModal<?= $announcement['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                                <div class="modal-header bg-primary py-3 px-4 border-0">
                                    <h5 class="modal-title text-white fw-bold mb-0">
                                        <i data-lucide="megaphone" style="width:20px;height:20px;" class="me-2"></i>
                                        Detail Pengumuman
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4 p-lg-5">
                                    <div class="mb-4">
                                        <h3 class="fw-bold text-dark mb-2"><?= esc($announcement['title'] ?? '') ?></h3>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i data-lucide="calendar" style="width:14px;height:14px;" class="me-1"></i>
                                            Dipublikasikan: <?= date('d M Y H:i', strtotime($announcement['published_at'])) ?>
                                        </div>
                                    </div>
                                    <div class="announcement-content text-muted" style="font-size:1rem; line-height:1.8; white-space:pre-wrap;">
                                        <?= esc($announcement['content'] ?? '') ?>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-0 py-3 px-4">
                                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pager)): ?>
                <nav aria-label="Pagination" class="mt-5 animate-fade-up">
                    <?= $pager->links() ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="animate-fade-up">
                <?= view('layouts/_empty_state', [
                    'emptyIcon' => 'megaphone',
                    'emptyTitle' => 'Belum Ada Pengumuman',
                    'emptyMessage' => 'Belum ada pengumuman yang tersedia saat ini. Silakan cek kembali nanti.'
                ]) ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
