<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in justify-content-center">
    <div class="col-md-8 col-lg-6">
        <!-- Back button -->
        <div class="mb-3">
            <a href="<?= base_url('admin/users') ?>" class="text-decoration-none">
                <i class="me-1" data-lucide="arrow-left"></i> Kembali ke Daftar Pengguna
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title text-primary"><i class="me-2" data-lucide="user-edit"></i> Edit Data Pengguna</h5>
                <small class="text-muted">Perbarui data profil pengguna.</small>
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
                                <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="operator" <?= old('role', $user['role']) === 'operator' ? 'selected' : '' ?>>Operator</option>
                                <option value="pendaftar" <?= old('role', $user['role']) === 'pendaftar' ? 'selected' : '' ?>>Pendaftar (Siswa)</option>
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
</div>
<?= $this->endSection() ?>
