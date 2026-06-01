<?php
$settingModel = new \App\Models\SettingModel();
$globalThemeColor = $settingModel->getValue('theme_color', 'purple');
$schoolName = $settingModel->getValue('school_name', 'SMP Nusantara Mandiri');
$schoolTagline = $settingModel->getValue('tagline', 'Sekolah Berkarakter & Berprestasi');
$schoolLogo = $settingModel->getValue('school_logo', '');
$footerPhone = $settingModel->getValue('phone', 'Kontak belum dikonfigurasi');
$footerEmail = $settingModel->getValue('email', 'Email belum dikonfigurasi');
$footerAddress = $settingModel->getValue('address', 'Alamat sekolah belum dikonfigurasi');
$footerWhatsapp = preg_replace('/[^0-9]/', '', (string) $settingModel->getValue('whatsapp', '6282190822641'));
$footerAccreditation = $settingModel->getValue('accreditation', 'A');
$footerNpsn = $settingModel->getValue('npsn', '-');
$footerDesc = $settingModel->getValue('school_description', 'Membentuk generasi cerdas, berkarakter, dan siap menghadapi tantangan global dengan sistem pendidikan inovatif.');
$appInfo = config('AppInfo');

// URL logo yang sudah diproses — digunakan di navbar, footer, favicon, og:image
$schoolLogoUrl = !empty($schoolLogo) ? base_url($schoolLogo) : '';
?>
<!DOCTYPE html>
<html lang="id" data-theme-color="<?= esc($globalThemeColor) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="Smart SPMB Pro v<?= esc($appInfo->version) ?> - Sistem Penerimaan Murid Baru Online yang modern dan profesional">
    <meta name="author" content="<?= esc($appInfo->developer) ?>">
    <meta name="application-name" content="<?= esc($appInfo->name) ?>">
    <meta name="version" content="<?= esc($appInfo->version) ?>">
    
    <!-- Mobile Native Optimization -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="">
    
    <title><?= $title ?? 'Smart SPMB Pro' ?> - Sistem Penerimaan Siswa Baru</title>

    <!-- Favicon — gunakan logo sekolah jika tersedia -->
    <?php if ($schoolLogoUrl): ?>
        <link rel="icon" type="image/x-icon" href="<?= esc($schoolLogoUrl) ?>">
        <link rel="apple-touch-icon" href="<?= esc($schoolLogoUrl) ?>">
    <?php else: ?>
        <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/img/favicon.svg') ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:title" content="<?= esc($schoolName) ?> — Smart SPMB Pro">
    <meta property="og:description" content="<?= esc($footerDesc) ?>">
    <?php if ($schoolLogoUrl): ?>
        <meta property="og:image" content="<?= esc($schoolLogoUrl) ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    
    <script src="<?= base_url('assets/js/theme-sync.js') ?>"></script>
    <script>
        SpTheme.init({ serverTheme: '<?= esc($globalThemeColor) ?>', scope: 'public' });
    </script>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins, Inter & Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Smart SPMB Pro — Foundation + Public Styles -->
    <link href="<?= base_url('assets/css/foundation.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/public.css') ?>" rel="stylesheet">

    <!-- Lucide Icons with local fallback -->
    <script src="https://unpkg.com/lucide@0.309.0/dist/umd/lucide.min.js"></script>
    <script>
        if (typeof lucide === 'undefined') {
            document.write('<script src="<?= base_url('assets/js/lucide.min.js') ?>"><\/script>');
        }
    </script>

    <?= $this->renderSection('additional_css') ?>
</head>
<body>
    <script>SpTheme.initDarkMode();</script>
    <!-- Skip Navigation -->
    <a href="#main-content" class="sp-skip-link">Lewati ke konten utama</a>

    <!-- Sticky Top App Bar -->
    <nav class="sp-public-navbar fixed-top" id="main-navbar" aria-label="Navigasi utama">
        <div class="container sp-nav-container">
            <a class="navbar-brand" href="<?= base_url('/') ?>" aria-label="Beranda <?= esc($schoolName) ?>">
                <div class="sp-brand-icon<?= $schoolLogoUrl ? ' sp-brand-icon--logo' : '' ?>">
                    <?php if ($schoolLogoUrl): ?>
                        <img src="<?= esc($schoolLogoUrl) ?>" alt="Logo <?= esc($schoolName) ?>" class="sp-brand-logo-img">
                    <?php else: ?>
                        <i data-lucide="graduation-cap"></i>
                    <?php endif; ?>
                </div>
                <div class="brand-text-wrapper">
                    <span class="brand-name"><?= esc($schoolName) ?></span>
                    <span class="brand-tagline d-none d-sm-block"><?= esc($schoolTagline) ?></span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <div class="sp-desktop-menu d-none d-lg-flex">
                <ul class="sp-nav-list" role="menubar">
                    <li class="sp-nav-item" role="none">
                        <a href="<?= base_url('/') ?>" class="sp-nav-link <?= current_url() == base_url('/') ? 'active' : '' ?>" role="menuitem">Beranda</a>
                    </li>
                    <li class="sp-nav-item sp-nav-dropdown dropdown" role="none">
                        <a href="#" class="sp-nav-link dropdown-toggle" role="menuitem" aria-expanded="false" aria-haspopup="true">
                            Profil Sekolah
                            <i data-lucide="chevron-down" class="sp-nav-chevron"></i>
                        </a>
                        <ul class="dropdown-menu sp-nav-dropdown-menu shadow-lg border-0" role="menu">
                            <li><a class="dropdown-item" href="<?= base_url('/profil/sejarah') ?>"><i data-lucide="book-open"></i> Sejarah & Visi Misi</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/profil/fasilitas') ?>"><i data-lucide="building-2"></i> Fasilitas Sekolah</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/profil/guru') ?>"><i data-lucide="users"></i> Tenaga Pendidik</a></li>
                        </ul>
                    </li>
                    <li class="sp-nav-item sp-nav-dropdown dropdown" role="none">
                        <a href="#" class="sp-nav-link dropdown-toggle" role="menuitem" aria-expanded="false" aria-haspopup="true">
                            Informasi
                            <i data-lucide="chevron-down" class="sp-nav-chevron"></i>
                        </a>
                        <ul class="dropdown-menu sp-nav-dropdown-menu shadow-lg border-0" role="menu">
                            <li><a class="dropdown-item" href="<?= base_url('/spmb') ?>"><i data-lucide="file-text"></i> Panduan Pendaftaran</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/hasil-seleksi') ?>"><i data-lucide="search"></i> Hasil Seleksi</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/biaya') ?>"><i data-lucide="wallet"></i> Informasi Biaya</a></li>
                        </ul>
                    </li>
                    <li class="sp-nav-item" role="none">
                        <a href="<?= base_url('/galeri') ?>" class="sp-nav-link" role="menuitem">Galeri</a>
                    </li>
                    <li class="sp-nav-item" role="none">
                        <a href="<?= base_url('/kontak') ?>" class="sp-nav-link" role="menuitem">Kontak</a>
                    </li>
                </ul>
            </div>

            <div class="sp-navbar-actions">
                <button class="sp-nav-icon-btn" id="public-theme-toggle" type="button" title="Ganti Tema" aria-label="Toggle tema gelap">
                    <i data-lucide="moon" id="public-theme-toggle-icon"></i>
                </button>
                <?php if (session()->has('user_id')): ?>
                    <a href="<?= base_url('pendaftar/dashboard') ?>" class="btn btn-primary sp-nav-cta d-none d-sm-inline-flex">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-primary sp-nav-cta d-none d-sm-inline-flex">
                        <i data-lucide="log-in"></i>
                        <span>Login</span>
                    </a>
                <?php endif; ?>
                <button class="sp-nav-icon-btn sp-nav-menu-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenuDrawer" aria-controls="mobileMenuDrawer" aria-label="Buka menu">
                    <i data-lucide="menu"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation Drawer -->
    <div class="offcanvas offcanvas-end sp-mobile-offcanvas d-lg-none" tabindex="-1" id="mobileMenuDrawer" aria-labelledby="mobileMenuDrawerLabel">
        <div class="offcanvas-header">
            <div class="sp-mobile-drawer-brand">
                <div class="sp-brand-icon sp-brand-icon-sm<?= $schoolLogoUrl ? ' sp-brand-icon--logo' : '' ?>">
                    <?php if ($schoolLogoUrl): ?>
                        <img src="<?= esc($schoolLogoUrl) ?>" alt="Logo <?= esc($schoolName) ?>" class="sp-brand-logo-img">
                    <?php else: ?>
                        <i data-lucide="graduation-cap"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h5 class="offcanvas-title mb-0" id="mobileMenuDrawerLabel"><?= esc($schoolName) ?></h5>
                    <small class="text-muted">Menu Navigasi</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <nav class="sp-mobile-nav flex-grow-1" aria-label="Menu mobile">
                <a href="<?= base_url('/') ?>" class="sp-mobile-nav-link <?= current_url() == base_url('/') ? 'active' : '' ?>">
                    <i data-lucide="home"></i>
                    <span>Beranda</span>
                    <i data-lucide="chevron-right" class="sp-mobile-nav-arrow"></i>
                </a>

                <div class="sp-mobile-nav-group">
                    <button class="sp-mobile-nav-link sp-mobile-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavProfil" aria-expanded="false">
                        <i data-lucide="school"></i>
                        <span>Profil Sekolah</span>
                        <i data-lucide="chevron-down" class="sp-mobile-nav-chevron"></i>
                    </button>
                    <div class="collapse sp-mobile-nav-sub" id="mobileNavProfil">
                        <a href="<?= base_url('/profil/sejarah') ?>" class="sp-mobile-nav-sublink">Sejarah & Visi Misi</a>
                        <a href="<?= base_url('/profil/fasilitas') ?>" class="sp-mobile-nav-sublink">Fasilitas Sekolah</a>
                        <a href="<?= base_url('/profil/guru') ?>" class="sp-mobile-nav-sublink">Tenaga Pendidik</a>
                    </div>
                </div>

                <div class="sp-mobile-nav-group">
                    <button class="sp-mobile-nav-link sp-mobile-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavInfo" aria-expanded="false">
                        <i data-lucide="info"></i>
                        <span>Informasi</span>
                        <i data-lucide="chevron-down" class="sp-mobile-nav-chevron"></i>
                    </button>
                    <div class="collapse sp-mobile-nav-sub" id="mobileNavInfo">
                        <a href="<?= base_url('/spmb') ?>" class="sp-mobile-nav-sublink">Panduan Pendaftaran</a>
                        <a href="<?= base_url('/hasil-seleksi') ?>" class="sp-mobile-nav-sublink">Hasil Seleksi</a>
                        <a href="<?= base_url('/biaya') ?>" class="sp-mobile-nav-sublink">Informasi Biaya</a>
                    </div>
                </div>

                <a href="<?= base_url('/galeri') ?>" class="sp-mobile-nav-link">
                    <i data-lucide="images"></i>
                    <span>Galeri</span>
                    <i data-lucide="chevron-right" class="sp-mobile-nav-arrow"></i>
                </a>
                <a href="<?= base_url('/kontak') ?>" class="sp-mobile-nav-link">
                    <i data-lucide="phone"></i>
                    <span>Kontak</span>
                    <i data-lucide="chevron-right" class="sp-mobile-nav-arrow"></i>
                </a>
            </nav>

            <div class="sp-mobile-drawer-footer">
                <?php if (session()->has('user_id')): ?>
                    <a href="<?= base_url('pendaftar/dashboard') ?>" class="btn btn-primary w-100 sp-mobile-cta">
                        <i data-lucide="layout-dashboard"></i> Dashboard Saya
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-primary w-100 sp-mobile-cta">
                        <i data-lucide="log-in"></i> Login / Daftar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="sp-flash-container">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i data-lucide="alert-circle" style="width:16px;height:16px;" class="me-1"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="sp-flash-container">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i data-lucide="check-circle-2" style="width:16px;height:16px;" class="me-1"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Content Section -->
    <main id="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="sp-mobile-bottom-nav d-lg-none" aria-label="Navigasi bawah">
        <a href="<?= base_url('/') ?>" class="mobile-nav-item <?= current_url() == base_url('/') ? 'active' : '' ?>">
            <span class="mobile-nav-icon"><i data-lucide="home"></i></span>
            <span class="mobile-nav-label">Beranda</span>
        </a>
        <a href="<?= base_url('auth/register') ?>" class="mobile-nav-item <?= current_url() == base_url('auth/register') ? 'active' : '' ?>">
            <span class="mobile-nav-icon"><i data-lucide="clipboard-list"></i></span>
            <span class="mobile-nav-label">Daftar</span>
        </a>
        <a href="<?= base_url('/spmb') ?>" class="mobile-nav-item <?= str_contains(current_url(), '/spmb') ? 'active' : '' ?>">
            <span class="mobile-nav-icon"><i data-lucide="info"></i></span>
            <span class="mobile-nav-label">Info</span>
        </a>
        <a href="<?= base_url('/hasil-seleksi') ?>" class="mobile-nav-item <?= str_contains(current_url(), '/hasil-seleksi') ? 'active' : '' ?>">
            <span class="mobile-nav-icon"><i data-lucide="bar-chart-3"></i></span>
            <span class="mobile-nav-label">Status</span>
        </a>
        <?php if (session()->has('user_id')): ?>
            <a href="<?= base_url('pendaftar/dashboard') ?>" class="mobile-nav-item <?= str_contains(current_url(), '/pendaftar') || str_contains(current_url(), '/admin') || str_contains(current_url(), '/operator') ? 'active' : '' ?>">
                <span class="mobile-nav-icon"><i data-lucide="user"></i></span>
                <span class="mobile-nav-label">Akun</span>
            </a>
        <?php else: ?>
            <a href="<?= base_url('auth/login') ?>" class="mobile-nav-item <?= str_contains(current_url(), '/auth') ? 'active' : '' ?>">
                <span class="mobile-nav-icon"><i data-lucide="user"></i></span>
                <span class="mobile-nav-label">Akun</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Floating WhatsApp Button -->
    <a href="<?= base_url('/kontak') ?>" class="sp-floating-wa" aria-label="Hubungi panitia">
        <i data-lucide="message-circle"></i>
    </a>

    <!-- Footer -->
    <footer class="sp-public-footer">
        <div class="container">
            <div class="sp-footer-grid">
                <!-- Brand Column -->
                <div class="sp-footer-brand-col">
                    <a class="sp-footer-brand" href="<?= base_url('/') ?>">
                        <div class="sp-brand-icon<?= $schoolLogoUrl ? ' sp-brand-icon--logo' : '' ?>">
                            <?php if ($schoolLogoUrl): ?>
                                <img src="<?= esc($schoolLogoUrl) ?>" alt="Logo <?= esc($schoolName) ?>" class="sp-brand-logo-img">
                            <?php else: ?>
                                <i data-lucide="graduation-cap"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="sp-footer-brand-name"><?= esc($schoolName) ?></span>
                            <span class="sp-footer-brand-tagline"><?= esc($schoolTagline) ?></span>
                        </div>
                    </a>
                    <p class="sp-footer-desc"><?= esc($footerDesc) ?></p>
                    <div class="sp-footer-social">
                        <a href="#" class="footer-social-link" aria-label="Facebook"><i data-lucide="facebook"></i></a>
                        <a href="#" class="footer-social-link" aria-label="Instagram"><i data-lucide="instagram"></i></a>
                        <a href="#" class="footer-social-link" aria-label="YouTube"><i data-lucide="youtube"></i></a>
                    </div>
                </div>

                <!-- Links Column -->
                <div class="sp-footer-links-col">
                    <h5 class="sp-footer-heading">Tautan Cepat</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                        <li><a href="<?= base_url('/profil') ?>">Profil Sekolah</a></li>
                        <li><a href="<?= base_url('/spmb') ?>">Info Pendaftaran</a></li>
                        <li><a href="<?= base_url('/hasil-seleksi') ?>">Hasil Seleksi</a></li>
                        <li><a href="<?= base_url('/kontak') ?>">Hubungi Kami</a></li>
                    </ul>
                </div>

                <!-- Contact Column -->
                <div class="sp-footer-contact-col">
                    <h5 class="sp-footer-heading">Hubungi Kami</h5>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon"><i data-lucide="map-pin"></i></div>
                        <div class="footer-contact-info">
                            <h6>Alamat</h6>
                            <p><?= esc($footerAddress) ?></p>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon"><i data-lucide="phone"></i></div>
                        <div class="footer-contact-info">
                            <h6>Telepon</h6>
                            <p><a href="tel:<?= preg_replace('/[^0-9+]/', '', $footerPhone) ?>"><?= esc($footerPhone) ?></a></p>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon"><i data-lucide="mail"></i></div>
                        <div class="footer-contact-info">
                            <h6>Email</h6>
                            <p><a href="mailto:<?= esc($footerEmail) ?>"><?= esc($footerEmail) ?></a></p>
                        </div>
                    </div>
                </div>

                <!-- CTA Column -->
                <div class="sp-footer-cta-col">
                    <div class="sp-footer-cta-card">
                        <div class="sp-footer-cta-badge">
                            <i data-lucide="award" class="text-primary"></i>
                            <div>
                                <strong>Akreditasi <?= esc($footerAccreditation) ?></strong>
                                <?php if ($footerNpsn && $footerNpsn !== '-'): ?>
                                <small>NPSN: <?= esc($footerNpsn) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="sp-footer-cta-text">Unduh brosur resmi sekolah untuk informasi lengkap program unggulan.</p>
                        <a href="<?= base_url('/brosur') ?>" class="btn btn-primary w-100 sp-footer-cta-btn">
                            <i data-lucide="download"></i> Download Brosur
                        </a>
                    </div>
                </div>
            </div>

            <div class="sp-footer-bottom">
                <p class="sp-footer-copy mb-0">
                    <span>&copy; <?= date('Y') ?> <?= esc($schoolName) ?></span>
                    <span class="sp-footer-copy-separator" aria-hidden="true">|</span>
                    <span>Smart SPMB Pro v<?= esc($appInfo->version) ?></span>
                    <span class="sp-footer-copy-separator" aria-hidden="true">|</span>
                    <span>Developed by <strong><?= esc($appInfo->developer) ?></strong></span>
                </p>
                <div class="sp-footer-legal">
                    <a href="<?= base_url('/kebijakan-privasi') ?>">Kebijakan Privasi</a>
                    <a href="<?= base_url('/syarat-ketentuan') ?>">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Initialize Lucide Icons & Premium Dark Mode -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Navbar scroll effect
            const navbar = document.getElementById('main-navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            SpTheme.bindDarkModeToggle(
                document.getElementById('public-theme-toggle'),
                document.getElementById('public-theme-toggle-icon')
            );

            document.addEventListener('dark-mode-change', function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });

            // Desktop nav dropdown — CSS hover (no Bootstrap toggle; avoids gap/close bugs)
            document.querySelectorAll('.sp-nav-dropdown .dropdown-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    if (window.matchMedia('(min-width: 992px)').matches) {
                        e.preventDefault();
                    }
                });
            });

            // Close mobile drawer after navigation
            const mobileDrawer = document.getElementById('mobileMenuDrawer');
            if (mobileDrawer) {
                mobileDrawer.querySelectorAll('a.sp-mobile-nav-link:not(.sp-mobile-nav-toggle), a.sp-mobile-nav-sublink').forEach(function(link) {
                    link.addEventListener('click', function() {
                        bootstrap.Offcanvas.getInstance(mobileDrawer)?.hide();
                    });
                });
            }

            // Mobile nav accordion chevron sync
            document.querySelectorAll('.sp-mobile-nav-sub').forEach(function(collapseEl) {
                collapseEl.addEventListener('show.bs.collapse', function() {
                    this.previousElementSibling?.classList.add('expanded');
                });
                collapseEl.addEventListener('hide.bs.collapse', function() {
                    this.previousElementSibling?.classList.remove('expanded');
                });
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
