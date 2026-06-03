<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-announcement-edit-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konten Publik</p>
            <h1 id="admin-announcement-edit-title">Edit Pengumuman</h1>
            <p class="admin-page-subtitle">Perbarui judul, konten, thumbnail, dan status publikasi pengumuman resmi.</p>
        </div>
        <div class="admin-page-actions">
            <a href="<?= base_url('admin/announcements') ?>" class="btn btn-outline-secondary">
                <i class="me-1" data-lucide="arrow-left"></i> Kembali
            </a>
        </div>
    </header>

    <div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card admin-secondary-panel shadow-sm border">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="edit"></i> Form Pengumuman</h2>
                <p class="admin-section-subtitle">Perbarui data pengumuman resmi.</p>
            </div>
            
            <div class="card-body">
                <!-- Validation errors -->
                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <ul class="mb-0 ps-3">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('admin/announcements/'.$announcement['id'].'/update') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold small">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?= old('title', $announcement['title']) ?>" placeholder="Masukkan judul pengumuman..." required>
                    </div>

                    <!-- Tag & Image row -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="tag" class="form-label fw-bold small">Tag / Kategori</label>
                            <input type="text" class="form-control" id="tag" name="tag" value="<?= old('tag', $announcement['tag'] ?? 'INFO') ?>" placeholder="INFO, PENTING, PENGUMUMAN..." maxlength="50">
                            <div class="form-text">Label singkat yang tampil di kartu berita.</div>
                        </div>
                        <div class="col-md-8">
                            <label for="image" class="form-label fw-bold small">Gambar Thumbnail <span class="text-muted fw-normal">(opsional)</span></label>
                            <?php if (!empty($announcement['image'])): ?>
                                <div class="mb-2">
                                    <img src="<?= base_url($announcement['image']) ?>" alt="Thumbnail saat ini" class="rounded" style="height:60px;object-fit:cover;" onerror="this.style.display='none'">
                                    <small class="text-muted ms-2">Gambar saat ini</small>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Upload gambar baru untuk mengganti. Format JPG/PNG/WebP, maks. 2MB.</div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold small">Konten Pengumuman <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="12" placeholder="Tulis isi pengumuman lengkap..." required><?= old('content', $announcement['content']) ?></textarea>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold small">Status Publikasi <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check card p-3 shadow-none border bg-light position-relative" style="cursor: pointer;">
                                    <input class="form-check-input position-absolute top-0 start-0 m-3" type="radio" name="status" id="status_draft" value="draft" <?= old('status', $announcement['status']) === 'draft' ? 'checked' : '' ?>>
                                    <label class="form-check-label ps-4 fw-semibold text-dark" for="status_draft">
                                        <i class="text-secondary me-2" data-lucide="file-alt"></i> Simpan Sebagai Draft
                                        <small class="text-muted d-block fw-normal mt-1">Pengumuman disimpan dan tidak dipublikasikan ke publik.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card p-3 shadow-none border bg-light position-relative" style="cursor: pointer;">
                                    <input class="form-check-input position-absolute top-0 start-0 m-3" type="radio" name="status" id="status_published" value="published" <?= old('status', $announcement['status']) === 'published' ? 'checked' : '' ?>>
                                    <label class="form-check-label ps-4 fw-semibold text-dark" for="status_published">
                                        <i class="text-success me-2" data-lucide="send"></i> Terbitkan Sekarang
                                        <small class="text-muted d-block fw-normal mt-1">Pengumuman langsung terbit dan dapat dibaca oleh publik.</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="me-2" data-lucide="save"></i> Perbarui Pengumuman
                        </button>
                        <a href="<?= base_url('admin/announcements') ?>" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</section>
<?= $this->endSection() ?>
