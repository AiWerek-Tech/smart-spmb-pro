<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 text-primary">Manajemen Banner Hero</h4>
                <p class="text-muted mb-0">Kelola konten utama yang muncul di bagian atas beranda.</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                <i class="me-2" data-lucide="plus"></i> Tambah Banner
            </button>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Pratinjau</th>
                            <th>Informasi Banner</th>
                            <th>Status</th>
                            <th>Urutan</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($banners)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fs-1 mb-2" data-lucide="image"></i>
                                    <p class="mb-0">Belum ada banner.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($banners as $index => $banner): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <img src="<?= (strpos($banner['image'], 'http') === 0) ? esc($banner['image']) : base_url(esc($banner['image'])) ?>" class="rounded border shadow-xs object-fit-cover" style="width: 120px; height: 60px;" alt="Banner" onerror="this.onerror=null;this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>';">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($banner['title']) ?></div>
                                        <small class="text-muted"><?= esc($banner['subtitle']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $banner['is_active'] ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $banner['is_active'] ? 'success' : 'danger' ?>">
                                            <?= $banner['is_active'] ? 'Aktif' : 'Non-aktif' ?>
                                        </span>
                                    </td>
                                    <td><?= $banner['sort_order'] ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-label-warning me-1" data-bs-toggle="modal" data-bs-target="#editBanner<?= $banner['id'] ?>">
                                            <i data-lucide="edit-3"></i>
                                        </button>
                                        <form action="<?= base_url('admin/banners/'.$banner['id'].'/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus banner ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-label-danger">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editBanner<?= $banner['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="<?= base_url('admin/banners/'.$banner['id'].'/update') ?>" method="POST" enctype="multipart/form-data">
                                            <?= csrf_field() ?>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Banner</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Judul Utama</label>
                                                        <input type="text" name="title" class="form-control" value="<?= esc($banner['title']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Sub-judul / Deskripsi</label>
                                                        <textarea name="subtitle" class="form-control" rows="2"><?= esc($banner['subtitle']) ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Gambar Banner (Kosongkan jika tidak diganti)</label>
                                                        <input type="file" name="banner_img" class="form-control">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Teks Tombol (CTA)</label>
                                                            <input type="text" name="cta_text" class="form-control" value="<?= esc($banner['cta_text']) ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Link Tombol (URL)</label>
                                                            <input type="text" name="cta_url" class="form-control" value="<?= esc($banner['cta_url']) ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Urutan</label>
                                                            <input type="number" name="sort_order" class="form-control" value="<?= $banner['sort_order'] ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Status</label>
                                                            <div class="form-check form-switch mt-2">
                                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= $banner['is_active'] ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Aktif</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/banners/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Banner Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Utama</label>
                        <input type="text" name="title" class="form-control" required placeholder="Contoh: Wujudkan Masa Depan Cemerlang">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sub-judul / Deskripsi</label>
                        <textarea name="subtitle" class="form-control" rows="2" placeholder="Deskripsi singkat banner..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Gambar Banner</label>
                        <input type="file" name="banner_img" class="form-control" required>
                        <small class="text-muted">Rekomendasi ukuran: 1200x600px, Maks 2MB.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teks Tombol (CTA)</label>
                            <input type="text" name="cta_text" class="form-control" placeholder="Contoh: Daftar Sekarang">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Link Tombol (URL)</label>
                            <input type="text" name="cta_url" class="form-control" placeholder="Contoh: auth/register">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Urutan</label>
                            <input type="number" name="sort_order" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Banner</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
