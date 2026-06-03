<?php
$settingModel = new \App\Models\SettingModel();
$globalThemeColor = $settingModel->getValue('theme_color', 'purple');
$schoolName = $settingModel->getValue('school_name', 'Smart SPMB Pro');
$schoolLogo = $settingModel->getValue('school_logo', '');
$schoolLogoUrl = !empty($schoolLogo) ? base_url($schoolLogo) : '';
$appInfo = config('AppInfo');
$supportPhone = preg_replace('/[^0-9]/', '', (string) $settingModel->getValue('whatsapp', $appInfo->developerWhatsapp));
$supportEmail = (string) $settingModel->getValue('email', $appInfo->developerEmail);
?>
<!DOCTYPE html>
<html lang="id" data-theme-color="<?= esc($globalThemeColor) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart SPMB Pro v<?= esc($appInfo->version) ?> - Dashboard Panel">
    <meta name="author" content="<?= esc($appInfo->developer) ?>">
    <meta name="application-name" content="<?= esc($appInfo->name) ?>">
    <meta name="version" content="<?= esc($appInfo->version) ?>">
    <title><?= $title ?? 'Dashboard' ?> - Smart SPMB Pro</title>

    <!-- Favicon — gunakan logo sekolah jika tersedia -->
    <?php if ($schoolLogoUrl): ?>
        <link rel="icon" type="image/x-icon" href="<?= esc($schoolLogoUrl) ?>">
        <link rel="apple-touch-icon" href="<?= esc($schoolLogoUrl) ?>">
    <?php else: ?>
        <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/img/favicon.svg') ?>">
    <?php endif; ?>
    
    <script src="<?= base_url('assets/js/theme-sync.js') ?>"></script>
    <script>
        SpTheme.init({ serverTheme: '<?= esc($globalThemeColor) ?>', scope: 'dashboard' });
    </script>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <!-- ApexCharts CSS -->
    <link href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css" rel="stylesheet">

    <!-- Smart SPMB Pro — Foundation Design System + Dashboard Layout -->
    <link href="<?= base_url('assets/css/foundation.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin-dashboard.css') ?>" rel="stylesheet">

    <!-- Lucide Icons with First-Party Local Fallback -->
    <script src="https://unpkg.com/lucide@0.309.0/dist/umd/lucide.min.js"></script>
    <script>
        if (typeof lucide === 'undefined') {
            document.write('<script src="<?= base_url('assets/js/lucide.min.js') ?>"><\/script>');
        }
    </script>

    <?= $this->renderSection('additional_css') ?>
</head>
<body data-user-role="<?= esc(session()->get('user_base_role') ?? session()->get('user_role') ?? 'pendaftar') ?>">
    <script>SpTheme.initDarkMode();</script>

    <!-- Skip Navigation -->
    <a href="#main-content" class="sp-skip-link">Lewati ke konten utama</a>

    <div class="layout-wrapper">
        <!-- Sidebar Backdrop for Mobile -->
        <div class="sidebar-overlay" id="sidebar-overlay" role="presentation"></div>

        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar" role="navigation" aria-label="Menu navigasi utama">
            <div class="sidebar-brand">
                <div class="brand-logo<?= $schoolLogoUrl ? ' brand-logo--logo' : '' ?>">
                    <?php if ($schoolLogoUrl): ?>
                        <img src="<?= esc($schoolLogoUrl) ?>" alt="Logo <?= esc($schoolName) ?>" class="sp-brand-logo-img">
                    <?php else: ?>
                        <i data-lucide="graduation-cap"></i>
                    <?php endif; ?>
                </div>
                <h5 class="brand-text"><?= esc($schoolName) ?></h5>
            </div>
            
            <ul class="sidebar-menu">
                <!-- Dashboard Route per Role -->
                <?php 
                    $role = session()->get('user_base_role') ?? session()->get('user_role') ?? 'pendaftar'; 
                    $roleSlug = session()->get('user_role') ?? $role;
                    $segment1 = service('request')->getUri()->getSegment(1);
                    $segment2 = service('request')->getUri()->getSegment(2);
                    $currentRegistrantStatus = (string) service('request')->getGet('status');
                    $canViewPayments = service('rbacEngine')->hasAnyPermission((int) session()->get('user_id'), ['payments.view']);
                ?>

                <!-- ------------------- ADMIN SIDEBAR ------------------- -->
                <?php if ($role === 'admin'): ?>
                    <?php
                        $spmbMenuOpen = in_array($segment2, ['academic-years', 'jalur', 'gelombang', 'document-requirements', 'seleksi'], true);
                        $contentMenuOpen = in_array($segment2, ['content', 'teachers', 'gallery', 'banners', 'announcements', 'testimonials', 'statistics', 'faq'], true);
                        $systemMenuOpen = in_array($segment2, ['users', 'access', 'settings', 'backup'], true);
                    ?>
                    <li class="menu-header">Menu</li>
                    <li class="menu-item <?= ($segment1 === 'admin' && ($segment2 === '' || $segment2 === 'dashboard')) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/dashboard') ?>" class="menu-link">
                            <i data-lucide="layout-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="menu-item has-submenu <?= $spmbMenuOpen ? 'open active' : '' ?>">
                        <button type="button" class="menu-link submenu-toggle" data-sidebar-submenu="spmb" aria-expanded="<?= $spmbMenuOpen ? 'true' : 'false' ?>">
                            <i data-lucide="graduation-cap"></i>
                            <span>Data SPMB</span>
                            <i data-lucide="chevron-down" class="submenu-chevron"></i>
                        </button>
                        <ul class="submenu-list" data-sidebar-submenu-panel="spmb">
                            <li><a href="<?= base_url('admin/academic-years') ?>" class="submenu-link <?= $segment2 === 'academic-years' ? 'active' : '' ?>"><i data-lucide="calendar-range"></i><span>Tahun Pelajaran</span></a></li>
                            <li><a href="<?= base_url('admin/jalur') ?>" class="submenu-link <?= $segment2 === 'jalur' ? 'active' : '' ?>"><i data-lucide="git-fork"></i><span>Jalur Pendaftaran</span></a></li>
                            <li><a href="<?= base_url('admin/gelombang') ?>" class="submenu-link <?= $segment2 === 'gelombang' ? 'active' : '' ?>"><i data-lucide="calendar"></i><span>Gelombang</span></a></li>
                            <li><a href="<?= base_url('admin/document-requirements') ?>" class="submenu-link <?= $segment2 === 'document-requirements' ? 'active' : '' ?>"><i data-lucide="file-check-2"></i><span>Syarat Dokumen</span></a></li>
                            <li><a href="<?= base_url('admin/seleksi') ?>" class="submenu-link <?= $segment2 === 'seleksi' ? 'active' : '' ?>"><i data-lucide="award"></i><span>Hasil Seleksi</span></a></li>
                        </ul>
                    </li>

                    <li class="menu-item has-submenu <?= $contentMenuOpen ? 'open active' : '' ?>">
                        <button type="button" class="menu-link submenu-toggle" data-sidebar-submenu="content" aria-expanded="<?= $contentMenuOpen ? 'true' : 'false' ?>">
                            <i data-lucide="layers"></i>
                            <span>Konten Publik</span>
                            <i data-lucide="chevron-down" class="submenu-chevron"></i>
                        </button>
                        <ul class="submenu-list" data-sidebar-submenu-panel="content">
                            <li><a href="<?= base_url('admin/content') ?>" class="submenu-link <?= $segment2 === 'content' ? 'active' : '' ?>"><i data-lucide="school"></i><span>Profil Sekolah</span></a></li>
                            <li><a href="<?= base_url('admin/teachers') ?>" class="submenu-link <?= $segment2 === 'teachers' ? 'active' : '' ?>"><i data-lucide="users"></i><span>Tenaga Pendidik</span></a></li>
                            <li><a href="<?= base_url('admin/gallery') ?>" class="submenu-link <?= $segment2 === 'gallery' ? 'active' : '' ?>"><i data-lucide="image"></i><span>Galeri Sekolah</span></a></li>
                            <li><a href="<?= base_url('admin/banners') ?>" class="submenu-link <?= $segment2 === 'banners' ? 'active' : '' ?>"><i data-lucide="image"></i><span>Banner Hero</span></a></li>
                            <li><a href="<?= base_url('admin/announcements') ?>" class="submenu-link <?= $segment2 === 'announcements' ? 'active' : '' ?>"><i data-lucide="megaphone"></i><span>Pengumuman</span></a></li>
                            <li><a href="<?= base_url('admin/testimonials') ?>" class="submenu-link <?= $segment2 === 'testimonials' ? 'active' : '' ?>"><i data-lucide="message-square"></i><span>Testimoni</span></a></li>
                            <li><a href="<?= base_url('admin/statistics') ?>" class="submenu-link <?= $segment2 === 'statistics' ? 'active' : '' ?>"><i data-lucide="bar-chart-2"></i><span>Statistik</span></a></li>
                            <li><a href="<?= base_url('admin/faq') ?>" class="submenu-link <?= $segment2 === 'faq' ? 'active' : '' ?>"><i data-lucide="help-circle"></i><span>FAQ</span></a></li>
                        </ul>
                    </li>

                    <li class="menu-item has-submenu <?= $systemMenuOpen ? 'open active' : '' ?>">
                        <button type="button" class="menu-link submenu-toggle" data-sidebar-submenu="system" aria-expanded="<?= $systemMenuOpen ? 'true' : 'false' ?>">
                            <i data-lucide="settings"></i>
                            <span>Sistem</span>
                            <i data-lucide="chevron-down" class="submenu-chevron"></i>
                        </button>
                        <ul class="submenu-list" data-sidebar-submenu-panel="system">
                            <li><a href="<?= base_url('admin/users') ?>" class="submenu-link <?= $segment2 === 'users' ? 'active' : '' ?>"><i data-lucide="users"></i><span>Pengguna</span></a></li>
                            <li><a href="<?= base_url('admin/access') ?>" class="submenu-link <?= $segment2 === 'access' ? 'active' : '' ?>"><i data-lucide="shield-check"></i><span>Mode & Hak Akses</span></a></li>
                            <li><a href="<?= base_url('admin/settings') ?>" class="submenu-link <?= $segment2 === 'settings' ? 'active' : '' ?>"><i data-lucide="sliders-horizontal"></i><span>Konfigurasi</span></a></li>
                            <li><a href="<?= base_url('admin/backup') ?>" class="submenu-link <?= $segment2 === 'backup' ? 'active' : '' ?>"><i data-lucide="database"></i><span>Backup & Restore</span></a></li>
                        </ul>
                    </li>

                <!-- ------------------- OPERATOR SIDEBAR ------------------- -->
                <?php elseif ($role === 'operator'): ?>
                    <li class="menu-header">Menu Utama</li>
                    <li class="menu-item <?= ($segment1 === 'operator' && ($segment2 === '' || $segment2 === 'dashboard')) ? 'active' : '' ?>">
                        <a href="<?= base_url('operator/dashboard') ?>" class="menu-link">
                            <i data-lucide="layout-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="menu-header">Pengelolaan SPMB</li>
                    <?php if ($canViewPayments): ?>
                    <li class="menu-item <?= ($segment1 === 'bendahara') ? 'active' : '' ?>">
                        <a href="<?= base_url('bendahara/invoices') ?>" class="menu-link">
                            <i data-lucide="wallet"></i>
                            <span>Pembayaran</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="menu-item <?= ($segment1 === 'operator' && $segment2 === 'registrants') ? 'active' : '' ?>">
                        <a href="<?= base_url('operator/registrants') ?>" class="menu-link">
                            <i data-lucide="graduation-cap"></i>
                            <span>Daftar Pendaftar</span>
                        </a>
                    </li>
                    <li class="menu-item <?= ($segment1 === 'operator' && $segment2 === 'dapodik') ? 'active' : '' ?>">
                        <a href="<?= base_url('operator/dapodik') ?>" class="menu-link">
                            <i data-lucide="check-square"></i>
                            <span>Validasi Dapodik</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="<?= base_url('operator/export/excel') ?>" class="menu-link">
                            <i data-lucide="download"></i>
                            <span>Ekspor Data</span>
                        </a>
                    </li>

                <!-- ------------------- PENDAFTAR SIDEBAR ------------------- -->
                <?php else: ?>
                    <li class="menu-header">Pendaftar</li>
                    <li class="menu-item <?= ($segment1 === 'pendaftar' && ($segment2 === '' || $segment2 === 'dashboard')) ? 'active' : '' ?>">
                        <a href="<?= base_url('pendaftar/dashboard') ?>" class="menu-link">
                            <i data-lucide="layout-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="menu-item <?= ($segment1 === 'pendaftar' && $segment2 === 'daftar') ? 'active' : '' ?>">
                        <a href="<?= base_url('pendaftar/daftar') ?>" class="menu-link">
                            <i data-lucide="file-text"></i>
                            <span>Formulir Pendaftaran</span>
                        </a>
                    </li>
                    <li class="menu-item <?= ($segment1 === 'pendaftar' && $segment2 === 'dokumen') ? 'active' : '' ?>">
                        <a href="<?= base_url('pendaftar/dokumen') ?>" class="menu-link">
                            <i data-lucide="folder-open"></i>
                            <span>Unggah Dokumen</span>
                        </a>
                    </li>
                    <li class="menu-item <?= ($segment1 === 'hasil-seleksi') ? 'active' : '' ?>">
                        <a href="<?= base_url('hasil-seleksi') ?>" class="menu-link">
                            <i data-lucide="clipboard-check"></i>
                            <span>Hasil Seleksi</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="menu-header">Sesi</li>
                <li class="menu-item">
                    <a href="<?= base_url('auth/logout') ?>" class="menu-link text-danger logout-trigger">
                        <i data-lucide="log-out"></i>
                        <span>Keluar Akun</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Workspace -->
        <div class="main-container">
            <!-- Top Navbar — Glassmorphism -->
            <nav class="top-navbar" aria-label="Navigasi atas">
                <button class="menu-toggle-btn" id="menu-toggle" aria-label="Toggle sidebar" aria-controls="sidebar" aria-expanded="true">
                    <i data-lucide="menu"></i>
                </button>
                <div class="navbar-content">
                    <div class="search-box" id="open-command-palette" role="button" tabindex="0" aria-label="Pencarian global">
                        <i data-lucide="search" class="me-2" style="width: 16px; height: 16px;"></i>
                        <span>Cari menu atau data... <kbd style="font-size:0.7rem;background:rgba(var(--sp-secondary-rgb),0.08);padding:1px 5px;border-radius:3px;border:1px solid var(--sp-border-color);margin-left:4px;">Ctrl+K</kbd></span>
                    </div>
                    
                    <!-- Theme Toggle Switcher & Live Theme Switcher -->
                    <div class="d-flex align-items-center">
                        <button class="theme-toggle-btn me-2" id="theme-toggle" title="Ganti Tema" aria-label="Toggle tema gelap">
                            <i data-lucide="moon" style="width: 20px; height: 20px;" id="theme-toggle-icon"></i>
                        </button>

                        <!-- Theme Color Switcher Dropdown (Premium) -->
                        <div class="dropdown me-2">
                            <button class="theme-toggle-btn" type="button" id="themeColorSwitcher" data-bs-toggle="dropdown" aria-expanded="false" title="Ganti Warna Tema" aria-label="Ganti Warna Tema">
                                <i data-lucide="palette" style="width: 20px; height: 20px;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-3 border shadow-lg" aria-labelledby="themeColorSwitcher" style="min-width: 220px; border-radius: var(--sp-radius-md); z-index: 1050;">
                                <li><h6 class="dropdown-header px-0 mb-2 pt-0" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--sp-text-muted);">Pilih Warna Tema</h6></li>
                                <li>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
                                        <button type="button" class="btn-color-theme purple" data-color="purple" title="Purple (Default)" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid transparent; background: #6366f1; cursor: pointer; transition: transform 0.2s;"></button>
                                        <button type="button" class="btn-color-theme navy" data-color="navy" title="Navy Blue" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid transparent; background: #1e3a8a; cursor: pointer; transition: transform 0.2s;"></button>
                                        <button type="button" class="btn-color-theme lightblue" data-color="lightblue" title="Light Blue" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid transparent; background: #0284c7; cursor: pointer; transition: transform 0.2s;"></button>
                                        <button type="button" class="btn-color-theme emerald" data-color="emerald" title="Emerald Green" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid transparent; background: #059669; cursor: pointer; transition: transform 0.2s;"></button>
                                        <button type="button" class="btn-color-theme red" data-color="red" title="Crimson Red" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid transparent; background: #dc2626; cursor: pointer; transition: transform 0.2s;"></button>
                                        <button type="button" class="btn-color-theme orange" data-color="orange" title="Sunset Orange" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid transparent; background: #ea580c; cursor: pointer; transition: transform 0.2s;"></button>
                                        <button type="button" class="btn-color-theme rose" data-color="rose" title="Rose Pink" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid transparent; background: #db2777; cursor: pointer; transition: transform 0.2s;"></button>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li>
                                    <button type="button" class="btn btn-sm btn-light w-100 text-center py-1 mt-1 btn-reset-theme" style="font-size: 0.75rem; font-weight: 600;">
                                        <i data-lucide="rotate-ccw" class="me-1 d-inline-block align-middle" style="width: 12px; height: 12px;"></i><span class="align-middle">Reset Default</span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="user-dropdown dropdown ms-2">
                            <div class="d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="text-end me-2 d-none d-sm-block">
                                    <div class="fw-semibold" style="font-size: 0.85rem; color: var(--sp-heading-color);"><?= esc(session()->get('user_name')) ?></div>
                                    <div class="text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: var(--sp-primary); letter-spacing: 0.5px;"><?= esc($roleSlug) ?></div>
                                </div>
                                <div class="avatar-container">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('user_name') ?? 'User') ?>&background=7c3aed&color=fff" class="avatar" alt="Avatar User">
                                    <span class="status-badge"></span>
                                </div>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end mt-2">
                                <li><h6 class="dropdown-header">Profil Anda</h6></li>
                                <li>
                                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileDetailModal">
                                        <i data-lucide="user" class="me-2 d-inline-block align-middle" style="width: 16px; height: 16px;"></i><span class="align-middle">Detail Profil</span>
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger logout-trigger" href="<?= base_url('auth/logout') ?>">
                                        <i data-lucide="log-out" class="me-2 d-inline-block align-middle" style="width: 16px; height: 16px;"></i><span class="align-middle">Keluar</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Scrollable Content Slot -->
            <main class="content-body animate-fade-in" id="main-content">
                <!-- Flash Alerts -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i data-lucide="alert-circle" class="me-2 flex-shrink-0" style="width:20px;height:20px;"></i>
                            <div><?= session()->getFlashdata('error') ?></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i data-lucide="check-circle-2" class="me-2 flex-shrink-0" style="width:20px;height:20px;"></i>
                            <div><?= session()->getFlashdata('success') ?></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endif; ?>

                <!-- Breadcrumbs -->
                <?= $this->include('layouts/_breadcrumb') ?>

                <!-- View Specific Content -->
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Footer — Premium -->
            <footer class="footer">
                <div>
                    <span>&copy; 2026 <strong>Smart SPMB Pro</strong> v<?= esc($appInfo->version) ?>. <?= esc($appInfo->developer) ?>.</span>
                </div>
                <div class="footer-actions">
                    <button type="button" class="footer-link me-3" data-bs-toggle="modal" data-bs-target="#panduanModal">Panduan</button>
                    <button type="button" class="footer-link" data-bs-toggle="modal" data-bs-target="#bantuanModal">Bantuan</button>
                </div>
            </footer>

            <?php
                $bottomNavItems = [];

                if ($role === 'admin') {
                    $bottomNavItems = [
                        ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'url' => base_url('admin/dashboard'), 'active' => $segment1 === 'admin' && ($segment2 === '' || $segment2 === 'dashboard')],
                        ['label' => 'Pendaftar', 'icon' => 'graduation-cap', 'url' => base_url('operator/registrants'), 'active' => $segment1 === 'operator' && $segment2 === 'registrants' && $currentRegistrantStatus !== 'submitted'],
                        ['label' => 'Verifikasi', 'icon' => 'file-check-2', 'url' => base_url('operator/registrants?status=submitted'), 'active' => $segment1 === 'operator' && (($segment2 === 'registrants' && $currentRegistrantStatus === 'submitted') || $segment2 === 'documents')],
                        ['label' => 'Seleksi', 'icon' => 'award', 'url' => base_url('admin/seleksi'), 'active' => $segment1 === 'admin' && $segment2 === 'seleksi'],
                    ];
                } elseif ($role === 'operator') {
                    $bottomNavItems = [
                        ['label' => 'Beranda', 'icon' => 'layout-dashboard', 'url' => base_url('operator/dashboard'), 'active' => $segment1 === 'operator' && ($segment2 === '' || $segment2 === 'dashboard')],
                        ['label' => 'Pendaftar', 'icon' => 'graduation-cap', 'url' => base_url('operator/registrants'), 'active' => $segment1 === 'operator' && $segment2 === 'registrants'],
                        ['label' => 'Dapodik', 'icon' => 'check-square', 'url' => base_url('operator/dapodik'), 'active' => $segment1 === 'operator' && $segment2 === 'dapodik'],
                        ['label' => 'Ekspor', 'icon' => 'download', 'url' => base_url('operator/export/excel'), 'active' => false],
                    ];
                } else {
                    $bottomNavItems = [
                        ['label' => 'Beranda', 'icon' => 'layout-dashboard', 'url' => base_url('pendaftar/dashboard'), 'active' => $segment1 === 'pendaftar' && ($segment2 === '' || $segment2 === 'dashboard')],
                        ['label' => 'Formulir', 'icon' => 'file-text', 'url' => base_url('pendaftar/daftar'), 'active' => $segment1 === 'pendaftar' && $segment2 === 'daftar'],
                        ['label' => 'Dokumen', 'icon' => 'folder-open', 'url' => base_url('pendaftar/dokumen'), 'active' => $segment1 === 'pendaftar' && $segment2 === 'dokumen'],
                        ['label' => 'Status', 'icon' => 'clipboard-check', 'url' => base_url('hasil-seleksi'), 'active' => false],
                    ];
                }
            ?>
            <nav class="dashboard-mobile-bottom-nav" aria-label="Navigasi dashboard mobile">
                <?php foreach ($bottomNavItems as $item): ?>
                    <a href="<?= esc($item['url']) ?>" class="dashboard-bottom-item <?= $item['active'] ? 'active' : '' ?>" <?= $item['active'] ? 'aria-current="page"' : '' ?>>
                        <span class="dashboard-bottom-icon"><i data-lucide="<?= esc($item['icon']) ?>"></i></span>
                        <span class="dashboard-bottom-label"><?= esc($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
                <button type="button" class="dashboard-bottom-item dashboard-bottom-more" id="dashboard-more-toggle" aria-controls="sidebar" aria-expanded="false">
                    <span class="dashboard-bottom-icon"><i data-lucide="more-horizontal"></i></span>
                    <span class="dashboard-bottom-label">Lainnya</span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Command Palette -->
    <?= $this->include('layouts/_command_palette') ?>

    <!-- Scripts Section -->
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Indonesian Locale for Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <!-- Command Palette JS -->
    <script src="<?= base_url('assets/js/command-palette.js') ?>"></script>

    <script>
        $(document).ready(function() {
            // 1. Prioritize rendering Lucide icons immediately at the very front
            try {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } catch (e) {
                console.error("Lucide failed to initialize at priority front:", e);
            }

            // Sidebar Toggle (Mobile slide / Desktop collapse)
            function setMobileSidebarOpen(isOpen) {
                $('#sidebar').toggleClass('show', isOpen);
                $('#sidebar-overlay').toggleClass('show', isOpen);
                $('#dashboard-more-toggle').attr('aria-expanded', isOpen ? 'true' : 'false');
                $('#menu-toggle').attr('aria-expanded', isOpen ? 'true' : 'false');
            }

            $('#menu-toggle').on('click', function() {
                if ($(window).width() >= 992) {
                    $('.layout-wrapper').toggleClass('sidebar-collapsed');
                    $(this).attr('aria-expanded', $('.layout-wrapper').hasClass('sidebar-collapsed') ? 'false' : 'true');
                } else {
                    setMobileSidebarOpen(!$('#sidebar').hasClass('show'));
                }
            });

            $('#sidebar-overlay').on('click', function() {
                setMobileSidebarOpen(false);
            });

            $('#dashboard-more-toggle').on('click', function() {
                setMobileSidebarOpen(!$('#sidebar').hasClass('show'));
            });

            $('.submenu-toggle').on('click', function() {
                const $item = $(this).closest('.has-submenu');
                const willOpen = !$item.hasClass('open');

                $item.toggleClass('open', willOpen);
                $(this).attr('aria-expanded', willOpen ? 'true' : 'false');

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });

            $('#sidebar a.menu-link').on('click', function() {
                if ($(window).width() < 992) {
                    setMobileSidebarOpen(false);
                }
            });

            $(document).on('keydown', function(event) {
                if (event.key === 'Escape' && $(window).width() < 992 && $('#sidebar').hasClass('show')) {
                    setMobileSidebarOpen(false);
                    $('#dashboard-more-toggle').trigger('focus');
                }
            });

            // Premium Color Theme Switcher Logic
            function activeThemeOutline() {
                const current = $('html').attr('data-theme-color') || 'purple';
                $('.btn-color-theme').css('border-color', 'transparent').css('transform', 'scale(1)');

                const isDark = SpTheme.isDarkMode();
                const borderColor = isDark ? '#ffffff' : '#0f172a';

                $(`.btn-color-theme[data-color="${current}"]`)
                    .css('border-color', borderColor)
                    .css('transform', 'scale(1.15)');
            }

            // Dark mode toggle (unified with public site via SpTheme)
            SpTheme.bindDarkModeToggle(
                document.getElementById('theme-toggle'),
                document.getElementById('theme-toggle-icon')
            );

            document.addEventListener('dark-mode-change', function() {
                activeThemeOutline();
                if (window.registrationTrendChart && window.SpTheme) {
                    const color = SpTheme.getThemePrimary();
                    window.registrationTrendChart.updateOptions({
                        colors: [color],
                        stroke: { colors: [color] },
                        tooltip: { theme: SpTheme.isDarkMode() ? 'dark' : 'light' },
                    });
                }
            });

            document.addEventListener('theme-color-change', function() {
                activeThemeOutline();
            });

            try {
                if ($('.select2').length && typeof $.fn.select2 === 'function') {
                    $('.select2').select2({
                        theme: 'bootstrap-5'
                    });
                }
            } catch (e) {
                console.error("Select2 failed to initialize:", e);
            }

            // Initialize flatpickr globally if any
            try {
                if ($('.flatpickr').length && typeof flatpickr !== 'undefined') {
                    flatpickr('.flatpickr', {
                        locale: 'id',
                        dateFormat: 'Y-m-d',
                        allowInput: true
                    });
                }
            } catch (e) {
                console.error("Flatpickr failed to initialize:", e);
            }

            // Confirmation on Logout
            $('.logout-trigger').on('click', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                Swal.fire(SpTheme.mergeSwalOptions({
                    title: 'Keluar Akun?',
                    text: "Apakah Anda yakin ingin keluar dari panel dashboard?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Keluar!',
                    cancelButtonText: 'Batal'
                })).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            // Confirmation for standard deletes
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: "Data yang dihapus tidak dapat dipulihkan kembali!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $('.btn-color-theme').on('click', function() {
                const color = $(this).data('color');
                if (window.SpTheme) {
                    SpTheme.applyThemeColor(color, { persist: true });
                } else {
                    $('html').attr('data-theme-color', color);
                    localStorage.setItem('theme-color', color);
                }
                activeThemeOutline();
                
                // Sync with settings theme radios if on the settings page
                if ($('.theme-radio').length) {
                    $(`.theme-radio[value="${color}"]`).prop('checked', true);
                    if (typeof updateThemeCardStyles === 'function') {
                        updateThemeCardStyles();
                    }
                }
            });

            $('.btn-reset-theme').on('click', function() {
                localStorage.removeItem('theme-color');
                const defaultColor = '<?= esc($globalThemeColor) ?>';
                if (window.SpTheme) {
                    SpTheme.applyThemeColor(defaultColor, { persist: false });
                } else {
                    $('html').attr('data-theme-color', defaultColor);
                }
                activeThemeOutline();
                
                // Sync with settings theme radios if on the settings page
                if ($('.theme-radio').length) {
                    $(`.theme-radio[value="${defaultColor}"]`).prop('checked', true);
                    if (typeof updateThemeCardStyles === 'function') {
                        updateThemeCardStyles();
                    }
                }
            });

            activeThemeOutline();

            // Animated counter for stat values
            $('.stat-value[data-count]').each(function() {
                const $el = $(this);
                const target = parseFloat($el.data('count'));
                const decimals = ($el.data('decimals') !== undefined) ? parseInt($el.data('decimals')) : 0;
                const duration = 1200;
                const startTime = Date.now();
                
                function updateCounter() {
                    const elapsed = Date.now() - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    // Ease-out cubic
                    const easedProgress = 1 - Math.pow(1 - progress, 3);
                    const current = target * easedProgress;
                    
                    if (decimals > 0) {
                        $el.text(current.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
                    } else {
                        $el.text(Math.floor(current).toLocaleString('id-ID'));
                    }
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    }
                }
                
                requestAnimationFrame(updateCounter);
            });
        });
    </script>
    <!-- Profile & Password Change Modals -->
    <?= $this->include('layouts/_placeholder_modals') ?>

    <?php if (false): ?>
    <!-- Floating Premium Developer Diagnostics HUD -->
    <style>
        /* Developer HUD Styling */
        #sp-dev-hud {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 10000;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #sp-dev-hud-trigger {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #1e293b;
            border: 2px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10001;
            transition: all 0.2s ease;
        }
        #sp-dev-hud-trigger:hover {
            transform: scale(1.05);
            background: #0f172a;
            border-color: rgba(255, 255, 255, 0.2);
        }
        .hud-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 11px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: none; /* Show only if error count > 0 */
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid #1e293b;
        }
        
        /* Glowing pulse ring if there are errors */
        .hud-pulse-ring {
            border: 3px solid #ef4444;
            border-radius: 30px;
            height: 54px;
            width: 54px;
            position: absolute;
            top: -5px;
            left: -5px;
            animation: sp-pulsate 1.5s ease-out infinite;
            opacity: 0;
            display: none;
        }
        @keyframes sp-pulsate {
            0% { transform: scale(0.9); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: scale(1.2); opacity: 0; }
        }

        /* Panel Expansion */
        .hud-panel {
            position: absolute;
            bottom: 60px;
            right: 0;
            width: 380px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: var(--sp-radius-lg);
            overflow: hidden;
            display: none;
            flex-direction: column;
            max-height: 480px;
            z-index: 10000;
            transform: translateY(10px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        #sp-dev-hud.sp-dev-hud-expanded .hud-panel {
            display: flex;
            transform: translateY(0);
            opacity: 1;
        }

        .hud-header {
            background: rgba(30, 41, 59, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .hud-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            padding: 0 12px;
        }
        .hud-tabs .nav-link {
            color: #94a3b8 !important;
            border: none !important;
            background: transparent !important;
            padding: 10px 12px !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
        }
        .hud-tabs .nav-link.active {
            color: #ffffff !important;
            border-bottom: 2px solid var(--sp-primary) !important;
        }
        
        .hud-tab-content {
            overflow-y: auto;
            max-height: 300px;
        }

        .hud-log-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .hud-log-item {
            background: rgba(0, 0, 0, 0.2);
            border-left: 3px solid #ef4444;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .hud-log-item.asset-failure {
            border-left-color: #f97316;
        }
    </style>

    <div id="sp-dev-hud" class="sp-dev-hud-collapsed" style="display: none;">
        <button id="sp-dev-hud-trigger" class="btn-hud" aria-label="Developer Diagnostics HUD">
            <span class="hud-pulse-ring"></span>
            <span class="hud-icon">🛠️</span>
            <span class="hud-badge" id="sp-dev-hud-badge">0</span>
        </button>
        <div id="sp-dev-hud-panel" class="hud-panel shadow-lg border">
            <div class="hud-header">
                <h6 class="mb-0 text-white fw-bold"><span class="badge bg-danger me-2">DEV</span> Konsol Diagnostik Sistem</h6>
                <button type="button" class="btn-close btn-close-white" id="sp-dev-hud-close" aria-label="Close"></button>
            </div>
            <div class="hud-body text-white">
                <ul class="nav nav-tabs hud-tabs" id="hudTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="hud-errors-tab" data-bs-toggle="tab" data-bs-target="#hud-errors" type="button" role="tab">JS Errors (<span id="hud-count-errors">0</span>)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="hud-assets-tab" data-bs-toggle="tab" data-bs-target="#hud-assets" type="button" role="tab">Gagal Muat (<span id="hud-count-assets">0</span>)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="hud-sys-tab" data-bs-toggle="tab" data-bs-target="#hud-sys" type="button" role="tab">Sistem</button>
                    </li>
                </ul>
                <div class="tab-content p-3 hud-tab-content" id="hudTabContent">
                    <div class="tab-pane fade show active" id="hud-errors" role="tabpanel">
                        <div class="text-muted small mb-2">Kesalahan eksekusi script atau sintaks:</div>
                        <div id="hud-errors-list" class="hud-log-container">
                            <div class="text-success small"><i data-lucide="check-circle" class="me-1" style="width:12px;height:12px;"></i> Semua aman. Tidak ada kesalahan runtime JavaScript.</div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hud-assets" role="tabpanel">
                        <div class="text-muted small mb-2">Aset (script/styles) pihak ketiga terblokir atau 404:</div>
                        <div id="hud-assets-list" class="hud-log-container">
                            <div class="text-success small"><i data-lucide="check-circle" class="me-1" style="width:12px;height:12px;"></i> Semua aset berhasil dimuat dengan sempurna.</div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hud-sys" role="tabpanel">
                        <table class="table table-sm table-borderless text-white mb-0 small">
                            <tr><td class="text-muted" style="width:120px;">Role Akun</td><td>: <span class="badge bg-secondary"><?= esc(session()->get('user_role') ?? 'admin') ?></span></td></tr>
                            <tr><td class="text-muted">Domain Asal</td><td>: <span class="font-monospace text-warning"><?= esc(base_url()) ?></span></td></tr>
                            <tr><td class="text-muted">Kondisi Tema</td><td>: <span class="badge bg-primary" id="hud-theme-badge">Purple</span></td></tr>
                            <tr><td class="text-muted">Lucide JS</td><td>: <span id="hud-status-lucide" class="badge bg-success">Aktif</span></td></tr>
                            <tr><td class="text-muted">JQuery JS</td><td>: <span id="hud-status-jquery" class="badge bg-success">Aktif</span></td></tr>
                            <tr><td class="text-muted">Bootstrap JS</td><td>: <span id="hud-status-bootstrap" class="badge bg-success">Aktif</span></td></tr>
                        </table>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-xs btn-outline-light" id="hud-btn-copy" style="font-size:0.75rem;padding:2px 8px;">Salin Laporan Diagnostik</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Show developer badge if settings page or if there are errors
            const isSettingsPage = window.location.pathname.indexOf('/admin/settings') !== -1;
            const hud = document.getElementById('sp-dev-hud');
            
            if (isSettingsPage || window.__diagnostics.errors.length > 0 || window.__diagnostics.failedAssets.length > 0) {
                hud.style.display = 'block';
            }

            // HUD Toggle mechanics
            const trigger = document.getElementById('sp-dev-hud-trigger');
            const closeBtn = document.getElementById('sp-dev-hud-close');
            
            trigger.addEventListener('click', function() {
                hud.classList.toggle('sp-dev-hud-expanded');
                hud.classList.toggle('sp-dev-hud-collapsed');
            });
            
            closeBtn.addEventListener('click', function() {
                hud.classList.remove('sp-dev-hud-expanded');
                hud.classList.add('sp-dev-hud-collapsed');
            });

            // Sync HUD content dynamically
            window.__updateDiagnosticsHUD = function() {
                const total = window.__diagnostics.errors.length + window.__diagnostics.rejections.length + window.__diagnostics.failedAssets.length;
                
                // Update badge & ring
                const badge = document.getElementById('sp-dev-hud-badge');
                const ring = hud.querySelector('.hud-pulse-ring');
                
                if (total > 0) {
                    badge.innerText = total;
                    badge.style.display = 'flex';
                    ring.style.display = 'block';
                    hud.style.display = 'block'; // Make sure badge is visible
                } else {
                    badge.style.display = 'none';
                    ring.style.display = 'none';
                }

                // Update tab headings count
                document.getElementById('hud-count-errors').innerText = window.__diagnostics.errors.length + window.__diagnostics.rejections.length;
                document.getElementById('hud-count-assets').innerText = window.__diagnostics.failedAssets.length;

                // Populate JS Errors log
                const errorsList = document.getElementById('hud-errors-list');
                if (window.__diagnostics.errors.length === 0 && window.__diagnostics.rejections.length === 0) {
                    errorsList.innerHTML = `<div class="text-success small"><i data-lucide="check-circle" class="me-1" style="width:12px;height:12px;"></i> Semua aman. Tidak ada kesalahan runtime JavaScript.</div>`;
                } else {
                    let html = '';
                    window.__diagnostics.errors.forEach(err => {
                        html += `
                            <div class="hud-log-item small">
                                <div class="text-danger fw-bold">[${err.time}] Runtime Error</div>
                                <div class="text-white-50">${err.message}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">File: ${err.file} | Baris: ${err.line}:${err.col}</div>
                            </div>
                        `;
                    });
                    window.__diagnostics.rejections.forEach(rej => {
                        html += `
                            <div class="hud-log-item small" style="border-left-color: #e11d48;">
                                <div class="text-danger fw-bold">[${rej.time}] Unhandled Promise Rejection</div>
                                <div class="text-white-50">${rej.reason}</div>
                            </div>
                        `;
                    });
                    errorsList.innerHTML = html;
                }

                // Populate Failed Assets log
                const assetsList = document.getElementById('hud-assets-list');
                if (window.__diagnostics.failedAssets.length === 0) {
                    assetsList.innerHTML = `<div class="text-success small"><i data-lucide="check-circle" class="me-1" style="width:12px;height:12px;"></i> Semua aset berhasil dimuat dengan sempurna.</div>`;
                } else {
                    let html = '';
                    window.__diagnostics.failedAssets.forEach(asset => {
                        html += `
                            <div class="hud-log-item asset-failure small">
                                <div class="text-warning fw-bold">[${asset.time}] Gagal Pemuatan CDN (${asset.tag})</div>
                                <div class="text-white-50 text-break" style="font-size: 0.75rem;">${asset.url}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Kemungkinan diblokir oleh Ad-Blocker atau Masalah Jaringan.</div>
                            </div>
                        `;
                    });
                    assetsList.innerHTML = html;
                }

                // Populate Diagnostics details
                document.getElementById('hud-status-lucide').className = (typeof lucide !== 'undefined') ? 'badge bg-success' : 'badge bg-danger';
                document.getElementById('hud-status-lucide').innerText = (typeof lucide !== 'undefined') ? 'Aktif' : 'Terblokir';
                
                document.getElementById('hud-status-jquery').className = (typeof $ !== 'undefined') ? 'badge bg-success' : 'badge bg-danger';
                document.getElementById('hud-status-jquery').innerText = (typeof $ !== 'undefined') ? 'Aktif' : 'Gagal';
                
                document.getElementById('hud-status-bootstrap').className = (typeof bootstrap !== 'undefined') ? 'badge bg-success' : 'badge bg-danger';
                document.getElementById('hud-status-bootstrap').innerText = (typeof bootstrap !== 'undefined') ? 'Aktif' : 'Gagal';
                
                document.getElementById('hud-theme-badge').innerText = $('html').attr('data-theme-color') || 'purple';

                if (typeof lucide !== 'undefined') {
                    try { lucide.createIcons(); } catch(e) {}
                }
            };

            // Diagnostic copy utility
            document.getElementById('hud-btn-copy').addEventListener('click', function() {
                const report = {
                    time: new Date().toLocaleString(),
                    url: window.location.href,
                    theme: $('html').attr('data-theme-color') || 'purple',
                    lucide_active: typeof lucide !== 'undefined',
                    jquery_active: typeof $ !== 'undefined',
                    bootstrap_active: typeof bootstrap !== 'undefined',
                    errors: window.__diagnostics.errors,
                    rejections: window.__diagnostics.rejections,
                    failed_assets: window.__diagnostics.failedAssets
                };
                
                navigator.clipboard.writeText(JSON.stringify(report, null, 2)).then(() => {
                    alert('Laporan diagnostik berhasil disalin ke papan klip (clipboard)! Silakan tempel (paste) dan kirim ke pengembang.');
                }).catch(() => {
                    alert(JSON.stringify(report, null, 2));
                });
            });

            // Initial render
            window.__updateDiagnosticsHUD();
        });
    </script>

    <?php endif; ?>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
