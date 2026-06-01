<?php
/**
 * @var string $schoolName
 * @var string $schoolTagline
 * @var string $schoolProfile
 * @var string $academicYear
 * @var int $totalRegistrations
 * @var array $jalurs
 * @var bool $noActiveJalurs
 * @var string $ctaUrl
 * @var string $ctaText
 */
?>
<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Section 2: Hero Banner -->
<section class="sp-hero-banner animate-up">
    <div class="container">
        <?php if (!empty($banners)): ?>
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($banners as $index => $banner): ?>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner rounded-5 overflow-hidden shadow-lg">
                    <?php foreach ($banners as $index => $banner): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" data-bs-interval="5000">
                            <div class="sp-hero-card">
                                <div class="sp-hero-content">
                                    <div class="sp-hero-badge glass-effect">
                                        <i data-lucide="sparkles" style="width:14px;height:14px;"></i>
                                        <span>Pendaftaran <?= esc($academicYear) ?> Dibuka</span>
                                    </div>
                                    <h1 class="sp-hero-title"><?= esc($banner['title']) ?></h1>
                                    <p class="sp-hero-subtitle"><?= esc($banner['subtitle']) ?></p>
                                    <div class="sp-hero-actions">
                                        <a href="<?= $banner['cta_url'] ? (strpos($banner['cta_url'], 'http') === 0 ? $banner['cta_url'] : base_url($banner['cta_url'])) : esc($ctaUrl) ?>" class="btn btn-light btn-lg rounded-pill px-5 fw-800 text-primary btn-pulse">
                                            <?= $banner['cta_text'] ?: esc($ctaText) ?>
                                        </a>
                                        <a href="#timeline" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-700">
                                            Lihat Jadwal
                                        </a>
                                    </div>
                                    <div class="sp-hero-trust-badges mt-4">
                                        <div class="sp-hero-trust-item">
                                            <i data-lucide="shield-check" class="sp-hero-trust-icon"></i>
                                            <span>Aman & Terpercaya</span>
                                        </div>
                                        <div class="sp-hero-trust-item">
                                            <i data-lucide="zap" class="sp-hero-trust-icon"></i>
                                            <span>Mudah Diakses</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="sp-hero-visual">
                                    <div class="sp-hero-mockup">
                                        <div class="sp-hero-mockup-img">
                                            <?php $bannerImg = (strpos($banner['image'], 'http') === 0) ? $banner['image'] : base_url($banner['image']); ?>
                                            <img src="<?= esc($bannerImg) ?>" class="w-100 h-100 object-fit-cover" alt="Banner Image" onerror="this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Default Fallback Banner -->
            <div class="sp-hero-card">
                <div class="sp-hero-content">
                    <div class="sp-hero-badge glass-effect">
                        <i data-lucide="sparkles" style="width:14px;height:14px;"></i>
                        <span>Pendaftaran <?= esc($academicYear) ?> Dibuka</span>
                    </div>
                    <h1 class="sp-hero-title">Wujudkan Masa Depan Cemerlang di <?= esc($schoolName) ?></h1>
                    <p class="sp-hero-subtitle">Portal pendaftaran resmi dengan sistem yang transparan, akuntabel, dan terintegrasi langsung dengan data nasional.</p>
                    <div class="sp-hero-actions">
                        <a href="<?= esc($ctaUrl) ?>" class="btn btn-light btn-lg rounded-pill px-5 fw-800 text-primary btn-pulse">
                            <?= esc($ctaText) ?>
                        </a>
                        <a href="#timeline" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-700">
                            Lihat Jadwal
                        </a>
                    </div>
                    <div class="sp-hero-trust-badges">
                        <div class="sp-hero-trust-item">
                            <i data-lucide="shield-check" class="sp-hero-trust-icon"></i>
                            <span>Aman & Terpercaya</span>
                        </div>
                        <div class="sp-hero-trust-item">
                            <i data-lucide="database" class="sp-hero-trust-icon"></i>
                            <span>Data Terintegrasi</span>
                        </div>
                    </div>
                </div>
                <div class="sp-hero-visual">
                    <div class="sp-hero-mockup">
                        <div class="sp-hero-mockup-img">
                             <div class="sp-mockup-ui">
                                <div class="sp-mockup-header">
                                    <div class="sp-mockup-dot" style="background: #ff5f56;"></div>
                                    <div class="sp-mockup-dot" style="background: #ffbd2e;"></div>
                                    <div class="sp-mockup-dot" style="background: #27c93f;"></div>
                                </div>
                                <div class="sp-mockup-bg-image" style="background-image: url('<?= base_url('assets/img/gallery-placeholder.svg') ?>');"></div>
                                <div class="sp-mockup-body">
                                    <div class="sp-mockup-item" style="width: 100%; background: #f1f5f9;"></div>
                                    <div class="sp-mockup-item" style="width: 85%; background: #f1f5f9;"></div>
                                    <div class="sp-mockup-stats mt-4">
                                        <div class="sp-mockup-stat-card"><div style="width: 70%; height: 8px; background: var(--sp-primary); border-radius: 4px;"></div></div>
                                        <div class="sp-mockup-stat-card"><div style="width: 70%; height: 8px; background: var(--sp-primary); border-radius: 4px;"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Section 2.1: Trust Bar -->
<section class="sp-trust-bar">
    <div class="container">
        <div class="sp-trust-container">
            <?php 
            // Use the first 4 stats for the trust bar
            $highlightStats = array_slice($stats, 0, 4);
            foreach ($highlightStats as $stat): 
            ?>
            <div class="sp-trust-item">
                <i data-lucide="<?= $stat['icon'] ?: 'award' ?>" class="sp-trust-icon"></i>
                <div class="sp-trust-text"><?= esc($stat['value']) ?> <?= esc($stat['label']) ?><br><span class="fw-normal opacity-75 small"><?= strpos($stat['label'], 'Akreditasi') !== false ? 'Sangat Memuaskan' : 'Kualitas Terjamin' ?></span></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 3: Quick Actions -->
<section class="sp-quick-actions" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header text-center">
            <h2 class="sp-section-title-sm">Layanan Utama</h2>
            <p class="text-muted small">Portal layanan terintegrasi untuk kemudahan pendaftaran Anda.</p>
        </div>
        
        <!-- Primary Actions -->
        <div class="sp-primary-actions">
            <a href="<?= esc($ctaUrl) ?>" class="sp-primary-card">
                <div class="sp-primary-icon sp-icon-theme">
                    <i data-lucide="user-plus"></i>
                </div>
                <div class="sp-primary-info">
                    <h3>Pendaftaran</h3>
                    <p>Mulai pendaftaran online.</p>
                </div>
            </a>
            <a href="<?= base_url('/pengumuman') ?>" class="sp-primary-card">
                <div class="sp-primary-icon sp-icon-success">
                    <i data-lucide="search"></i>
                </div>
                <div class="sp-primary-info">
                    <h3>Cek Status</h3>
                    <p>Pantau hasil seleksi.</p>
                </div>
            </a>
            <a href="#timeline" class="sp-primary-card">
                <div class="sp-primary-icon sp-icon-accent">
                    <i data-lucide="calendar"></i>
                </div>
                <div class="sp-primary-info">
                    <h3>Jadwal</h3>
                    <p>Agenda & batas waktu.</p>
                </div>
            </a>
        </div>

        <!-- Secondary Actions -->
        <div class="sp-secondary-actions">
            <a href="<?= base_url('/spmb') ?>" class="sp-secondary-item">
                <i data-lucide="file-text" class="sp-secondary-icon" style="width:18px;"></i>
                <span class="sp-secondary-label">Persyaratan</span>
            </a>
            <a href="<?= base_url('/profil') ?>" class="sp-secondary-item">
                <i data-lucide="school" class="sp-secondary-icon" style="width:18px;"></i>
                <span class="sp-secondary-label">Profil Sekolah</span>
            </a>
            <a href="#faq" class="sp-secondary-item">
                <i data-lucide="help-circle" class="sp-secondary-icon" style="width:18px;"></i>
                <span class="sp-secondary-label">Bantuan (FAQ)</span>
            </a>
            <a href="<?= base_url('/kontak') ?>" class="sp-secondary-item">
                <i data-lucide="phone" class="sp-secondary-icon" style="width:18px;"></i>
                <span class="sp-secondary-label">Hubungi Kami</span>
            </a>
        </div>
    </div>
</section>

<!-- Section 5: Registration Timeline -->
<section class="sp-timeline-section bg-light" id="timeline" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header text-center">
            <h2 class="sp-section-title-sm">Alur Pendaftaran</h2>
            <p class="text-muted small">Ikuti langkah-langkah mudah pendaftaran di bawah ini.</p>
        </div>
        <div class="sp-timeline-container">
            <div class="sp-timeline-item active">
                <div class="sp-timeline-dot"><i data-lucide="user-plus" style="width:18px;"></i></div>
                <span class="sp-timeline-label">Daftar Akun</span>
            </div>
            <div class="sp-timeline-item">
                <div class="sp-timeline-dot"><i data-lucide="file-up" style="width:18px;"></i></div>
                <span class="sp-timeline-label">Lengkapi Data</span>
            </div>
            <div class="sp-timeline-item">
                <div class="sp-timeline-dot"><i data-lucide="shield-check" style="width:18px;"></i></div>
                <span class="sp-timeline-label">Verifikasi</span>
            </div>
            <div class="sp-timeline-item">
                <div class="sp-timeline-dot"><i data-lucide="users" style="width:18px;"></i></div>
                <span class="sp-timeline-label">Seleksi</span>
            </div>
            <div class="sp-timeline-item">
                <div class="sp-timeline-dot"><i data-lucide="award" style="width:18px;"></i></div>
                <span class="sp-timeline-label">Pengumuman</span>
            </div>
        </div>
    </div>
</section>

<!-- Section 5.5: Jalur Penerimaan -->
<?php if (isset($noActiveJalurs) && !$noActiveJalurs && isset($jalurs)): ?>
<section class="sp-jalur-section bg-white" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header mb-5">
            <h2 class="sp-section-title-sm">Pilih Jalur Pendaftaran</h2>
            <p class="text-muted small">Tersedia berbagai pilihan jalur masuk yang sesuai dengan potensi Anda.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($jalurs as $jalur): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="jalur-card animate-up">
                        <div class="jalur-icon-box" style="background: var(--sp-primary-light); color: var(--sp-primary);">
                            <i data-lucide="<?= ($jalur['name'] == 'Zonasi') ? 'map-pin' : (($jalur['name'] == 'Prestasi') ? 'award' : 'graduation-cap') ?>"></i>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="jalur-title mb-0"><?= esc($jalur['name'] ?? '') ?></h3>
                            <span class="badge rounded-pill <?= ($jalur['is_active'] ?? false) ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 <?= ($jalur['is_active'] ?? false) ? 'text-success' : 'text-danger' ?> px-3 py-2" style="font-size: 0.65rem; font-weight: 800;">
                                <?= ($jalur['is_active'] ?? false) ? 'DIBUKA' : 'DITUTUP' ?>
                            </span>
                        </div>
                        
                        <p class="jalur-desc">
                            <?= esc($jalur['description'] ?? 'Pendaftaran melalui jalur ' . ($jalur['name'] ?? '') . ' sesuai dengan ketentuan yang berlaku.') ?>
                        </p>

                        <div class="jalur-progress-wrapper mt-auto">
                            <div class="jalur-stats">
                                <span>Kapasitas Terisi</span>
                                <span class="text-primary"><?= $jalur['percentage_filled'] ?? 0 ?>%</span>
                            </div>
                            <div class="progress mb-4" style="height:10px; border-radius:20px; background-color: #F1F5F9; overflow: visible;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width:<?= $jalur['percentage_filled'] ?? 0 ?>%; background: var(--sp-gradient-brand-horizontal); border-radius:20px; position: relative;">
                                    <div class="progress-pulse"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between small fw-700 mb-4">
                                <span class="text-muted">Kuota: <?= $jalur['quota'] ?? 0 ?></span>
                                <span class="text-primary">Sisa: <?= $jalur['remaining_quota'] ?? 0 ?></span>
                            </div>
                            <a href="<?= base_url('auth/register') ?>" class="btn-pilih-jalur <?= !($jalur['is_active'] ?? false) ? 'disabled' : '' ?>">
                                <?= ($jalur['is_active'] ?? false) ? 'Daftar Jalur Ini' : 'Pendaftaran Ditutup' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section 7: Statistics -->
<section class="sp-stats-section-new" id="stats" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header text-center mb-5">
            <h2 class="sp-section-title-sm"><?= esc($schoolName) ?> dalam Angka</h2>
            <p class="text-muted small">Transparansi data pendaftaran untuk kepercayaan Anda.</p>
        </div>
        <div class="sp-stats-row">
            <?php foreach ($stats as $index => $stat): ?>
            <div class="stat-item animate-up" style="animation-delay: <?= $index * 0.1 ?>s;">
                <div class="stat-icon"><i data-lucide="<?= $stat['icon'] ?: 'activity' ?>"></i></div>
                <div class="stat-info">
                    <span class="stat-number"><?= esc($stat['value']) ?></span>
                    <span class="stat-label"><?= esc($stat['label']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 7.5: School Gallery -->
<section class="sp-gallery-section bg-light" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header text-center mb-5">
            <h2 class="sp-section-title-sm">Lingkungan & Fasilitas</h2>
            <p class="text-muted small">Suasana belajar yang nyaman dan kondusif untuk tumbuh kembang siswa.</p>
        </div>
        <div class="row g-4">
            <?php if (!empty($gallery)): ?>
                <?php foreach ($gallery as $index => $item): ?>
                <div class="col-6 col-md-4">
                    <div class="sp-gallery-item animate-up" style="animation-delay: <?= $index * 0.1 ?>s;">
                        <div class="sp-gallery-img-wrapper">
                            <img src="<?= (strpos($item['image'], 'http') === 0) ? esc($item['image']) : base_url($item['image']) ?>" alt="<?= esc($item['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>'">
                        </div>
                        <?php if ($item['title']): ?>
                        <div class="sp-gallery-overlay">
                            <span class="sp-gallery-caption"><?= esc($item['title']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback Static Gallery if Empty -->
                <?php
                $fallbackGallery = [
                    ['title' => 'Gedung Utama', 'img' => base_url('assets/img/gallery-placeholder.svg')],
                    ['title' => 'Perpustakaan Digital', 'img' => base_url('assets/img/gallery-placeholder.svg')],
                    ['title' => 'Laboratorium Sains Modern', 'img' => base_url('assets/img/gallery-placeholder.svg')],
                ];
                foreach ($fallbackGallery as $fi => $fgItem):
                ?>
                <div class="col-6 col-md-4">
                    <div class="sp-gallery-item animate-up" style="<?= $fi > 0 ? 'animation-delay: ' . ($fi * 0.1) . 's;' : '' ?>">
                        <div class="sp-gallery-img-wrapper">
                            <img src="<?= $fgItem['img'] ?>" alt="<?= esc($fgItem['title']) ?>" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="sp-gallery-overlay"><span class="sp-gallery-caption"><?= esc($fgItem['title']) ?></span></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section 7.6: Testimonials -->
<?php if (!empty($testimonials)): ?>
<section class="sp-testimonial-section bg-white" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header text-center mb-5">
            <h2 class="sp-section-title-sm">Apa Kata Mereka?</h2>
            <p class="text-muted small">Ulasan jujur dari alumni dan orang tua siswa <?= esc($schoolName) ?>.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($testimonials as $testi): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 animate-up">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <?php $testiPhoto = !empty($testi['photo']) ? ((strpos($testi['photo'], 'http') === 0) ? $testi['photo'] : base_url($testi['photo'])) : 'https://ui-avatars.com/api/?name=' . urlencode($testi['name']); ?>
                        <img src="<?= esc($testiPhoto) ?>" class="rounded-circle border" style="width: 56px; height: 56px; object-fit: cover;" alt="User" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($testi['name']) ?>'">
                        <div>
                            <h5 class="fw-800 mb-0" style="font-size: 1rem;"><?= esc($testi['name']) ?></h5>
                            <small class="text-muted"><?= esc($testi['role']) ?></small>
                        </div>
                    </div>
                    <div class="text-warning mb-3">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i data-lucide="star" class="<?= $i <= $testi['rating'] ? 'fill-warning' : '' ?>" style="width: 14px; height: 14px; <?= $i <= $testi['rating'] ? 'fill: #F59E0B;' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; font-style: italic; line-height: 1.6;">"<?= esc($testi['content']) ?>"</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section 8: Latest News -->
<section class="sp-news-section bg-white" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header d-flex justify-content-between align-items-end">
            <div>
                <h2 class="sp-section-title-sm">Kegiatan & Berita</h2>
                <p class="text-muted small mb-0">Informasi terbaru seputar kegiatan di <?= esc($schoolName) ?>.</p>
            </div>
            <a href="<?= base_url('/pengumuman') ?>" class="sp-view-all">Lihat Semua →</a>
        </div>
        <div class="sp-news-container mt-4">
            <?php if (isset($announcements) && !empty($announcements)): ?>
                <?php foreach ($announcements as $news): ?>
                <div class="sp-news-card animate-up">
                    <div class="sp-news-image">
                        <?php $newsImg = !empty($news['image']) ? ((strpos($news['image'], 'http') === 0) ? $news['image'] : base_url($news['image'])) : base_url('assets/img/gallery-placeholder.svg'); ?>
                        <img src="<?= esc($newsImg) ?>" alt="News" onerror="this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>'">
                        <span class="sp-news-tag"><?= esc($news['tag'] ?? 'INFO') ?></span>
                    </div>
                    <div class="sp-news-body">
                        <h3 class="sp-news-title"><?= esc($news['title']) ?></h3>
                        <div class="sp-news-date">
                            <i data-lucide="calendar" style="width:12px;height:12px;"></i>
                            <span><?= date('d M Y', strtotime($news['published_at'])) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section 9: FAQ Accordion -->
<section class="sp-faq-section bg-light" id="faq" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="sp-section-header text-center mb-5">
                    <h2 class="sp-section-title-sm">Pertanyaan Sering Diajukan</h2>
                    <p class="text-muted small">Temukan jawaban cepat untuk pertanyaan umum seputar pendaftaran.</p>
                </div>
                <div class="sp-faq-list">
                    <?php if (isset($faqs) && !empty($faqs)): ?>
                        <?php foreach ($faqs as $index => $faq): ?>
                        <div class="sp-faq-item animate-up" style="animation-delay: <?= $index * 0.1 ?>s;">
                            <button class="sp-faq-button">
                                <span><?= esc($faq['question']) ?></span>
                                <i data-lucide="chevron-down" class="sp-faq-icon" style="width:20px;height:20px;"></i>
                            </button>
                            <div class="sp-faq-content">
                                <?= esc($faq['answer']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= base_url('/faq') ?>" class="text-primary fw-800 text-decoration-none small">Lihat Semua FAQ →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 10: Bottom CTA -->
<section class="sp-bottom-cta" style="padding: var(--sp-section-gap) 0; margin-bottom: 40px;">
    <div class="container">
        <div class="card border-0 rounded-5 shadow-lg overflow-hidden animate-up sp-bottom-cta-card">
            <div class="card-body p-5 text-center text-white">
                <h2 class="fw-800 h1 mb-3">Siapkan Masa Depan Putra-Putri Anda Bersama <?= esc($schoolName) ?></h2>
                <p class="opacity-90 mb-4 mx-auto" style="max-width: 600px;">Pendaftaran Tahun Ajaran <?= esc($academicYear) ?> telah dibuka. Kuota terbatas untuk setiap jalur penerimaan. Segera amankan kursi Anda.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= esc($ctaUrl) ?>" class="btn btn-light btn-lg rounded-pill px-5 fw-800 text-primary btn-pulse"><?= esc($ctaText) ?></a>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $schoolWhatsapp ?? $schoolPhone ?? '6282190822641') ?>" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-700" target="_blank" rel="noopener">Hubungi Panitia</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Intersection Observer for Scroll Animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-up').forEach(el => {
            observer.observe(el);
        });

        // 2. FAQ Toggle Logic
        document.querySelectorAll('.sp-faq-button').forEach(button => {
            button.addEventListener('click', () => {
                const item = button.parentElement;
                const content = button.nextElementSibling;
                const isActive = item.classList.contains('active');

                // Close other FAQs
                document.querySelectorAll('.sp-faq-item').forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                        const otherContent = otherItem.querySelector('.sp-faq-content');
                        otherContent.style.maxHeight = '0px';
                        otherContent.style.paddingBottom = '0px';
                    }
                });

                // Toggle current
                if (isActive) {
                    item.classList.remove('active');
                    content.style.maxHeight = '0px';
                    content.style.paddingBottom = '0px';
                } else {
                    item.classList.add('active');
                    content.style.maxHeight = content.scrollHeight + 20 + 'px';
                    content.style.paddingBottom = '20px';
                }
            });
        });

        // 3. Smooth Scroll for Anchor Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const navbarHeight = 80;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // 4. CTA Loading State (UX)
        document.querySelectorAll('.btn-pulse').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.getAttribute('href').startsWith('http') || this.getAttribute('href').startsWith('/')) {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';
                    this.style.pointerEvents = 'none';
                    this.style.opacity = '0.8';
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
