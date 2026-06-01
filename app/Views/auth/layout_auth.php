<?php
$settingModel = new \App\Models\SettingModel();
$globalThemeColor = $settingModel->getValue('theme_color', 'purple');
$appInfo = config('AppInfo');
?>
<!DOCTYPE html>
<html lang="id" data-theme-color="<?= esc($globalThemeColor) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart SPMB Pro v<?= esc($appInfo->version) ?> - Sistem Penerimaan Murid Baru Profesional">
    <meta name="author" content="<?= esc($appInfo->developer) ?>">
    <meta name="application-name" content="<?= esc($appInfo->name) ?>">
    <meta name="version" content="<?= esc($appInfo->version) ?>">
    <meta name="theme-color" content="">
    <title><?= $title ?? 'Autentikasi' ?> - Smart SPMB Pro</title>

    <script src="<?= base_url('assets/js/theme-sync.js') ?>"></script>
    <script>
        SpTheme.init({ serverTheme: '<?= esc($globalThemeColor) ?>', scope: 'public' });
    </script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Smart SPMB Pro — Foundation + Auth Styles -->
    <link href="<?= base_url('assets/css/foundation.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/auth.css') ?>" rel="stylesheet">
    <!-- Lucide Icons with local fallback -->
    <script src="https://unpkg.com/lucide@0.309.0/dist/umd/lucide.min.js"></script>
    <script>
        if (typeof lucide === 'undefined') {
            document.write('<script src="<?= base_url('assets/js/lucide.min.js') ?>"><\/script>');
        }
    </script>
</head>
<body>
    <script>SpTheme.initDarkMode();</script>

    <div class="auth-wrapper">
        <!-- Brand Panel (Left Side — hidden on mobile) -->
        <div class="auth-brand-panel">
            <div class="auth-brand-content">
                <div class="auth-brand-logo">
                    <i data-lucide="graduation-cap"></i>
                </div>
                <h1 class="auth-brand-title">Smart SPMB Pro</h1>
                <p class="auth-brand-subtitle">
                    Platform penerimaan murid baru yang modern, efisien, dan terintegrasi untuk sekolah Anda.
                </p>
                <ul class="auth-brand-features">
                    <li>
                        <i data-lucide="shield-check"></i>
                        Keamanan data terenkripsi
                    </li>
                    <li>
                        <i data-lucide="zap"></i>
                        Proses pendaftaran cepat & mudah
                    </li>
                    <li>
                        <i data-lucide="bar-chart-3"></i>
                        Dashboard analitik real-time
                    </li>
                    <li>
                        <i data-lucide="smartphone"></i>
                        Akses dari mana saja
                    </li>
                </ul>
            </div>
        </div>

        <!-- Form Panel (Right Side) -->
        <div class="auth-form-panel">
            <button type="button" class="auth-theme-toggle" id="auth-theme-toggle" title="Ganti Tema" aria-label="Toggle tema gelap">
                <i data-lucide="moon" id="auth-theme-toggle-icon"></i>
            </button>
            <div class="auth-form-container">
                <!-- Mobile Brand (shown on small screens) -->
                <div class="auth-mobile-brand">
                    <div class="auth-mobile-brand-logo">
                        <i data-lucide="graduation-cap"></i>
                    </div>
                    <h3>Smart SPMB Pro</h3>
                </div>

                <!-- Flash Alerts -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="auth-alert auth-alert-error" role="alert">
                        <i data-lucide="alert-circle"></i>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="auth-alert auth-alert-success" role="alert">
                        <i data-lucide="check-circle-2"></i>
                        <div><?= esc(session()->getFlashdata('success')) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('info')): ?>
                    <div class="auth-alert auth-alert-info" role="alert">
                        <i data-lucide="info"></i>
                        <div><?= esc(session()->getFlashdata('info')) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Content slot for child views -->
                <main role="main">
                    <?= $this->renderSection('content') ?>
                </main>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        SpTheme.bindDarkModeToggle(
            document.getElementById('auth-theme-toggle'),
            document.getElementById('auth-theme-toggle-icon')
        );
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
