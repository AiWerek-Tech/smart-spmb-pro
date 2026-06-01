<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// (Removed legacy compatibility redirects — routing is handled by router.php)

/*
 * -----------------------------------------------------------------------
 * Public Website Routes (no authentication required)
 * -----------------------------------------------------------------------
 */
$routes->get('/', 'Public\HomeController::index');
$routes->get('/profil', 'Public\ProfileController::index');
$routes->get('/lingkungan-kampus', 'Public\PageController::campus');
$routes->get('/galeri', 'Public\GalleryController::index');
$routes->get('/spmb', 'Public\SpmbController::index');
$routes->get('/pengumuman', 'Public\AnnouncementController::index');
$routes->post('/pengumuman/cek-hasil', 'Public\AnnouncementController::checkResult');
$routes->get('/kontak', 'Public\ContactController::index');
$routes->get('/kebijakan-privasi', 'Public\PageController::privacy');
$routes->get('/syarat-ketentuan', 'Public\PageController::terms');

// Pengalihan Sub-Page & Halaman Menu Utama agar tidak ada 404/halaman kosong (Req 2.0)
$routes->addRedirect('profil/sejarah', 'profil#sejarah');
$routes->addRedirect('profil/fasilitas', 'profil#fasilitas');
$routes->addRedirect('profil/guru', 'profil#guru');
$routes->addRedirect('biaya', 'spmb#biaya');
$routes->addRedirect('faq', 'spmb#faq');

/*
 * -----------------------------------------------------------------------
 * Authentication Routes
 * -----------------------------------------------------------------------
 */
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth\AuthController::loginView');
    $routes->post('login', 'Auth\AuthController::login');
    $routes->get('register', 'Auth\AuthController::registerView');
    $routes->post('register', 'Auth\AuthController::register');
    $routes->get('forgot', 'Auth\AuthController::forgotView');
    $routes->post('send-reset', 'Auth\AuthController::sendReset');
    $routes->get('reset', 'Auth\AuthController::resetView');
    $routes->get('reset/(:segment)', 'Auth\AuthController::resetView/$1');
    $routes->post('reset-password', 'Auth\AuthController::resetPassword');
    $routes->get('logout', 'Auth\AuthController::logout');
});

/*
 * -----------------------------------------------------------------------
 * Admin Routes (requires auth + role:admin)
 * -----------------------------------------------------------------------
 */
$routes->group('admin', ['filter' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // User Management
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->get('users/(:num)/edit', 'Admin\UserController::edit/$1');
    $routes->post('users/(:num)/update', 'Admin\UserController::update/$1');
    $routes->post('users/(:num)/toggle', 'Admin\UserController::toggle/$1');
    $routes->post('users/(:num)/delete', 'Admin\UserController::delete/$1');

    // System Settings
    $routes->get('settings', 'Admin\SettingController::index');
    $routes->post('settings/save', 'Admin\SettingController::save');

    // Content Management
    $routes->get('content', 'Admin\ContentController::index');
    $routes->post('content/save', 'Admin\ContentController::save');
    $routes->post('content/gallery/upload', 'Admin\ContentController::galleryUpload');
    $routes->post('content/gallery/(:num)/update', 'Admin\ContentController::galleryUpdate/$1');
    $routes->post('content/gallery/(:num)/delete', 'Admin\ContentController::galleryDelete/$1');

    // Banner Management
    $routes->get('banners', 'Admin\ContentController::banners');
    $routes->post('banners/store', 'Admin\ContentController::bannerStore');
    $routes->post('banners/(:num)/update', 'Admin\ContentController::bannerUpdate/$1');
    $routes->post('banners/(:num)/delete', 'Admin\ContentController::bannerDelete/$1');

    // Testimonial Management
    $routes->get('testimonials', 'Admin\ContentController::testimonials');
    $routes->post('testimonials/store', 'Admin\ContentController::testimonialStore');
    $routes->post('testimonials/(:num)/update', 'Admin\ContentController::testimonialUpdate/$1');
    $routes->post('testimonials/(:num)/delete', 'Admin\ContentController::testimonialDelete/$1');

    // Statistic Management
    $routes->get('statistics', 'Admin\ContentController::statistics');
    $routes->post('statistics/store', 'Admin\ContentController::statisticStore');
    $routes->post('statistics/(:num)/update', 'Admin\ContentController::statisticUpdate/$1');
    $routes->post('statistics/(:num)/delete', 'Admin\ContentController::statisticDelete/$1');

    // FAQ Management
    $routes->get('faq', 'Admin\ContentController::faq');
    $routes->post('faq/store', 'Admin\ContentController::faqStore');
    $routes->post('faq/(:num)/update', 'Admin\ContentController::faqUpdate/$1');
    $routes->post('faq/(:num)/delete', 'Admin\ContentController::faqDelete/$1');

    // Jalur Management
    $routes->get('jalur', 'Admin\JalurController::index');
    $routes->post('jalur/(:num)/update', 'Admin\JalurController::update/$1');
    $routes->post('jalur/(:num)/toggle', 'Admin\JalurController::toggle/$1');

    // Gelombang Management
    $routes->get('gelombang', 'Admin\JalurController::gelombang');
    $routes->post('gelombang/store', 'Admin\JalurController::gelombangStore');
    $routes->post('gelombang/(:num)/update', 'Admin\JalurController::gelombangUpdate/$1');
    $routes->post('gelombang/(:num)/delete', 'Admin\JalurController::gelombangDelete/$1');

    // Announcement Management
    $routes->get('announcements', 'Admin\AnnouncementController::index');
    $routes->get('announcements/create', 'Admin\AnnouncementController::create');
    $routes->post('announcements/store', 'Admin\AnnouncementController::store');
    $routes->get('announcements/(:num)/edit', 'Admin\AnnouncementController::edit/$1');
    $routes->post('announcements/(:num)/update', 'Admin\AnnouncementController::update/$1');
    $routes->post('announcements/(:num)/delete', 'Admin\AnnouncementController::delete/$1');
    $routes->post('announcements/(:num)/publish', 'Admin\AnnouncementController::publish/$1');

    // Seleksi (Selection Results)
    $routes->get('seleksi', 'Admin\AnnouncementController::seleksi');
    $routes->post('seleksi/hitung-ranking', 'Admin\AnnouncementController::calculateRanking');
    $routes->post('seleksi/(:num)/update', 'Admin\AnnouncementController::seleksiUpdate/$1');

    // Backup & Restore
    $routes->get('backup', 'Admin\BackupController::index');
    $routes->post('backup/create', 'Admin\BackupController::create');
    $routes->post('backup/restore', 'Admin\BackupController::restore');
});

/*
 * -----------------------------------------------------------------------
 * Operator Routes (requires auth + role:operator or admin)
 * -----------------------------------------------------------------------
 */
$routes->group('operator', ['filter' => ['auth', 'role:operator,admin']], function ($routes) {
    $routes->get('/', 'Operator\DashboardController::index');
    $routes->get('dashboard', 'Operator\DashboardController::index');

    // Registrant Management
    $routes->get('registrants', 'Operator\RegistrantController::index');
    $routes->get('registrants/(:num)', 'Operator\RegistrantController::show/$1');
    $routes->get('registrants/(:num)/edit', 'Operator\RegistrantController::edit/$1');
    $routes->post('registrants/(:num)/update', 'Operator\RegistrantController::update/$1');

    // Document Verification
    $routes->get('documents/(:num)', 'Operator\DocumentController::index/$1');
    $routes->post('documents/(:num)/verify', 'Operator\DocumentController::verify/$1');
    $routes->get('documents/(:num)/view', 'Operator\DocumentController::view/$1');

    // Export
    $routes->get('export/excel', 'Operator\ExportController::excel');
    $routes->get('export/fpd/(:num)', 'Operator\ExportController::fpd/$1');

    // Dapodik Validation
    $routes->get('dapodik', 'Operator\DapodikController::index');
    $routes->get('dapodik/(:num)', 'Operator\DapodikController::show/$1');
});

/*
 * -----------------------------------------------------------------------
 * Pendaftar Routes (requires auth + role:pendaftar)
 * -----------------------------------------------------------------------
 */
$routes->group('pendaftar', ['filter' => ['auth', 'role:pendaftar']], function ($routes) {
    $routes->get('/', 'Pendaftar\DashboardController::index');
    $routes->get('dashboard', 'Pendaftar\DashboardController::index');

    // Registration Wizard
    $routes->get('daftar', 'Pendaftar\RegistrationController::wizard');
    $routes->get('daftar/step/(:num)', 'Pendaftar\RegistrationController::step/$1');
    $routes->post('daftar/step/(:num)/save', 'Pendaftar\RegistrationController::saveStep/$1');
    $routes->post('daftar/submit', 'Pendaftar\RegistrationController::submit');

    // Documents
    $routes->get('dokumen', 'Pendaftar\DocumentController::index');
    $routes->post('dokumen/upload', 'Pendaftar\DocumentController::upload');
    $routes->post('dokumen/(:num)/delete', 'Pendaftar\DocumentController::delete/$1');

    // Print
    $routes->get('cetak/bukti', 'Pendaftar\DocumentController::printBukti');
    $routes->get('cetak/kartu', 'Pendaftar\DocumentController::printKartu');
    $routes->get('cetak/skl', 'Pendaftar\DocumentController::printSkl');
});

/*
 * -----------------------------------------------------------------------
 * API Routes (requires auth)
 * -----------------------------------------------------------------------
 */
$routes->get('api/stats', 'Public\HomeController::stats');
$routes->group('api', ['filter' => 'auth'], function ($routes) {
    $routes->get('search', 'Api\SearchController::index');
    $routes->post('theme/save', 'Api\ThemeController::save');
});

/*
 * -----------------------------------------------------------------------
 * Fallback / Error Routes
 * -----------------------------------------------------------------------
 */
$routes->set404Override(function () {
    return view('errors/404');
});
