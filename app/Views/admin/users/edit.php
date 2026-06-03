<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-user-edit-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Manajemen Akun</p>
            <h1 id="admin-user-edit-title">Edit Data Pengguna</h1>
            <p class="admin-page-subtitle">Perbarui profil pengguna, role tambahan, dan izin efektif akun.</p>
        </div>
        <div class="admin-page-actions">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
                <i class="me-1" data-lucide="arrow-left"></i> Kembali
            </a>
        </div>
    </header>

<div class="row justify-content-center g-3">
    <div class="col-md-8 col-lg-6">
        <div class="card admin-secondary-panel shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="user-edit"></i> Form Pengguna</h2>
                <p class="admin-section-subtitle">Perbarui data profil pengguna.</p>
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

                <form method="POST" action="<?= base_url('admin/users/'.$user['id'].'/update') ?>" autocomplete="off">
                    <?= csrf_field() ?>

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i  data-lucide="user"></i></span>
                            <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $user['name']) ?>" placeholder="Masukkan nama lengkap" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i  data-lucide="mail"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" placeholder="nama@domain.com" required autocomplete="new-email">
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold small">Hak Akses / Peran <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i  data-lucide="user-tag"></i></span>
                            <select class="form-select select2" id="role" name="role" required>
                                <?php foreach (($roles ?? []) as $role): ?>
                                    <option value="<?= esc($role['slug']) ?>" <?= old('role', $user['role']) === $role['slug'] ? 'selected' : '' ?>>
                                        <?= esc($role['name']) ?> (<?= esc(ucfirst($role['base_role'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded border mb-4">
                        <h6 class="text-dark fw-bold small mb-2"><i class="me-1 text-primary" data-lucide="shield-alt"></i> Ganti Kata Sandi</h6>
                        <p class="text-muted small mb-3">Biarkan kosong jika Anda tidak berniat mengubah kata sandi pengguna ini.</p>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold small">Kata Sandi Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i  data-lucide="key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password">
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-0">
                            <label for="confirm_password" class="form-label fw-bold small">Konfirmasi Kata Sandi Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i  data-lucide="check-double"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi">
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="mb-4">
                        <div class="form-check form-switch p-0 ps-5 d-flex align-items-center">
                            <input class="form-check-input ms-n5 me-2" type="checkbox" role="switch" id="is_active" name="is_active" value="1" <?= old('is_active', $user['is_active']) ? 'checked' : '' ?> style="width: 2.5em; height: 1.25em;">
                            <label class="form-check-label fw-bold small" for="is_active">Aktifkan akun ini segera</label>
                        </div>
                        <small class="text-muted d-block mt-1">Jika dinonaktifkan, pengguna tidak dapat masuk ke sistem.</small>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="me-2" data-lucide="save"></i> Perbarui Akun
                        </button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8 col-lg-4">
        <div class="card admin-secondary-panel shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="admin-section-title text-primary mb-1"><i class="me-2" data-lucide="shield-check"></i> Role Tambahan</h2>
                <p class="admin-section-subtitle">Role efektif digabung dari role utama dan assignment aktif.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/users/'.$user['id'].'/roles/assign') ?>" class="mb-3">
                    <?= csrf_field() ?>
                    <label class="form-label fw-bold small" for="role_id">Assign Role</label>
                    <select class="form-select mb-2" id="role_id" name="role_id" required>
                        <option value="">Pilih role aktif</option>
                        <?php foreach (($roles ?? []) as $role): ?>
                            <option value="<?= esc($role['id']) ?>"><?= esc($role['name']) ?> (<?= esc($role['slug']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <label class="form-label fw-bold small" for="expires_at">Kedaluwarsa</label>
                    <input type="date" class="form-control mb-3" id="expires_at" name="expires_at">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="me-1" data-lucide="plus"></i> Assign Role
                    </button>
                </form>

                <div class="d-grid gap-2">
                    <?php foreach (($assignedRoles ?? []) as $assignedRole): ?>
                        <?php $isPrimaryRole = ($assignedRole['name'] ?? '') === ($user['role'] ?? ''); ?>
                        <div class="border rounded p-2">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold"><?= esc($assignedRole['display_name'] ?? $assignedRole['name']) ?></div>
                                    <div class="small text-muted">
                                        <?= $isPrimaryRole ? 'Role utama' : 'Role tambahan' ?>
                                        <?php if (! empty($assignedRole['expires_at'])): ?>
                                            &middot; sampai <?= date('d M Y', strtotime($assignedRole['expires_at'])) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (! $isPrimaryRole): ?>
                                    <form method="POST" action="<?= base_url('admin/users/'.$user['id'].'/roles/'.($assignedRole['id'] ?? 0).'/revoke') ?>" class="m-0">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cabut role">
                                            <i data-lucide="x" style="width:14px;height:14px;"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($assignedRoles)): ?>
                        <div class="text-muted small border rounded p-3">Belum ada assignment role tambahan.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card admin-secondary-panel shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="admin-section-title text-primary mb-1"><i class="me-2" data-lucide="key-round"></i> Permission Efektif</h2>
                <p class="admin-section-subtitle">Gabungan seluruh permission aktif user.</p>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1">
                    <?php foreach (array_slice(($effectivePermissions ?? []), 0, 40) as $permission): ?>
                        <span class="badge bg-label-secondary"><?= esc($permission) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php if (count($effectivePermissions ?? []) > 40): ?>
                    <div class="small text-muted mt-2">+<?= count($effectivePermissions) - 40 ?> permission lainnya.</div>
                <?php elseif (empty($effectivePermissions)): ?>
                    <div class="small text-muted">User belum memiliki permission aktif.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</section>
<?= $this->endSection() ?>
