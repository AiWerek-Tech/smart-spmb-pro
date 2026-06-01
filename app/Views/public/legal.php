<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="sp-page-header">
    <div class="container animate-fade-up">
        <h1 class="fw-bold">
            <i data-lucide="<?= esc($icon ?? 'file-text') ?>" style="width:28px;height:28px;" class="me-2"></i>
            <?= esc($heading ?? $title ?? 'Informasi') ?>
        </h1>
        <p class="opacity-90">Informasi resmi yang berlaku pada penggunaan layanan Smart SPMB Pro.</p>
    </div>
</div>

<section class="sp-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <article class="glass-panel p-4 p-lg-5 rounded-4 shadow-sm">
                    <div class="text-muted" style="font-size:1rem;line-height:1.9;">
                        <?= nl2br(esc($content ?? 'Konten belum tersedia.')) ?>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
