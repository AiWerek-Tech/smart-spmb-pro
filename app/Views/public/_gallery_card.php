<?php
$id = 'galleryDetailModal' . ($item['id'] ?? $index ?? uniqid());
$type = $item['media_type'] ?? 'photo';
$image = $item['image'] ?? '';
$imageUrl = $image !== '' && str_starts_with($image, 'http') ? $image : base_url($image ?: 'assets/img/gallery-placeholder.svg');
$videoUrl = $item['video_url'] ?? '';
$videoEmbedUrl = $videoUrl;
if ($type === 'video' && $videoUrl !== '' && !str_contains($videoUrl, '/embed/')) {
    $host = parse_url($videoUrl, PHP_URL_HOST) ?: '';
    $path = trim(parse_url($videoUrl, PHP_URL_PATH) ?: '', '/');
    parse_str(parse_url($videoUrl, PHP_URL_QUERY) ?: '', $query);

    if (str_contains($host, 'youtu.be') && $path !== '') {
        $videoEmbedUrl = 'https://www.youtube.com/embed/' . $path;
    } elseif (str_contains($host, 'youtube.com') && !empty($query['v'])) {
        $videoEmbedUrl = 'https://www.youtube.com/embed/' . $query['v'];
    }
}
?>
<div class="col-md-6 col-lg-4 animate-fade-up delay-<?= (($index ?? 0) % 3) + 1 ?>">
    <button type="button" class="sp-gallery-card-btn w-100 text-start border-0 bg-transparent p-0" data-bs-toggle="modal" data-bs-target="#<?= esc($id) ?>">
        <div class="position-relative overflow-hidden rounded-4 shadow-sm hover-lift bg-white" style="aspect-ratio:16/10;">
            <img src="<?= esc($imageUrl) ?>" alt="<?= esc($item['title'] ?? 'Galeri sekolah') ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>'">
            <div class="position-absolute top-0 start-0 m-3">
                <span class="badge <?= $type === 'video' ? 'bg-danger' : 'bg-primary' ?> rounded-pill px-3 py-2">
                    <i data-lucide="<?= $type === 'video' ? 'play' : 'image' ?>" style="width:13px;height:13px;" class="me-1"></i>
                    <?= $type === 'video' ? 'Video' : 'Foto' ?>
                </span>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background:linear-gradient(transparent,rgba(0,0,0,.78));">
                <div class="text-white fw-bold"><?= esc($item['title'] ?? 'Galeri Sekolah') ?></div>
                <?php if (!empty($item['category'])): ?>
                    <div class="text-white-50 small"><?= esc($item['category']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </button>
</div>

<div class="modal fade" id="<?= esc($id) ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold mb-0"><?= esc($item['title'] ?? 'Galeri Sekolah') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0">
                <?php if ($type === 'video' && $videoEmbedUrl): ?>
                    <div class="ratio ratio-16x9 bg-dark">
                        <iframe src="<?= esc($videoEmbedUrl) ?>" title="<?= esc($item['title'] ?? 'Video sekolah') ?>" allowfullscreen loading="lazy"></iframe>
                    </div>
                <?php else: ?>
                    <img src="<?= esc($imageUrl) ?>" alt="<?= esc($item['title'] ?? 'Galeri sekolah') ?>" class="w-100" style="max-height:72vh;object-fit:contain;background:#0f172a;">
                <?php endif; ?>
                <div class="p-4 p-lg-5">
                    <?php if (!empty($item['category'])): ?>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-3"><?= esc($item['category']) ?></span>
                    <?php endif; ?>
                    <p class="text-muted mb-0" style="line-height:1.8;"><?= nl2br(esc($item['description'] ?? 'Deskripsi belum tersedia.')) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
