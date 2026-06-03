<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="sp-page-header">
    <div class="container animate-fade-up">
        <h1 class="fw-bold">
            <i data-lucide="building-2" style="width:28px;height:28px;" class="me-2"></i>
            Lingkungan & Kampus
        </h1>
        <p class="opacity-90">Ruang belajar, fasilitas, dan suasana sekolah.</p>
    </div>
</div>

<section class="sp-section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 animate-fade-up">
                <h2 class="sp-section-title text-start ms-0 mb-4"><?= esc($campusTitle) ?></h2>
                <p class="text-muted" style="font-size:1.05rem;line-height:1.9;"><?= nl2br(esc($campusDescription)) ?></p>
                <a href="<?= base_url('/galeri') ?>" class="btn btn-primary rounded-pill px-4 fw-bold mt-3">
                    <i data-lucide="image" class="me-2" style="width:18px;height:18px;"></i>
                    Lihat Galeri Lengkap
                </a>
            </div>
            <div class="col-lg-6 animate-fade-up delay-1">
                <div class="row g-3">
                    <?php if (!empty($facilities)): ?>
                    <?php foreach (array_slice($facilities, 0, 8) as $facility): ?>
                        <div class="col-sm-6">
                            <div class="glass-panel p-3 rounded-4 h-100 d-flex gap-3 align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                                    <i data-lucide="check-circle-2" style="width:20px;height:20px;"></i>
                                </div>
                                <span class="fw-semibold text-dark"><?= esc($facility) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <?= view('layouts/_empty_state', [
                                'emptyIcon' => 'building-2',
                                'emptyTitle' => 'Data Fasilitas Belum Tersedia',
                                'emptyMessage' => 'Daftar fasilitas akan tampil setelah diisi dari dashboard admin.'
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sp-section-alt">
    <div class="container">
        <div class="sp-section-header text-center mb-5">
            <h2 class="sp-section-title-sm">Dokumentasi Lingkungan</h2>
            <p class="text-muted small">Foto dan video fasilitas sekolah yang diambil dari data galeri.</p>
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
                        'emptyTitle' => 'Dokumentasi Belum Tersedia',
                        'emptyMessage' => 'Foto dan video lingkungan sekolah akan tampil setelah dipublikasikan dari dashboard admin.'
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
