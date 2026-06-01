<?= $this->extend('auth/layout_auth') ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-card-header">
        <h2><i data-lucide="lock-keyhole"></i> Reset Password</h2>
        <p>Masukkan password baru Anda</p>
    </div>
    <div class="auth-card-body">
        <form method="POST" action="<?= base_url('auth/reset-password') ?>" class="auth-form" novalidate>
            <?= csrf_field() ?>

            <!-- Token (Hidden) -->
            <input type="hidden" name="token" value="<?= isset($token) ? esc($token) : '' ?>">

            <!-- Password Baru -->
            <div class="form-group">
                <label for="password" class="form-label">Password Baru</label>
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
                <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Password harus minimal 8 karakter dan kuat</small>
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
                        placeholder="Ulangi password baru Anda" 
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

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100" style="padding:12px;font-size:0.95rem;font-weight:700;">
                <span class="btn-spinner"></span>
                <i data-lucide="save" style="width:16px;height:16px;" class="me-1"></i>
                Reset Password
            </button>
        </form>

        <!-- Divider -->
        <div class="auth-divider">
            <span>atau</span>
        </div>

        <!-- Links -->
        <div class="auth-links">
            Ingat password Anda? <a href="<?= base_url('auth/login') ?>">Login di sini</a>
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
