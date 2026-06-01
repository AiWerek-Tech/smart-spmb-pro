<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="sp-page-header">
    <div class="container animate-fade-up">
        <h1 class="fw-bold">
            <i data-lucide="images" style="width:28px;height:28px;" class="me-2"></i>
            Galeri Sekolah
        </h1>
        <p class="opacity-90">Dokumentasi foto dan video kegiatan, fasilitas, dan lingkungan sekolah.</p>
    </div>
</div>

<section class="sp-section">
    <div class="container">
        <div class="d-flex justify-content-center gap-2 flex-wrap mb-5">
            <a class="btn <?= $activeType === 'all' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-4" href="<?= base_url('/galeri') ?>">Semua</a>
            <a class="btn <?= $activeType === 'photo' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-4" href="<?= base_url('/galeri?type=photo') ?>">Foto</a>
            <a class="btn <?= $activeType === 'video' ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-4" href="<?= base_url('/galeri?type=video') ?>">Video</a>
        </div>

        <?php if (!empty($items)): ?>
            <div class="row g-4">
                <?php foreach ($items as $index => $item): ?>
                    <?= view('public/_gallery_card', ['item' => $item, 'index' => $index]) ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?= view('layouts/_empty_state', [
                'emptyIcon' => 'images',
                'emptyTitle' => 'Galeri Belum Tersedia',
                'emptyMessage' => 'Belum ada item galeri yang dipublikasikan.'
            ]) ?>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
