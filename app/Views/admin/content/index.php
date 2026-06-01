<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in">
    <!-- Header Page -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 text-primary">Profil & Galeri Sekolah</h4>
                <p class="text-muted mb-0">Kelola visi, misi, sejarah, slogan, serta foto-foto galeri yang ditampilkan di website publik.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/banners') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="me-1" data-lucide="image"></i> Banner Hero
                </a>
                <a href="<?= base_url('admin/testimonials') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="me-1" data-lucide="message-square"></i> Testimoni
                </a>
                <a href="<?= base_url('admin/statistics') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="me-1" data-lucide="bar-chart-2"></i> Statistik
                </a>
            </div>
        </div>
    </div>

    <!-- LEFT SIDE: Profile Settings -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title text-primary"><i class="me-2" data-lucide="file-signature"></i> Sunting Profil Sekolah</h5>
                <small class="text-muted">Gunakan bahasa Indonesia yang baku dan formal.</small>
            </div>
            
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/content/save') ?>">
                    <?= csrf_field() ?>

                    <!-- Tagline -->
                    <div class="mb-3">
                        <label for="tagline" class="form-label fw-bold small">Slogan / Tagline Sekolah</label>
                        <input type="text" class="form-control" id="tagline" name="tagline" value="<?= esc(old('tagline', $settings['tagline'] ?? '')) ?>" placeholder="Masukkan slogan/tagline sekolah...">
                    </div>

                    <!-- Vision -->
                    <div class="mb-3">
                        <label for="vision" class="form-label fw-bold small">Visi Sekolah <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="vision" name="vision" rows="3" required placeholder="Masukkan visi sekolah..."><?= esc(old('vision', $settings['vision'] ?? '')) ?></textarea>
                    </div>

                    <!-- Mission -->
                    <div class="mb-3">
                        <label for="mission" class="form-label fw-bold small">Misi Sekolah <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="mission" name="mission" rows="5" required placeholder="Masukkan misi sekolah (gunakan poin-poin/bullet)..."><?= esc(old('mission', $settings['mission'] ?? '')) ?></textarea>
                        <small class="text-muted">Gunakan pemisah baris baru untuk setiap butir misi sekolah.</small>
                    </div>

                    <!-- History -->
                    <div class="mb-4">
                        <label for="history" class="form-label fw-bold small">Sejarah Singkat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="history" name="history" rows="6" required placeholder="Tuliskan sejarah singkat berdirinya sekolah..."><?= esc(old('history', $settings['history'] ?? '')) ?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="me-2" data-lucide="save"></i> Perbarui Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Gallery Management -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title text-primary"><i class="me-2" data-lucide="images"></i> Galeri Sekolah</h5>
                    <small class="text-muted">Foto galeri yang tampil di halaman profil.</small>
                </div>
                <span class="badge bg-label-primary rounded"><?= count($gallery) ?> Foto</span>
            </div>
            
            <div class="card-body">
                <!-- Upload form -->
                <form method="POST" action="<?= base_url('admin/content/gallery/upload') ?>" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded border">
                    <?= csrf_field() ?>
                    <label for="gallery_image" class="form-label fw-bold small mb-2"><i class="me-1 text-primary" data-lucide="upload"></i> Unggah Foto Baru</label>
                    <div class="input-group">
                        <input type="file" class="form-control form-control-sm" id="gallery_image" name="gallery_image" required>
                        <button class="btn btn-primary btn-sm px-3" type="submit">
                            <i  data-lucide="plus"></i> Unggah
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Ukuran maksimal file: 2 MB (Format: JPG, PNG).</small>
                </form>

                <!-- Grid list -->
                <div class="row g-2 overflow-auto" style="max-height: 480px;">
                    <?php if (empty($gallery)): ?>
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fs-1 mb-2" data-lucide="images"></i>
                            <p class="mb-0">Belum ada foto galeri.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($gallery as $item): ?>
                            <div class="col-6 col-sm-4 col-md-6 position-relative gallery-item-wrapper" style="height: 120px;">
                                <img src="<?= (strpos($item['image'], 'http') === 0) ? esc($item['image']) : base_url(esc($item['image'])) ?>" class="img-fluid w-100 h-100 rounded border object-fit-cover shadow-xs" alt="<?= esc($item['title']) ?>" onerror="this.onerror=null;this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>';">
                                
                                <!-- Delete Overlay Trigger -->
                                <form action="<?= base_url('admin/content/gallery/'.$item['id'].'/delete') ?>" method="POST" onsubmit="return confirm('Hapus foto ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm p-1 rounded-circle position-absolute top-0 end-0 m-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background-color: rgba(255, 62, 29, 0.85); border: none;" title="Hapus Foto">
                                        <i class="font-size-xs" data-lucide="trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>
