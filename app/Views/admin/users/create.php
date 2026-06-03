<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-user-create-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Manajemen Akun</p>
            <h1 id="admin-user-create-title">Tambah Pengguna Baru</h1>
            <p class="admin-page-subtitle">Masukkan informasi akun baru dan tetapkan peran akses yang sesuai.</p>
        </div>
        <div class="admin-page-actions">
            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
                <i class="me-1" data-lucide="arrow-left"></i> Kembali
            </a>
        </div>
    </header>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card admin-secondary-panel shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="user-plus"></i> Form Akun</h2>
                <p class="admin-section-subtitle">Masukkan informasi akun baru di bawah ini.</p>
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

                <form method="POST" action="<?= base_url('admin/users/store') ?>" autocomplete="off">
                    <?= csrf_field() ?>

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i  data-lucide="user"></i></span>
                            <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" placeholder="Masukkan nama lengkap" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i  data-lucide="mail"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" placeholder="nama@domain.com" required autocomplete="new-email">
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold small">Hak Akses / Peran <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i  data-lucide="user-tag"></i></span>
                            <select class="form-select select2" id="role" name="role" required>
                                <option value="" disabled selected>Pilih peran...</option>
                                <?php foreach (($roles ?? []) as $role): ?>
                                    <option value="<?= esc($role['slug']) ?>" <?= old('role') === $role['slug'] ? 'selected' : '' ?>>
                                        <?= esc($role['name']) ?> (<?= esc(ucfirst($role['base_role'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold small">Kata Sandi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i  data-lucide="key"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-bold small">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i  data-lucide="check-double"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi" required>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="mb-4">
                        <div class="form-check form-switch p-0 ps-5 d-flex align-items-center">
                            <input class="form-check-input ms-n5 me-2" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked style="width: 2.5em; height: 1.25em;">
                            <label class="form-check-label fw-bold small" for="is_active">Aktifkan akun ini segera</label>
                        </div>
                        <small class="text-muted d-block mt-1">Jika dinonaktifkan, pengguna tidak dapat masuk ke sistem.</small>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="me-2" data-lucide="save"></i> Simpan Akun
                        </button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
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
