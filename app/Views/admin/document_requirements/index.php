<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-docreq-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Data SPMB</p>
            <h1 id="admin-docreq-title">Syarat Dokumen</h1>
            <p class="admin-page-subtitle">Atur dokumen wajib/opsional berdasarkan tahun pelajaran dan jalur pendaftaran.</p>
        </div>
        <div class="admin-page-actions">
            <span class="sp-status-pill"><i data-lucide="calendar-range"></i> <?= esc($activeYear ?? '-') ?></span>
        </div>
    </header>

    <?php if (session()->has('errors')): ?>
        <div>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="admin-page-grid admin-page-grid--settings">
        <aside class="admin-secondary-panel">
            <div class="admin-secondary-panel__body">
                <h2 class="admin-section-title">Scope Jalur</h2>
                <p class="admin-section-subtitle mb-3">Kosongkan jalur untuk aturan global. Aturan jalur tertentu akan menimpa label/setting global pada jenis dokumen yang sama.</p>
                <form method="GET" action="<?= base_url('admin/document-requirements') ?>" class="admin-form-stack mb-4">
                    <select class="form-select mb-2" name="jalur">
                        <option value="">Global semua jalur</option>
                        <?php foreach (($jalurOptions ?? []) as $jalur): ?>
                            <option value="<?= esc($jalur['id']) ?>" <?= (string) ($selectedJalurId ?? '') === (string) $jalur['id'] ? 'selected' : '' ?>>
                                <?= esc($jalur['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-outline-primary w-100" type="submit">
                        <i data-lucide="filter" class="me-1" style="width:14px;height:14px;"></i> Tampilkan
                    </button>
                </form>

                <h2 class="admin-section-title mb-3">Tambah Dokumen</h2>
                <form method="POST" action="<?= base_url('admin/document-requirements/store') ?>" class="admin-form-stack">
                    <?= csrf_field() ?>
                    <input type="hidden" name="jalur_id" value="<?= esc($selectedJalurId ?? '') ?>">
                    <div>
                        <label class="form-label small fw-bold" for="document_type">Kode Dokumen</label>
                        <input type="text" class="form-control" id="document_type" name="document_type" value="<?= esc(old('document_type')) ?>" placeholder="contoh: skl, surat_rekomendasi" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold" for="label">Nama Dokumen</label>
                        <input type="text" class="form-control" id="label" name="label" value="<?= esc(old('label')) ?>" placeholder="Surat Keterangan Lulus" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold" for="allowed_extensions">Format</label>
                        <input type="text" class="form-control" id="allowed_extensions" name="allowed_extensions" value="<?= esc(old('allowed_extensions', 'pdf,jpg,jpeg,png')) ?>" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold" for="max_size_kb">Maks KB</label>
                            <input type="number" class="form-control" id="max_size_kb" name="max_size_kb" value="<?= esc(old('max_size_kb', '2048')) ?>" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold" for="sort_order">Urutan</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', '100')) ?>">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_required" id="is_required" checked>
                        <label class="form-check-label small fw-bold" for="is_required">Wajib diunggah</label>
                    </div>
                    <div class="form-check form-switch mt-2 mb-3">
                        <input class="form-check-input" type="checkbox" name="requires_verification" id="requires_verification" checked>
                        <label class="form-check-label small fw-bold" for="requires_verification">Butuh verifikasi operator</label>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">
                        <i data-lucide="plus" class="me-1" style="width:14px;height:14px;"></i> Tambah Syarat
                    </button>
                </form>
            </div>
        </aside>

        <section class="admin-record-list" aria-label="Daftar syarat dokumen">
            <?php if (empty($requirements)): ?>
                <div class="admin-secondary-panel">
                    <div class="card-body text-center text-muted py-5">
                        <i data-lucide="file-question" class="mb-2" style="width:42px;height:42px;"></i>
                        <p class="mb-0">Belum ada syarat dokumen pada scope ini.</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach (($requirements ?? []) as $requirement): ?>
                <form method="POST" action="<?= base_url('admin/document-requirements/'.$requirement['id'].'/update') ?>" class="admin-secondary-panel">
                    <?= csrf_field() ?>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                    <span class="badge bg-label-primary"><?= esc($requirement['document_type']) ?></span>
                                    <?php if ((int) $requirement['is_required']): ?><span class="badge bg-label-danger">Wajib</span><?php endif; ?>
                                    <?php if ((int) $requirement['jalur_id']): ?><span class="badge bg-label-info">Khusus Jalur</span><?php else: ?><span class="badge bg-label-success">Global</span><?php endif; ?>
                                </div>
                                <input type="text" class="form-control fw-bold mb-2" name="label" value="<?= esc($requirement['label']) ?>" required>
                                <textarea class="form-control" name="description" rows="2" placeholder="Deskripsi singkat atau catatan untuk pendaftar"><?= esc($requirement['description'] ?? '') ?></textarea>
                            </div>
                            <div style="min-width:180px;">
                                <label class="form-label small fw-bold">Format</label>
                                <input type="text" class="form-control mb-2" name="allowed_extensions" value="<?= esc($requirement['allowed_extensions']) ?>" required>
                                <label class="form-label small fw-bold">Maks KB</label>
                                <input type="number" class="form-control" name="max_size_kb" value="<?= esc($requirement['max_size_kb']) ?>" min="1" required>
                            </div>
                        </div>

                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Urutan</label>
                                <input type="number" class="form-control" name="sort_order" value="<?= esc($requirement['sort_order']) ?>">
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_required" id="required_<?= esc($requirement['id']) ?>" <?= (int) $requirement['is_required'] ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-bold" for="required_<?= esc($requirement['id']) ?>">Wajib</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="requires_verification" id="verify_<?= esc($requirement['id']) ?>" <?= (int) $requirement['requires_verification'] ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-bold" for="verify_<?= esc($requirement['id']) ?>">Verifikasi</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="active_<?= esc($requirement['id']) ?>" <?= (int) $requirement['is_active'] ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-bold" for="active_<?= esc($requirement['id']) ?>">Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="admin-secondary-panel__footer justify-content-end">
                        <button type="submit" class="btn btn-outline-primary">
                            <i data-lucide="save" class="me-1" style="width:14px;height:14px;"></i> Simpan
                        </button>
                    </div>
                </form>
                <form method="POST" action="<?= base_url('admin/document-requirements/'.$requirement['id'].'/delete') ?>" class="text-end mt-n2">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-link text-danger btn-sm delete-confirm">Hapus syarat ini</button>
                </form>
            <?php endforeach; ?>
        </section>
    </div>
</section>
<?= $this->endSection() ?>
