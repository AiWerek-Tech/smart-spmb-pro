<?= $this->extend('auth/layout_auth') ?>

<?= $this->section('content') ?>
<div class="auth-card">
    <div class="auth-card-header">
        <h2><i data-lucide="key-round"></i> Lupa Password</h2>
        <p>Kami akan mengirimkan link reset ke email Anda</p>
    </div>
    <div class="auth-card-body">
        <p style="font-size:0.85rem;color:var(--sp-text-muted);margin-bottom:var(--sp-space-lg);">
            Masukkan email akun Anda dan kami akan mengirimkan link untuk mereset password ke email Anda.
        </p>

        <form method="POST" action="<?= base_url('auth/send-reset') ?>" class="auth-form" novalidate>
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
                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100" style="padding:12px;font-size:0.95rem;font-weight:700;">
                <span class="btn-spinner"></span>
                <i data-lucide="mail" style="width:16px;height:16px;" class="me-1"></i>
                Kirim Link Reset
            </button>
        </form>

        <!-- Divider -->
        <div class="auth-divider">
            <span>atau</span>
        </div>

        <!-- Links -->
        <div class="auth-links" style="margin-bottom:8px;">
            Ingat password Anda? <a href="<?= base_url('auth/login') ?>">Login di sini</a>
        </div>
        <div class="auth-links">
            Belum punya akun? <a href="<?= base_url('auth/register') ?>">Daftar di sini</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
