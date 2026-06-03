<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-testimonials-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konten Publik</p>
            <h1 id="admin-testimonials-title">Testimoni</h1>
            <p class="admin-page-subtitle">Kelola ulasan dari alumni atau orang tua siswa untuk membangun kepercayaan.</p>
        </div>
        <div class="admin-page-actions">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestiModal">
                <i class="me-2" data-lucide="plus"></i> Tambah Testimoni
            </button>
        </div>
    </header>

    <section class="admin-secondary-panel" aria-labelledby="testimonials-table-title">
        <div class="admin-secondary-panel__header">
            <div>
                <h2 class="admin-section-title" id="testimonials-table-title">Daftar Testimoni</h2>
                <p class="admin-section-subtitle">Atur rating, status, dan narasi yang tampil di halaman publik.</p>
            </div>
        </div>
            <div class="table-responsive admin-table-shell">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Foto</th>
                            <th>Nama & Peran</th>
                            <th>Isi Testimoni</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($testimonials)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fs-1 mb-2" data-lucide="message-square"></i>
                                    <p class="mb-0">Belum ada testimoni.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($testimonials as $index => $testi): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <img src="<?= $testi['photo'] ? ((strpos($testi['photo'], 'http') === 0) ? esc($testi['photo']) : base_url(esc($testi['photo']))) : 'https://ui-avatars.com/api/?name='.urlencode($testi['name']) ?>" class="rounded-circle border shadow-xs object-fit-cover" style="width: 48px; height: 48px;" alt="Photo">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($testi['name']) ?></div>
                                        <small class="text-muted"><?= esc($testi['role']) ?></small>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" title="<?= esc($testi['content']) ?>">
                                            <?= esc($testi['content']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i class="font-size-xs <?= $i <= $testi['rating'] ? 'fas fa-star' : 'far fa-star' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $testi['is_active'] ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $testi['is_active'] ? 'success' : 'danger' ?>">
                                            <?= $testi['is_active'] ? 'Aktif' : 'Non-aktif' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-label-warning me-1" data-bs-toggle="modal" data-bs-target="#editTesti<?= $testi['id'] ?>">
                                            <i data-lucide="edit-3"></i>
                                        </button>
                                        <form action="<?= base_url('admin/testimonials/'.$testi['id'].'/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus testimoni ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-label-danger">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editTesti<?= $testi['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="<?= base_url('admin/testimonials/'.$testi['id'].'/update') ?>" method="POST" enctype="multipart/form-data">
                                            <?= csrf_field() ?>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Testimoni</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nama Lengkap</label>
                                                        <input type="text" name="name" class="form-control" value="<?= esc($testi['name']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Peran / Jabatan</label>
                                                        <input type="text" name="role" class="form-control" value="<?= esc($testi['role']) ?>" placeholder="Contoh: Alumni 2020 / Orang Tua Siswa">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Isi Testimoni</label>
                                                        <textarea name="content" class="form-control" rows="4" required><?= esc($testi['content']) ?></textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Rating (1-5)</label>
                                                            <select name="rating" class="form-select">
                                                                <?php for($i=1; $i<=5; $i++): ?>
                                                                    <option value="<?= $i ?>" <?= $testi['rating'] == $i ? 'selected' : '' ?>><?= $i ?> Bintang</option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Status</label>
                                                            <div class="form-check form-switch mt-2">
                                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= $testi['is_active'] ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Aktif</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Foto (Kosongkan jika tidak diganti)</label>
                                                        <input type="file" name="photo" class="form-control">
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
    </section>
</section>

<!-- Add Modal -->
<div class="modal fade" id="addTestiModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/testimonials/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Testimoni Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required placeholder="Nama pemberi testimoni...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Peran / Jabatan</label>
                        <input type="text" name="role" class="form-control" placeholder="Contoh: Alumni 2020">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Isi Testimoni</label>
                        <textarea name="content" class="form-control" rows="4" required placeholder="Apa kata mereka tentang sekolah?"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Rating (1-5)</label>
                            <select name="rating" class="form-select">
                                <option value="5">5 Bintang</option>
                                <option value="4">4 Bintang</option>
                                <option value="3">3 Bintang</option>
                                <option value="2">2 Bintang</option>
                                <option value="1">1 Bintang</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Profil</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Testimoni</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
