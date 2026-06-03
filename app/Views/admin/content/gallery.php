<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="sp-admin-page admin-page-shell animate-fade-in" aria-labelledby="admin-gallery-title">
    <header class="sp-page-toolbar admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konten Publik</p>
            <h1 id="admin-gallery-title">Galeri Sekolah</h1>
            <p class="admin-page-subtitle">Kelola foto dan video YouTube untuk homepage, halaman profil, galeri publik, serta lingkungan kampus.</p>
        </div>
        <div class="sp-toolbar-actions admin-page-actions">
            <span class="sp-status-pill"><i data-lucide="calendar"></i> <?= esc($activeYear ?? '-') ?></span>
            <a href="<?= base_url('admin/content') ?>" class="btn btn-outline-primary"><i data-lucide="school"></i> Profil</a>
            <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-primary"><i data-lucide="users"></i> Guru</a>
        </div>
    </header>

    <div class="row g-3">
        <div class="col-xl-4">
            <div class="card admin-secondary-panel shadow-sm border">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="upload"></i> Tambah Galeri</h2>
                    <p class="admin-section-subtitle">Upload otomatis masuk direktori tahun pelajaran aktif.</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('admin/content/gallery/upload') ?>" enctype="multipart/form-data" class="sp-compact-form">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Judul</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Jenis</label>
                                <select class="form-select gallery-media-type" name="media_type" data-target="#new-video-url" data-file="#gallery_image">
                                    <option value="photo">Foto</option>
                                    <option value="video">Video YouTube</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Kategori</label>
                                <input type="text" class="form-control" name="category" placeholder="Fasilitas">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-bold small">Foto / Thumbnail</label>
                            <input type="file" class="form-control" id="gallery_image" name="gallery_image" required>
                            <small class="text-muted">Foto wajib untuk jenis foto. Untuk video, foto bersifat opsional.</small>
                        </div>
                        <div class="mb-3 d-none" id="new-video-url">
                            <label class="form-label fw-bold small">URL YouTube</label>
                            <input type="url" class="form-control" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="row g-2 align-items-end">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Urutan</label>
                                <input type="number" class="form-control" name="sort_order" value="0" min="0">
                            </div>
                            <div class="col-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="new_gallery_active" checked>
                                    <label class="form-check-label small" for="new_gallery_active">Aktif</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 mt-3" type="submit">
                            <i data-lucide="plus"></i> Tambahkan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card admin-secondary-panel shadow-sm border">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="image"></i> Daftar Galeri</h2>
                        <p class="admin-section-subtitle">Arsip tahun pelajaran <?= esc($activeYear ?? '-') ?>.</p>
                    </div>
                    <span class="sp-status-pill"><?= count($gallery ?? []) ?> item</span>
                </div>
                <div class="card-body">
                    <?php if (empty($gallery)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fs-1 mb-2" data-lucide="image"></i>
                            <p class="mb-0">Belum ada item galeri untuk tahun ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="sp-gallery-admin-grid">
                            <?php foreach ($gallery as $item): ?>
                                <?php $imageUrl = (strpos($item['image'], 'http') === 0) ? esc($item['image']) : base_url(esc($item['image'])); ?>
                                <div class="sp-gallery-admin-item">
                                    <img src="<?= $imageUrl ?>" alt="<?= esc($item['title']) ?>" onerror="this.onerror=null;this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>';">
                                    <div class="sp-gallery-admin-overlay">
                                        <div class="min-w-0">
                                            <div class="fw-bold text-truncate"><?= esc($item['title']) ?></div>
                                            <div class="small text-white-50 text-truncate"><?= esc($item['category'] ?? 'Tanpa kategori') ?></div>
                                        </div>
                                        <span class="badge <?= ($item['media_type'] ?? 'photo') === 'video' ? 'bg-danger' : 'bg-primary' ?>"><?= ($item['media_type'] ?? 'photo') === 'video' ? 'Video' : 'Foto' ?></span>
                                    </div>
                                    <div class="sp-gallery-admin-actions">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editGalleryModal<?= $item['id'] ?>" title="Edit">
                                            <i data-lucide="pencil"></i>
                                        </button>
                                        <form action="<?= base_url('admin/content/gallery/'.$item['id'].'/delete') ?>" method="POST" onsubmit="return confirm('Hapus item galeri ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="modal fade" id="editGalleryModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <form method="POST" action="<?= base_url('admin/content/gallery/'.$item['id'].'/update') ?>" enctype="multipart/form-data">
                                                <?= csrf_field() ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Item Galeri</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <label class="form-label small fw-bold">Judul</label>
                                                            <input type="text" class="form-control" name="title" value="<?= esc($item['title']) ?>" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold">Jenis Media</label>
                                                            <select class="form-select gallery-media-type" name="media_type" data-target="#edit-video-url-<?= $item['id'] ?>" data-file="#edit-gallery-image-<?= $item['id'] ?>">
                                                                <option value="photo" <?= ($item['media_type'] ?? 'photo') === 'photo' ? 'selected' : '' ?>>Foto</option>
                                                                <option value="video" <?= ($item['media_type'] ?? 'photo') === 'video' ? 'selected' : '' ?>>Video YouTube</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold">Deskripsi</label>
                                                            <textarea class="form-control" name="description" rows="3"><?= esc($item['description'] ?? '') ?></textarea>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Kategori</label>
                                                            <input type="text" class="form-control" name="category" value="<?= esc($item['category'] ?? '') ?>">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small fw-bold">Urutan</label>
                                                            <input type="number" class="form-control" name="sort_order" value="<?= (int) ($item['sort_order'] ?? 0) ?>" min="0">
                                                        </div>
                                                        <div class="col-md-3 d-flex align-items-end">
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="gallery-active-<?= $item['id'] ?>" <?= ($item['is_active'] ?? 0) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="gallery-active-<?= $item['id'] ?>">Aktif</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 <?= ($item['media_type'] ?? 'photo') === 'video' ? '' : 'd-none' ?>" id="edit-video-url-<?= $item['id'] ?>">
                                                            <label class="form-label small fw-bold">URL YouTube</label>
                                                            <input type="url" class="form-control" name="video_url" value="<?= esc($item['video_url'] ?? '') ?>">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold">Ganti Foto / Thumbnail</label>
                                                            <input type="file" class="form-control" id="edit-gallery-image-<?= $item['id'] ?>" name="gallery_image">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function syncMediaType(select) {
        const target = document.querySelector(select.dataset.target);
        const fileInput = document.querySelector(select.dataset.file);
        const isVideo = select.value === 'video';
        if (target) target.classList.toggle('d-none', !isVideo);
        const videoInput = target ? target.querySelector('input[name="video_url"]') : null;
        if (videoInput) videoInput.required = isVideo;
        if (fileInput) fileInput.required = !isVideo && fileInput.id === 'gallery_image';
    }

    document.querySelectorAll('.gallery-media-type').forEach(function(select) {
        syncMediaType(select);
        select.addEventListener('change', function() {
            syncMediaType(select);
        });
    });
});
</script>
<?= $this->endSection() ?>
