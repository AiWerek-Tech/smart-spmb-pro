<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-statistics-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konten Publik</p>
            <h1 id="admin-statistics-title">Statistik</h1>
            <p class="admin-page-subtitle">Kelola angka-angka penting yang ditampilkan di beranda seperti jumlah guru, alumni, dan capaian sekolah.</p>
        </div>
        <div class="admin-page-actions">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStatModal">
                <i class="me-2" data-lucide="plus"></i> Tambah Statistik
            </button>
        </div>
    </header>

    <section class="admin-secondary-panel" aria-labelledby="statistics-table-title">
        <div class="admin-secondary-panel__header">
            <div>
                <h2 class="admin-section-title" id="statistics-table-title">Daftar Statistik</h2>
                <p class="admin-section-subtitle">Atur angka manual, ikon, urutan, dan status tampil.</p>
            </div>
        </div>
            <div class="table-responsive admin-table-shell">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Ikon</th>
                            <th>Label / Nama Data</th>
                            <th>Nilai (Value)</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fs-1 mb-2" data-lucide="bar-chart-2"></i>
                                    <p class="mb-0">Belum ada data statistik manual.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats as $index => $stat): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded d-inline-flex">
                                            <i data-lucide="<?= $stat['icon'] ?: 'activity' ?>" style="width: 20px; height: 20px;"></i>
                                        </div>
                                    </td>
                                    <td><div class="fw-bold"><?= esc($stat['label']) ?></div></td>
                                    <td><span class="badge bg-light text-dark border fw-800"><?= esc($stat['value']) ?></span></td>
                                    <td><?= $stat['sort_order'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $stat['is_active'] ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $stat['is_active'] ? 'success' : 'danger' ?>">
                                            <?= $stat['is_active'] ? 'Aktif' : 'Non-aktif' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-label-warning me-1" data-bs-toggle="modal" data-bs-target="#editStat<?= $stat['id'] ?>">
                                            <i data-lucide="edit-3"></i>
                                        </button>
                                        <form action="<?= base_url('admin/statistics/'.$stat['id'].'/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus statistik ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-label-danger">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editStat<?= $stat['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="<?= base_url('admin/statistics/'.$stat['id'].'/update') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Statistik</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Label Data</label>
                                                        <input type="text" name="label" class="form-control" value="<?= esc($stat['label']) ?>" required placeholder="Contoh: Jumlah Guru">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nilai (Value)</label>
                                                        <input type="text" name="value" class="form-control" value="<?= esc($stat['value']) ?>" required placeholder="Contoh: 50+">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Ikon (Lucide Name)</label>
                                                        <input type="text" name="icon" class="form-control" value="<?= esc($stat['icon']) ?>" placeholder="Contoh: users, award, school">
                                                        <small class="text-muted">Gunakan nama ikon dari <a href="https://lucide.dev" target="_blank">lucide.dev</a></small>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Urutan</label>
                                                            <input type="number" name="sort_order" class="form-control" value="<?= $stat['sort_order'] ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Status</label>
                                                            <div class="form-check form-switch mt-2">
                                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= $stat['is_active'] ? 'checked' : '' ?>>
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
    </section>
</section>

<!-- Add Modal -->
<div class="modal fade" id="addStatModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/statistics/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Statistik Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Label Data</label>
                        <input type="text" name="label" class="form-control" required placeholder="Contoh: Alumni Sukses">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nilai (Value)</label>
                        <input type="text" name="value" class="form-control" required placeholder="Contoh: 1.000+">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ikon (Lucide Name)</label>
                        <input type="text" name="icon" class="form-control" placeholder="Contoh: graduation-cap">
                        <small class="text-muted">Cari ikon di <a href="https://lucide.dev" target="_blank">lucide.dev</a></small>
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
                    <button type="submit" class="btn btn-primary">Simpan Statistik</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
