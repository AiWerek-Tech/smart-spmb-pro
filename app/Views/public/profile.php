<?php
/**
 * @var string $schoolHistory
 * @var string $schoolVision
 * @var string $schoolMission
 * @var string $schoolAccred
 * @var string $schoolAccredYear
 * @var array $facilities
 */
?>
<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Header Section -->
<div class="sp-page-header">
    <div class="container animate-fade-up">
        <h1 class="fw-bold">
            <i data-lucide="school" style="width:28px;height:28px;" class="me-2"></i>
            Profil Sekolah
        </h1>
        <p class="opacity-90">Kenali lebih dekat tentang institusi kami</p>
    </div>
</div>

<!-- Sejarah Section -->
<section class="sp-section" id="sejarah">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-md-7 mb-4 animate-fade-up">
                <h2 class="sp-section-title text-start ms-0 mb-4">Sejarah Sekolah</h2>
                <div style="font-size:1.05rem;line-height:1.8;color:var(--sp-text-color);">
                    <?= nl2br(esc($schoolHistory ?? '')) ?>
                </div>
            </div>
            <div class="col-md-5 animate-fade-up delay-1">
                <div class="position-relative">
                    <div style="background:linear-gradient(135deg,var(--sp-primary),var(--sp-accent));border-radius:var(--sp-radius-lg);height:300px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;text-align:center;box-shadow:var(--sp-shadow-lg);">
                        <i data-lucide="book-open" style="width:64px;height:64px;opacity:0.8;" class="mb-3"></i>
                        <p class="fw-bold px-4">Warisan Pendidikan Berkualitas & Berkelanjutan</p>
                    </div>
                    <!-- Decorative floating element -->
                    <div class="position-absolute bottom-0 start-0 glass-panel p-3 m-n3 rounded-3 shadow-sm animate-fade-up delay-2 d-none d-md-block" style="width: 180px;">
                        <div class="small fw-bold text-primary">Berdiri Sejak</div>
                        <div class="h5 fw-bold mb-0">2010</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visi & Misi Section -->
<section class="sp-section-alt">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 animate-fade-up">
                <div class="glass-panel p-4 rounded-4 h-100 hover-lift border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                            <i data-lucide="eye" style="width:24px;height:24px;"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-0">Visi</h3>
                    </div>
                    <div class="ps-1">
                        <p class="text-muted" style="line-height:1.8; font-size: 1.05rem;"><?= nl2br(esc($schoolVision ?? '')) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 animate-fade-up delay-1">
                <div class="glass-panel p-4 rounded-4 h-100 hover-lift border-0 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-accent bg-opacity-10 text-accent p-3 rounded-3" style="color: var(--sp-accent) !important;">
                            <i data-lucide="target" style="width:24px;height:24px;"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-0">Misi</h3>
                    </div>
                    <div class="ps-1">
                        <p class="text-muted" style="line-height:1.8; font-size: 1.05rem;"><?= nl2br(esc($schoolMission ?? '')) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Akreditasi Section -->
<section class="sp-section">
    <div class="container animate-fade-up">
        <h2 class="sp-section-title">Akreditasi</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="glass-panel p-5 text-center rounded-4 hover-lift border-0 shadow-sm">
                    <div class="bg-warning bg-opacity-10 text-warning p-4 rounded-circle d-inline-flex mb-4">
                        <i data-lucide="award" style="width:48px;height:48px;"></i>
                    </div>
                    <h3 class="display-6 fw-bold text-primary mb-2">Peringkat <?= esc($schoolAccred ?? 'A') ?></h3>
                    <p class="fw-semibold text-muted mb-4">Tahun <?= esc($schoolAccredYear ?? '2025') ?></p>
                    <p class="text-muted mb-0" style="font-size:1rem;line-height:1.8;">
                        Akreditasi institusional dari badan akreditasi nasional menunjukkan komitmen kami terhadap standar kualitas pendidikan yang unggul.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fasilitas Section -->
<section class="sp-section-alt" id="fasilitas">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Fasilitas Sekolah</h2>
        <div class="row g-4">
            <?php if (!empty($facilities)): ?>
                <?php foreach ($facilities as $index => $facility): ?>
                    <div class="col-md-4 col-sm-6 animate-fade-up delay-<?= ($index % 3) + 1 ?>">
                        <div class="glass-panel p-4 text-center rounded-4 hover-lift border-0 shadow-sm h-100">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-inline-flex mb-3">
                                <i data-lucide="check-circle" style="width:28px;height:28px;"></i>
                            </div>
                            <h5 class="fw-bold mb-0"><?= esc(trim($facility)) ?></h5>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Tenaga Pendidik Section -->
<section class="sp-section" id="guru">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Tenaga Pendidik</h2>
        <div class="row g-4 justify-content-center">
            <?php if (!empty($teachers)): ?>
                <?php foreach ($teachers as $index => $teacher): ?>
                    <div class="col-lg-3 col-md-6 col-sm-6 animate-fade-up delay-<?= ($index % 4) + 1 ?>">
                        <div class="glass-panel p-4 text-center rounded-4 hover-lift border-0 shadow-sm h-100">
                            <div class="mb-4 mx-auto rounded-circle overflow-hidden shadow-sm" style="width: 120px; height: 120px; border: 3px solid var(--sp-primary-light);">
                                <img src="<?= $teacher['photo'] ?>" alt="<?= esc($teacher['name']) ?>" class="w-100 h-100 object-fit-cover">
                            </div>
                            <h5 class="fw-bold mb-1" style="font-size: 1.05rem;"><?= esc($teacher['name']) ?></h5>
                            <p class="text-primary fw-semibold small mb-0"><?= esc($teacher['role']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="sp-section" id="galeri">
    <div class="container">
        <h2 class="sp-section-title animate-fade-up">Galeri Sekolah</h2>
        <div class="row g-4">
            <?php if (!empty($gallery)): ?>
                <?php foreach ($gallery as $index => $item): ?>
                    <div class="col-lg-4 col-md-6 animate-fade-up delay-<?= ($index % 3) + 1 ?>">
                        <div class="position-relative overflow-hidden rounded-4 hover-lift shadow-sm" style="aspect-ratio: 16/10;">
                            <img src="<?= (strpos($item['image'], 'http') === 0) ? esc($item['image']) : base_url($item['image']) ?>" alt="<?= esc($item['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>'">
                            <div class="sp-gallery-overlay position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(transparent, rgba(0,0,0,0.75));">
                                <span class="text-white fw-bold small"><?= esc($item['title']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback gallery placeholder -->
                <?php 
                $fallbackGallery = [
                    ['title' => 'Gedung Utama & Kampus'],
                    ['title' => 'Perpustakaan Digital'],
                    ['title' => 'Laboratorium Komputer'],
                    ['title' => 'Ruang Kelas Modern'],
                    ['title' => 'Lapangan Olahraga'],
                    ['title' => 'Laboratorium Sains'],
                ];
                $placeholderImg = base_url('assets/img/gallery-placeholder.svg');
                foreach ($fallbackGallery as $index => $item): 
                ?>
                    <div class="col-lg-4 col-md-6 animate-fade-up delay-<?= ($index % 3) + 1 ?>">
                        <div class="position-relative overflow-hidden rounded-4 hover-lift shadow-sm" style="aspect-ratio: 16/10;">
                            <img src="<?= $placeholderImg ?>" alt="<?= esc($item['title']) ?>" class="w-100 h-100 object-fit-cover">
                            <div class="sp-gallery-overlay position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(transparent, rgba(0,0,0,0.75));">
                                <span class="text-white fw-bold small"><?= esc($item['title']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
