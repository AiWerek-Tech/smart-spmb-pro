<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('additional_css') ?>
<style>
    .access-mode-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .access-mode-card,
    .access-role-card {
        border: 1px solid var(--sp-card-border);
        border-radius: var(--sp-radius-md);
        background: var(--sp-card-bg);
        box-shadow: var(--sp-shadow-xs);
    }

    .access-mode-card {
        padding: 14px;
        cursor: pointer;
        min-height: 132px;
    }

    .access-mode-card input {
        position: absolute;
        opacity: 0;
    }

    .access-mode-card:has(input:checked) {
        border-color: var(--sp-primary);
        box-shadow: 0 0 0 3px rgba(var(--sp-primary-rgb), 0.12);
    }

    .permission-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .permission-item {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        border: 1px solid var(--sp-border-color);
        border-radius: var(--sp-radius-sm);
        padding: 10px;
        min-height: 76px;
        background: rgba(var(--sp-secondary-rgb), 0.02);
    }

    @media (max-width: 767.98px) {
        .access-mode-toolbar {
            align-items: stretch !important;
            flex-direction: column;
        }

        .access-mode-toolbar .btn {
            width: 100%;
        }

        .access-mode-grid,
        .permission-grid {
            grid-template-columns: 1fr;
        }

        .access-role-card .card-body {
            padding: 14px !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-access-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Permission Core</p>
            <h1 id="admin-access-title">Mode & Hak Akses</h1>
            <p class="admin-page-subtitle">Atur mode operasional sekolah dan role permission sebagai fondasi konfigurasi SPMB.</p>
        </div>
        <div class="admin-page-actions">
            <span class="sp-status-pill"><i data-lucide="shield-check"></i> Permission Core</span>
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

    <div class="row g-3">
    <div class="col-12">
        <form action="<?= base_url('admin/access/mode') ?>" method="POST" class="access-role-card admin-secondary-panel admin-secondary-panel__body">
            <?= csrf_field() ?>
            <div class="access-mode-toolbar d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                <div>
                    <h2 class="admin-section-title">Mode Operasional Sekolah</h2>
                    <p class="admin-section-subtitle">Mode ini menjadi preset perilaku sistem untuk tahap pengembangan berikutnya.</p>
                </div>
                <button type="submit" class="btn btn-primary access-mode-save-btn">
                    <i data-lucide="save" class="me-1" style="width:14px;height:14px;"></i> Simpan Mode
                </button>
            </div>

            <div class="access-mode-grid">
                <label class="access-mode-card">
                    <input type="radio" name="school_operational_mode" value="small" <?= ($operationalMode ?? 'small') === 'small' ? 'checked' : '' ?>>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="school" class="text-primary"></i>
                        <strong>Sekolah Kecil</strong>
                    </div>
                    <p class="small text-muted mb-0">Alur pendek, role sederhana, cocok untuk panitia 1-3 operator.</p>
                </label>

                <label class="access-mode-card">
                    <input type="radio" name="school_operational_mode" value="standard" <?= ($operationalMode ?? '') === 'standard' ? 'checked' : '' ?>>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="workflow" class="text-primary"></i>
                        <strong>Sekolah Standar</strong>
                    </div>
                    <p class="small text-muted mb-0">Verifikasi, seleksi, review panitia, dan approval bertahap.</p>
                </label>

                <label class="access-mode-card">
                    <input type="radio" name="school_operational_mode" value="foundation" <?= ($operationalMode ?? '') === 'foundation' ? 'checked' : '' ?>>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="building-2" class="text-primary"></i>
                        <strong>Yayasan / Besar</strong>
                    </div>
                    <p class="small text-muted mb-0">Banyak role, audit kuat, dan siap diperluas ke multi-unit.</p>
                </label>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="access-role-card admin-secondary-panel admin-secondary-panel__body h-100">
            <h2 class="admin-section-title">Tambah Role Baru</h2>
            <p class="admin-section-subtitle mb-3">Role baru memakai base role agar tetap kompatibel dengan dashboard lama.</p>

            <form action="<?= base_url('admin/access/roles/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-bold" for="role_name">Nama Role</label>
                    <input type="text" class="form-control" id="role_name" name="name" value="<?= esc(old('name')) ?>" placeholder="Contoh: Verifikator Prestasi" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold" for="role_slug">Slug</label>
                    <input type="text" class="form-control" id="role_slug" name="slug" value="<?= esc(old('slug')) ?>" placeholder="verifikator-prestasi" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold" for="base_role">Base Role</label>
                    <select class="form-select" id="base_role" name="base_role" required>
                        <option value="operator" <?= old('base_role', 'operator') === 'operator' ? 'selected' : '' ?>>Operator</option>
                        <option value="admin" <?= old('base_role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="pendaftar" <?= old('base_role') === 'pendaftar' ? 'selected' : '' ?>>Pendaftar</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold" for="role_description">Deskripsi</label>
                    <textarea class="form-control" id="role_description" name="description" rows="3" placeholder="Ringkas fungsi role ini"><?= esc(old('description')) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i data-lucide="plus" class="me-1" style="width:16px;height:16px;"></i> Tambah Role
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="d-grid gap-3">
            <?php foreach (($roles ?? []) as $role): ?>
                <?php $assigned = $rolePermissions[(int) $role['id']] ?? []; ?>
                <form action="<?= base_url('admin/access/roles/'.$role['id'].'/update') ?>" method="POST" class="admin-secondary-panel access-role-card js-role-permission-form">
                    <?= csrf_field() ?>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                    <input type="text" class="form-control fw-bold" name="name" value="<?= esc($role['name']) ?>" style="max-width:280px;" required>
                                    <span class="badge bg-label-primary"><?= esc($role['slug']) ?></span>
                                    <?php if ((int) $role['is_system']): ?>
                                        <span class="badge bg-label-success">System</span>
                                    <?php endif; ?>
                                    <span class="badge bg-label-info js-permission-counter"><?= count($assigned) ?> permission</span>
                                </div>
                                <textarea class="form-control" name="description" rows="2" placeholder="Deskripsi role"><?= esc($role['description'] ?? '') ?></textarea>
                            </div>
                            <div style="min-width:170px;">
                                <label class="form-label small fw-bold">Base Role</label>
                                <select class="form-select" name="base_role" <?= (int) $role['is_system'] ? 'disabled' : '' ?>>
                                    <option value="admin" <?= $role['base_role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="operator" <?= $role['base_role'] === 'operator' ? 'selected' : '' ?>>Operator</option>
                                    <option value="pendaftar" <?= $role['base_role'] === 'pendaftar' ? 'selected' : '' ?>>Pendaftar</option>
                                </select>
                                <?php if ((int) $role['is_system']): ?>
                                    <input type="hidden" name="base_role" value="<?= esc($role['base_role']) ?>">
                                <?php endif; ?>
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="active_<?= esc($role['id']) ?>" <?= (int) $role['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label small fw-bold" for="active_<?= esc($role['id']) ?>">Aktif</label>
                                </div>
                            </div>
                        </div>

                        <?php foreach (($permissions ?? []) as $group => $items): ?>
                            <div class="mb-3">
                                <div class="small fw-bold text-muted text-uppercase mb-2"><?= esc($group) ?></div>
                                <div class="permission-grid">
                                    <?php foreach ($items as $permission): ?>
                                        <?php
                                            $isProtectedPermission = in_array($permission['permission_key'], ['approve_selection', 'publish_selection'], true);
                                            $canUseProtectedPermission = in_array($role['slug'], ['admin', 'super_admin', 'kepala_sekolah'], true);
                                            $isDisabled = $isProtectedPermission && ! $canUseProtectedPermission;
                                        ?>
                                        <label class="permission-item">
                                            <input class="form-check-input mt-1 js-permission-checkbox" type="checkbox" name="permissions[]" value="<?= esc($permission['permission_key']) ?>" <?= in_array($permission['permission_key'], $assigned, true) ? 'checked' : '' ?> <?= $isDisabled ? 'disabled' : '' ?>>
                                            <span>
                                                <span class="d-block fw-semibold small"><?= esc($permission['name']) ?></span>
                                                <span class="d-block text-muted" style="font-size:0.75rem;line-height:1.3;"><?= esc($permission['description']) ?></span>
                                                <?php if ($isDisabled): ?>
                                                    <span class="d-block text-danger" style="font-size:0.72rem;">Khusus Kepala Sekolah atau Super Admin.</span>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-end gap-2 flex-wrap">
                        <button type="submit" formaction="<?= base_url('admin/access/roles/'.$role['id'].'/duplicate') ?>" class="btn btn-outline-secondary btn-sm">
                            <i data-lucide="copy" class="me-1" style="width:14px;height:14px;"></i> Duplikasi
                        </button>
                        <?php if (! (int) $role['is_system']): ?>
                            <button type="submit" formaction="<?= base_url('admin/access/roles/'.$role['id'].'/delete') ?>" class="btn btn-outline-danger btn-sm delete-confirm">
                                <i data-lucide="trash-2" class="me-1" style="width:14px;height:14px;"></i> Hapus
                            </button>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i data-lucide="save" class="me-1" style="width:14px;height:14px;"></i> Simpan Role
                        </button>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.querySelectorAll('.js-role-permission-form').forEach((form) => {
        const counter = form.querySelector('.js-permission-counter');
        const checkboxes = form.querySelectorAll('.js-permission-checkbox');

        const updateCounter = () => {
            const total = Array.from(checkboxes).filter((checkbox) => checkbox.checked && !checkbox.disabled).length;
            if (counter) {
                counter.textContent = `${total} permission`;
            }
        };

        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateCounter));
        updateCounter();
    });
</script>
<?= $this->endSection() ?>
