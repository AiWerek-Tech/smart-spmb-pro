<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in justify-content-center">
    <div class="col-md-10 col-lg-8">
        <!-- Back button -->
        <div class="mb-3">
            <a href="<?= base_url('admin/announcements') ?>" class="text-decoration-none">
                <i class="me-1" data-lucide="arrow-left"></i> Kembali ke Pengumuman
            </a>
        </div>

        <div class="card shadow-sm border">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title text-primary"><i class="me-2" data-lucide="edit"></i> Edit Pengumuman</h5>
                <small class="text-muted">Perbarui judul dan konten pengumuman resmi.</small>
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

                <form method="POST" action="<?= base_url('admin/announcements/'.$announcement['id'].'/update') ?>">
                    <?= csrf_field() ?>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold small">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?= old('title', $announcement['title']) ?>" placeholder="Masukkan judul pengumuman..." required>
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
<?= $this->endSection() ?>
