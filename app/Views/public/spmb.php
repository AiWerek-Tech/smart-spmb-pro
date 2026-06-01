<?php
/**
 * @var array $gelombang
 * @var array $jalurs
 * @var array $alur
 * @var array $faqs
 */
?>
<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Header Section -->
<div class="sp-page-header">
    <div class="container animate-fade-up">
        <h1 class="fw-bold">
            <i data-lucide="calendar-range" style="width:28px;height:28px;" class="me-2"></i>
            Informasi SPMB
        </h1>
        <p class="opacity-90">Jadwal, persyaratan, dan alur pendaftaran</p>
    </div>
</div>

<!-- Jadwal Section -->
<section class="sp-section">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Jadwal Pendaftaran</h2>
        <div class="row g-4">
            <?php if (!empty($gelombang)): ?>
                <?php foreach ($gelombang as $index => $gel): ?>
                    <div class="col-md-6 animate-fade-up delay-<?= ($index % 2) + 1 ?>">
                        <div class="glass-panel rounded-4 overflow-hidden h-100 hover-lift border-0 shadow-sm">
                            <div class="sp-gelombang-card-header bg-primary bg-opacity-10 py-3 px-4 border-bottom border-primary border-opacity-10">
                                <h5 class="fw-bold mb-0 text-primary">
                                    <i data-lucide="layers" style="width:20px;height:20px;" class="me-2"></i>
                                    Gelombang <?= $index + 1 ?>
                                </h5>
                            </div>
                            <div class="p-4">
                                <div class="mb-4">
                                    <label class="small fw-bold text-muted text-uppercase letter-spacing-05 mb-2 d-block">Periode Pendaftaran</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                                            <i data-lucide="calendar" style="width:18px;height:18px;"></i>
                                        </div>
                                        <span class="fw-semibold"><?= date('d M Y', strtotime($gel['open_date'])) ?> — <?= date('d M Y', strtotime($gel['close_date'])) ?></span>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="small fw-bold text-muted text-uppercase letter-spacing-05 mb-2 d-block">Pengumuman Hasil</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-accent bg-opacity-10 text-accent p-2 rounded-3" style="color: var(--sp-accent) !important;">
                                            <i data-lucide="megaphone" style="width:18px;height:18px;"></i>
                                        </div>
                                        <span class="fw-semibold"><?= date('d M Y', strtotime($gel['announcement_date'])) ?></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-light">
                                    <span class="small fw-bold text-muted">Status</span>
                                    <?php $isOpen = strtotime($gel['close_date']) >= strtotime(date('Y-m-d')); ?>
                                    <span class="badge rounded-pill px-3 py-2 <?= $isOpen ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' ?>">
                                        <i data-lucide="<?= $isOpen ? 'check-circle' : 'clock' ?>" style="width:14px;height:14px;" class="me-1"></i>
                                        <?= $isOpen ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 animate-fade-up">
                    <?= view('layouts/_empty_state', [
                        'emptyIcon' => 'calendar-x',
                        'emptyTitle' => 'Jadwal Belum Tersedia',
                        'emptyMessage' => 'Jadwal gelombang pendaftaran belum dipublikasikan. Silakan cek kembali nanti.'
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Persyaratan Section -->
<section class="sp-section-alt">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Persyaratan per Jalur</h2>
        <div class="accordion sp-accordion animate-fade-up delay-1" id="jalurAccordion">
            <?php if (!empty($jalurs)): ?>
                <?php foreach ($jalurs as $index => $jalur): ?>
                    <div class="accordion-item border-0 mb-3 rounded-4 overflow-hidden shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold py-3 px-4 <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#jalur<?= $index ?>">
                                <i data-lucide="git-fork" style="width:18px;height:18px;" class="me-2 opacity-50"></i>
                                <?= esc($jalur['name']) ?>
                            </button>
                        </h2>
                        <div id="jalur<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#jalurAccordion">
                            <div class="accordion-body p-4 bg-white">
                                <p class="text-muted mb-4"><?= nl2br(esc($jalur['description'] ?? '')) ?></p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light">
                                            <i data-lucide="check-circle" class="text-success mt-1" style="width:18px;height:18px;"></i>
                                            <div>
                                                <div class="fw-bold small">Akademik</div>
                                                <div class="small text-muted">Nilai rapor & ijazah sesuai standar</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light">
                                            <i data-lucide="check-circle" class="text-success mt-1" style="width:18px;height:18px;"></i>
                                            <div>
                                                <div class="fw-bold small">Administratif</div>
                                                <div class="small text-muted">Dokumen identitas & berkas sekolah asal</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?= view('layouts/_empty_state', [
                    'emptyIcon' => 'git-fork',
                    'emptyTitle' => 'Data Jalur Belum Tersedia',
                    'emptyMessage' => 'Informasi jalur pendaftaran belum dipublikasikan.'
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Alur Pendaftaran Section -->
<section class="sp-section">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Alur Pendaftaran</h2>
        <div class="row g-4">
            <?php if (!empty($alur)): ?>
                <?php foreach ($alur as $index => $step): ?>
                    <div class="col-lg-3 col-md-6 animate-fade-up delay-<?= ($index % 4) + 1 ?>">
                        <div class="sp-flow-step text-center position-relative">
                            <div class="sp-flow-circle mb-4 bg-primary bg-opacity-10 text-primary mx-auto shadow-sm" style="width: 70px; height: 70px; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 2px solid var(--sp-primary);">
                                <?= $step['step'] ?>
                            </div>
                            <h5 class="fw-bold mb-3"><?= esc($step['title']) ?></h5>
                            <p class="text-muted small px-3"><?= esc($step['desc']) ?></p>
                            
                            <?php if ($index < count($alur) - 1): ?>
                                <div class="d-none d-lg-block position-absolute top-0 start-100 translate-middle-y mt-5 opacity-25" style="z-index: -1; width: 100%;">
                                    <i data-lucide="chevron-right" style="width:32px;height:32px;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Biaya Pendaftaran Section -->
<section class="sp-section-alt" id="biaya">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Informasi Biaya Pendidikan</h2>
        <div class="row g-4 justify-content-center">
            <?php if (!empty($fees)): ?>
                <?php foreach ($fees as $index => $fee): ?>
                    <div class="col-lg-4 col-md-6 animate-fade-up delay-<?= ($index % 3) + 1 ?>">
                        <div class="glass-panel p-4 p-lg-5 rounded-4 h-100 hover-lift border-0 shadow-sm d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                                    <i data-lucide="<?= $fee['icon'] ?>" style="width:24px;height:24px;"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;"><?= esc($fee['name']) ?></h5>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill mt-1" style="font-size: 0.65rem;"><?= esc($fee['period']) ?></span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="display-6 fw-bold text-primary"><?= esc($fee['amount']) ?></div>
                            </div>
                            <p class="text-muted small mb-0" style="line-height: 1.7;"><?= esc($fee['desc']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="sp-section">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Pertanyaan Umum (FAQ)</h2>
        <div class="accordion sp-accordion animate-fade-up delay-1" id="faqAccordion">
            <?php if (!empty($faqs)): ?>
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item border-0 mb-3 rounded-4 overflow-hidden shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold py-3 px-4 <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $index ?>">
                                <i data-lucide="help-circle" style="width:18px;height:18px;" class="me-2 opacity-50 text-primary"></i>
                                <?= esc($faq['question']) ?>
                            </button>
                        </h2>
                        <div id="faq<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body p-4 bg-white text-muted" style="line-height: 1.8;">
                                <?= nl2br(esc($faq['answer'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?= view('layouts/_empty_state', [
                    'emptyIcon' => 'help-circle',
                    'emptyTitle' => 'FAQ Belum Tersedia',
                    'emptyMessage' => 'Pertanyaan umum belum dipublikasikan.'
                ]) ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="sp-cta-section py-5">
    <div class="container text-center animate-fade-up">
        <h2 class="fw-bold mb-4">Siap untuk bergabung bersama kami?</h2>
        <p class="opacity-90 mb-5 mx-auto" style="max-width: 600px;">Jangan lewatkan kesempatan emas untuk mendapatkan pendidikan terbaik. Daftar sekarang melalui sistem online kami!</p>
        <a href="<?= base_url('auth/register') ?>" class="btn-cta-primary shadow-lg">
            <i data-lucide="user-plus" style="width:20px;height:20px;"></i>
            Mulai Pendaftaran
        </a>
    </div>
</section>

<?= $this->endSection() ?>
