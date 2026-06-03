<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="sp-admin-page admin-page-shell animate-fade-in" aria-labelledby="admin-teachers-title">
    <header class="sp-page-toolbar admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konten Publik</p>
            <h1 id="admin-teachers-title">Tenaga Pendidik</h1>
            <p class="admin-page-subtitle">Kelola data guru dan staf pengajar yang tampil pada halaman profil sekolah.</p>
        </div>
        <div class="sp-toolbar-actions admin-page-actions">
            <span class="sp-status-pill"><i data-lucide="calendar"></i> <?= esc($activeYear ?? '-') ?></span>
            <a href="<?= base_url('admin/content') ?>" class="btn btn-outline-primary"><i data-lucide="school"></i> Profil</a>
            <a href="<?= base_url('admin/gallery') ?>" class="btn btn-outline-primary"><i data-lucide="image"></i> Galeri</a>
        </div>
    </header>

    <div class="row g-3">
        <div class="col-xl-4">
            <div class="card admin-secondary-panel shadow-sm border">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="user-plus"></i> Tambah Tenaga Pendidik</h2>
                    <p class="admin-section-subtitle">Data baru otomatis mengikuti tahun pelajaran aktif.</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('admin/content/teachers/store') ?>" enctype="multipart/form-data" class="sp-compact-form">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Jabatan / Mata Pelajaran</label>
                            <input type="text" class="form-control" name="role" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Foto</label>
                            <input type="file" class="form-control" name="teacher_photo" accept="image/*">
                        </div>
                        <div class="row g-2 align-items-end">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Urutan</label>
                                <input type="number" class="form-control" name="sort_order" value="0" min="0">
                            </div>
                            <div class="col-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="new_teacher_active" checked>
                                    <label class="form-check-label small" for="new_teacher_active">Aktif</label>
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
                        <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="users"></i> Daftar Tenaga Pendidik</h2>
                        <p class="admin-section-subtitle">Arsip tahun pelajaran <?= esc($activeYear ?? '-') ?>.</p>
                    </div>
                    <span class="sp-status-pill"><?= count($teachers ?? []) ?> data</span>
                </div>
                <div class="card-body">
                    <?php if (empty($teachers)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fs-1 mb-2" data-lucide="users"></i>
                            <p class="mb-0">Belum ada data tenaga pendidik untuk tahun ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="sp-mobile-records is-always">
                            <?php foreach ($teachers as $teacher): ?>
                                <?php
                                    $teacherPhoto = !empty($teacher['photo'])
                                        ? ((strpos($teacher['photo'], 'http') === 0) ? esc($teacher['photo']) : base_url(esc($teacher['photo'])))
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($teacher['name'] ?? 'Guru') . '&background=6366f1&color=fff&size=96';
                                ?>
                                <div class="sp-mobile-record">
                                    <div class="d-flex align-items-center gap-3 min-w-0">
                                        <img src="<?= $teacherPhoto ?>" alt="<?= esc($teacher['name']) ?>" class="rounded-circle object-fit-cover border" style="width:42px;height:42px;" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($teacher['name'] ?? 'Guru') ?>&background=6366f1&color=fff&size=96'">
                                        <div class="min-w-0">
                                            <div class="sp-record-title text-truncate"><?= esc($teacher['name']) ?></div>
                                            <div class="sp-record-meta text-truncate"><?= esc($teacher['role']) ?></div>
                                        </div>
                                    </div>
                                    <div class="sp-record-actions">
                                        <span class="badge <?= ($teacher['is_active'] ?? 0) ? 'bg-success' : 'bg-secondary' ?>"><?= ($teacher['is_active'] ?? 0) ? 'Aktif' : 'Nonaktif' ?></span>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editTeacherModal<?= $teacher['id'] ?>" title="Edit">
                                            <i data-lucide="pencil"></i>
                                        </button>
                                        <form action="<?= base_url('admin/content/teachers/'.$teacher['id'].'/delete') ?>" method="POST" onsubmit="return confirm('Hapus data tenaga pendidik ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="modal fade" id="editTeacherModal<?= $teacher['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST" action="<?= base_url('admin/content/teachers/'.$teacher['id'].'/update') ?>" enctype="multipart/form-data">
                                                <?= csrf_field() ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Tenaga Pendidik</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Nama Lengkap</label>
                                                        <input type="text" class="form-control" name="name" value="<?= esc($teacher['name']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Jabatan / Mata Pelajaran</label>
                                                        <input type="text" class="form-control" name="role" value="<?= esc($teacher['role']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Ganti Foto</label>
                                                        <input type="file" class="form-control" name="teacher_photo" accept="image/*">
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold">Urutan</label>
                                                            <input type="number" class="form-control" name="sort_order" value="<?= (int) ($teacher['sort_order'] ?? 0) ?>" min="0">
                                                        </div>
                                                        <div class="col-6 d-flex align-items-end">
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="teacher-active-<?= $teacher['id'] ?>" <?= ($teacher['is_active'] ?? 0) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="teacher-active-<?= $teacher['id'] ?>">Aktif</label>
                                                            </div>
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
