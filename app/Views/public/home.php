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
                <div class="carousel-inner">
                    <?php foreach ($banners as $index => $banner): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" data-bs-interval="5000">
                            <?php
                            $bannerImgSrc = !empty($banner['image'])
                                ? ((strpos($banner['image'], 'http') === 0) ? esc($banner['image'], 'url') : base_url(esc($banner['image'])))
                                : base_url('assets/img/gallery-placeholder.svg');
                            ?>
                            <div class="sp-hero-card" style="--sp-hero-image: url('<?= $bannerImgSrc ?>');">
                                <div class="sp-hero-content">
                                    <div class="sp-hero-badge glass-effect">
                                        <i data-lucide="sparkles" style="width:14px;height:14px;"></i>
                                        <span>Pendaftaran <?= esc($academicYear) ?> Dibuka</span>
                                    </div>
                                    <h1 class="sp-hero-title"><?= esc($schoolName) ?></h1>
                                    <p class="sp-hero-eyebrow-title"><?= esc($banner['title']) ?></p>
                                    <p class="sp-hero-subtitle"><?= esc($banner['subtitle']) ?></p>
                                    <div class="sp-hero-actions">
                                        <?php
                                        $heroCta = $banner['cta_url']
                                            ? (strpos($banner['cta_url'], 'http') === 0 ? esc($banner['cta_url'], 'url') : base_url(esc($banner['cta_url'])))
                                            : esc($ctaUrl);
                                        $heroCtaText = !empty($banner['cta_text']) ? esc($banner['cta_text']) : esc($ctaText);
                                        ?>
                                        <a href="<?= $heroCta ?>" class="btn btn-light btn-lg rounded-pill px-5 fw-800 text-primary btn-pulse">
                                            <?= $heroCtaText ?>
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
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Default Fallback Banner -->
            <div class="sp-hero-card" style="--sp-hero-image: url('<?= base_url('assets/img/gallery-placeholder.svg') ?>');">
                <div class="sp-hero-content">
                    <div class="sp-hero-badge glass-effect">
                        <i data-lucide="sparkles" style="width:14px;height:14px;"></i>
                        <span>Pendaftaran <?= esc($academicYear) ?> Dibuka</span>
                    </div>
                    <h1 class="sp-hero-title"><?= esc($schoolName) ?></h1>
                    <p class="sp-hero-eyebrow-title">Wujudkan Masa Depan Cemerlang</p>
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
                <i data-lucide="<?= esc($stat['icon'] ?: 'award') ?>" class="sp-trust-icon"></i>
                <div class="sp-trust-text"><?= esc($stat['value']) ?> <?= esc($stat['label']) ?><br><span class="fw-normal opacity-75 small"><?= esc($stat['description'] ?? 'Kualitas Terjamin') ?></span></div>
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
            <a href="<?= base_url('/hasil-seleksi') ?>" class="sp-primary-card">
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
            <a href="<?= base_url('/lingkungan-kampus') ?>" class="sp-secondary-item">
                <i data-lucide="trees" class="sp-secondary-icon" style="width:18px;"></i>
                <span class="sp-secondary-label">Lingkungan</span>
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

<!-- Section 5: SPMB Schedule -->
<section class="sp-timeline-section bg-light" id="timeline" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header text-center">
            <h2 class="sp-section-title-sm">Jadwal SPMB <?= esc($academicYear) ?></h2>
            <p class="text-muted small">Agenda resmi mulai masa pendaftaran, pengumuman, daftar ulang, hingga MPLS.</p>
        </div>
        <?php if (!empty($spmbSchedule)): ?>
            <div class="sp-schedule-strip" aria-label="Jadwal SPMB">
                <?php foreach ($spmbSchedule as $index => $item): ?>
                    <article class="sp-schedule-card <?= !empty($item['is_active']) ? 'is-live' : '' ?>">
                        <div class="sp-schedule-icon">
                            <i data-lucide="<?= esc($item['icon'] ?? 'calendar') ?>"></i>
                        </div>
                        <div class="sp-schedule-content">
                            <div class="sp-schedule-kicker"><?= $index === 0 ? 'Mulai' : 'Tahap ' . ($index + 1) ?></div>
                            <h3><?= esc($item['title']) ?></h3>
                            <p><?= esc($item['description']) ?></p>
                            <strong><?= esc($item['date_range']) ?></strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?= view('layouts/_empty_state', [
                'emptyIcon' => 'calendar-days',
                'emptyTitle' => 'Jadwal Belum Dipublikasikan',
                'emptyMessage' => 'Admin dapat mengatur gelombang pendaftaran, jadwal daftar ulang, dan MPLS dari dashboard.'
            ]) ?>
        <?php endif; ?>
    </div>
</section>

<!-- Section 5.2: Registration Flow -->
<section class="sp-flow-section bg-white" id="alur" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header text-center">
            <h2 class="sp-section-title-sm">Alur Pendaftaran</h2>
            <p class="text-muted small">Langkah ringkas untuk menyelesaikan pendaftaran online.</p>
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
                            <?php
                            // Pilih icon berdasarkan nama jalur secara case-insensitive
                            $jalurName = strtolower($jalur['name'] ?? '');
                            if (str_contains($jalurName, 'zonasi') || str_contains($jalurName, 'zona')) {
                                $jalurIcon = 'map-pin';
                            } elseif (str_contains($jalurName, 'prestasi') || str_contains($jalurName, 'akademik')) {
                                $jalurIcon = 'award';
                            } elseif (str_contains($jalurName, 'afirmasi') || str_contains($jalurName, 'inklusif')) {
                                $jalurIcon = 'heart-handshake';
                            } elseif (str_contains($jalurName, 'mutasi') || str_contains($jalurName, 'pindah')) {
                                $jalurIcon = 'move-right';
                            } else {
                                $jalurIcon = 'graduation-cap';
                            }
                            ?>
                            <i data-lucide="<?= esc($jalurIcon) ?>"></i>
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
                            <div class="progress mb-4" style="height:10px; border-radius:20px; overflow: visible;">
                                <div class="progress-bar" role="progressbar" 
                                     aria-valuenow="<?= $jalur['percentage_filled'] ?? 0 ?>"
                                     aria-valuemin="0" aria-valuemax="100"
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

<!-- Section 7.5: School Gallery -->
<section class="sp-gallery-section bg-light" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
            <div>
                <h2 class="sp-section-title-sm mb-1"><?= esc($campusTitle ?? 'Lingkungan & Fasilitas') ?></h2>
                <p class="text-muted small mb-0"><?= esc($campusDescription ?? 'Suasana belajar yang nyaman dan kondusif untuk tumbuh kembang siswa.') ?></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= base_url('/lingkungan-kampus') ?>" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Lihat Lingkungan</a>
                <a href="<?= base_url('/galeri') ?>" class="btn btn-primary rounded-pill px-4 fw-bold">Lihat Semua Galeri</a>
            </div>
        </div>
        <div class="row g-4">
            <?php if (!empty($gallery)): ?>
                <?php foreach ($gallery as $index => $item): ?>
                    <?= view('public/_gallery_card', ['item' => $item, 'index' => $index]) ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <?= view('layouts/_empty_state', [
                        'emptyIcon' => 'image',
                        'emptyTitle' => 'Galeri Belum Tersedia',
                        'emptyMessage' => 'Foto dan video lingkungan sekolah akan tampil setelah dipublikasikan dari dashboard admin.'
                    ]) ?>
                </div>
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
        <div class="sp-testimonial-strip" aria-label="Testimoni sekolah">
            <?php foreach ($testimonials as $testi): ?>
            <article class="sp-testimonial-card animate-up">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php
                    $testiPhotoSrc = !empty($testi['photo'])
                        ? ((strpos($testi['photo'], 'http') === 0) ? esc($testi['photo'], 'url') : base_url(esc($testi['photo'])))
                        : 'https://ui-avatars.com/api/?name=' . urlencode($testi['name']) . '&background=random&size=56';
                    ?>
                    <img src="<?= $testiPhotoSrc ?>" class="rounded-circle border flex-shrink-0" style="width:56px;height:56px;object-fit:cover;" alt="Foto <?= esc($testi['name']) ?>" loading="lazy" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($testi['name']) ?>'">
                    <div>
                        <h5 class="fw-800 mb-0" style="font-size: 1rem;"><?= esc($testi['name']) ?></h5>
                        <small class="text-muted"><?= esc($testi['role']) ?></small>
                    </div>
                </div>
                <div class="text-warning mb-3" aria-label="Rating <?= (int)$testi['rating'] ?> dari 5 bintang">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i data-lucide="star" class="sp-star<?= $i <= (int)$testi['rating'] ? ' sp-star--filled' : '' ?>" style="width:14px;height:14px;"></i>
                    <?php endfor; ?>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.9rem; font-style: italic; line-height: 1.6;">"<?= esc($testi['content']) ?>"</p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section 8: Latest News -->
<section class="sp-news-section bg-white" style="padding: var(--sp-section-gap) 0;">
    <div class="container">
        <div class="sp-section-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="sp-section-title-sm mb-1">Kegiatan &amp; Berita</h2>
                <p class="text-muted small mb-0">Informasi terbaru seputar kegiatan di <?= esc($schoolName) ?>.</p>
            </div>
            <a href="<?= base_url('/pengumuman') ?>" class="sp-view-all flex-shrink-0">Lihat Semua &#8594;</a>
        </div>
        <div class="sp-news-container mt-4">
            <?php if (isset($announcements) && !empty($announcements)): ?>
                <?php foreach ($announcements as $news): ?>
                <?php
                    $newsModalId = 'homeNewsModal' . (int) $news['id'];
                    $newsDate = !empty($news['published_at']) ? date('d M Y', strtotime($news['published_at'])) : date('d M Y', strtotime($news['created_at']));
                    $newsImgSrc = !empty($news['image'])
                        ? ((strpos($news['image'], 'http') === 0) ? esc($news['image'], 'url') : base_url(esc($news['image'])))
                        : base_url('assets/img/gallery-placeholder.svg');
                ?>
                <button type="button" class="sp-news-card animate-up border-0 text-start p-0 w-100" data-bs-toggle="modal" data-bs-target="#<?= esc($newsModalId) ?>">
                    <div class="sp-news-image">
                        <img src="<?= $newsImgSrc ?>" alt="<?= esc($news['title']) ?>" loading="lazy" onerror="this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>'">
                        <span class="sp-news-tag"><?= esc($news['tag'] ?? 'INFO') ?></span>
                    </div>
                    <div class="sp-news-body">
                        <h3 class="sp-news-title"><?= esc($news['title']) ?></h3>
                        <div class="sp-news-date">
                            <i data-lucide="calendar" style="width:12px;height:12px;"></i>
                            <span><?= esc($newsDate) ?></span>
                        </div>
                    </div>
                </button>

                <div class="modal fade" id="<?= esc($newsModalId) ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                            <div class="modal-header bg-primary text-white border-0">
                                <div>
                                    <span class="badge bg-white text-primary rounded-pill mb-2"><?= esc($news['tag'] ?? 'INFO') ?></span>
                                    <h5 class="modal-title fw-bold mb-0"><?= esc($news['title']) ?></h5>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body p-0">
                                <img src="<?= $newsImgSrc ?>" alt="<?= esc($news['title']) ?>" class="w-100" style="max-height:360px;object-fit:cover;" onerror="this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>'">
                                <div class="p-4 p-lg-5">
                                    <div class="sp-news-date mb-3">
                                        <i data-lucide="calendar" style="width:14px;height:14px;"></i>
                                        <span><?= esc($newsDate) ?></span>
                                    </div>
                                    <div class="text-muted" style="line-height:1.8;"><?= nl2br(esc($news['content'] ?? '')) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="w-100">
                    <?= view('layouts/_empty_state', [
                        'emptyIcon' => 'megaphone',
                        'emptyTitle' => 'Belum Ada Berita',
                        'emptyMessage' => 'Berita dan pengumuman terbaru akan tampil setelah dipublikasikan dari dashboard admin.'
                    ]) ?>
                </div>
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
                                <?= nl2br(esc($faq['answer'])) ?>
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
        <div class="card border-0 shadow-lg overflow-hidden animate-up sp-bottom-cta-card">
            <div class="card-body p-5 text-center text-white">
                <h2 class="fw-800 h1 mb-3">Siapkan Masa Depan Putra-Putri Anda Bersama <?= esc($schoolName) ?></h2>
                <p class="opacity-90 mb-4 mx-auto" style="max-width: 600px;">Pendaftaran Tahun Ajaran <?= esc($academicYear) ?> telah dibuka. Kuota terbatas untuk setiap jalur penerimaan. Segera amankan kursi Anda.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= esc($ctaUrl) ?>" class="btn btn-light btn-lg rounded-pill px-5 fw-800 text-primary btn-pulse"><?= esc($ctaText) ?></a>
                    <a href="<?= base_url('/kontak') ?>" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-700">Hubungi Panitia</a>
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
                    // Hitung offset: navbar height + extra padding
                    const navbar = document.getElementById('main-navbar');
                    const navbarHeight = navbar ? navbar.offsetHeight : 80;
                    const extraPadding = 16;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight - extraPadding;
                    
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
                const href = this.getAttribute('href');
                // Hanya tampilkan loading untuk navigasi nyata (bukan anchor #)
                if (href && href !== '#' && !href.startsWith('#')) {
                    const originalHTML = this.innerHTML;
                    const originalStyle = this.getAttribute('style') || '';
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memuat...';
                    this.style.pointerEvents = 'none';
                    this.style.opacity = '0.8';
                    // Restore jika navigasi tidak terjadi dalam 5 detik (misal: buka tab baru)
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                        this.setAttribute('style', originalStyle);
                        this.style.pointerEvents = '';
                    }, 5000);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
