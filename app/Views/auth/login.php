<?= $this->extend('auth/layout_auth') ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-card-header">
        <h2><i data-lucide="log-in"></i> Login</h2>
        <p>Masuk ke akun Anda</p>
    </div>
    <div class="auth-card-body">
        <form method="POST" action="<?= base_url('auth/login') ?>" class="auth-form" novalidate>
            <?= csrf_field() ?>

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
                    <div class="invalid-feedback">
                        <?= $errors['email'] ?>
                    </div>
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
                        placeholder="Masukkan password Anda" 
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password', this)" aria-label="Tampilkan password">
                        <i data-lucide="eye"></i>
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback">
                        <?= $errors['password'] ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Forgot Password Link -->
            <div class="d-flex justify-content-end mb-3">
                <a href="<?= base_url('auth/forgot') ?>" style="font-size:0.8rem;font-weight:600;">Lupa password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100" style="padding:12px;font-size:0.95rem;font-weight:700;">
                <span class="btn-spinner"></span>
                Masuk
            </button>
        </form>

        <!-- Divider -->
        <div class="auth-divider">
            <span>atau</span>
        </div>

        <!-- Register Link -->
        <div class="auth-links">
            Belum punya akun? <a href="<?= base_url('auth/register') ?>">Daftar di sini</a>
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
