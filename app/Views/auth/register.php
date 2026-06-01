<?= $this->extend('auth/layout_auth') ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-card-header">
        <h2><i data-lucide="user-plus"></i> Daftar Akun</h2>
        <p>Buat akun baru untuk pendaftaran</p>
    </div>
    <div class="auth-card-body">
        <form method="POST" action="<?= base_url('auth/register') ?>" class="auth-form" novalidate>
            <?= csrf_field() ?>

            <!-- Nama Lengkap -->
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input 
                    type="text" 
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                    id="name" 
                    name="name" 
                    value="<?= old('name') ?>" 
                    placeholder="Masukkan nama lengkap Anda" 
                    required
                    autocomplete="name"
                >
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                    id="email" 
                    name="email" 
                    value="<?= old('email') ?>" 
                    placeholder="nama@example.com" 
                    required
                    autocomplete="email"
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="auth-password-wrapper">
                    <input 
                        type="password" 
                        class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                        id="password" 
                        name="password" 
                        placeholder="Minimal 8 karakter" 
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password', this)" aria-label="Tampilkan password">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                <?php endif; ?>
                <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Password harus minimal 8 karakter</small>
            </div>

            <!-- Konfirmasi Password -->
            <div class="form-group">
                <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                <div class="auth-password-wrapper">
                    <input 
                        type="password" 
                        class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="Ulangi password Anda" 
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password_confirm', this)" aria-label="Tampilkan password">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="invalid-feedback"><?= $errors['password_confirm'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Terms Checkbox -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                <label class="form-check-label" for="terms" style="font-size:0.8rem;">
                    Saya setuju dengan <a href="<?= base_url('/syarat-ketentuan') ?>" target="_blank" rel="noopener">Syarat dan Ketentuan</a>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100" style="padding:12px;font-size:0.95rem;font-weight:700;">
                <span class="btn-spinner"></span>
                Daftar Akun
            </button>
        </form>

        <!-- Divider -->
        <div class="auth-divider">
            <span>atau</span>
        </div>

        <!-- Login Link -->
        <div class="auth-links">
            Sudah punya akun? <a href="<?= base_url('auth/login') ?>">Login di sini</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.querySelector('i').setAttribute('data-lucide', 'eye-off');
    } else {
        input.type = 'password';
        btn.querySelector('i').setAttribute('data-lucide', 'eye');
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}
</script>
<?= $this->endSection() ?>
