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
$routes->get('/hasil-seleksi', 'Public\AnnouncementController::results');
$routes->post('/pengumuman/cek-hasil', 'Public\AnnouncementController::checkResult');
$routes->post('/hasil-seleksi/cek', 'Public\AnnouncementController::checkResult');
$routes->get('/kontak', 'Public\ContactController::index');
$routes->get('/brosur', 'Public\PageController::brochure');
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
    $routes->get('users', 'Admin\UserController::index', ['filter' => 'permission:users.manage']);
    $routes->get('users/create', 'Admin\UserController::create', ['filter' => 'permission:users.manage']);
    $routes->post('users/store', 'Admin\UserController::store', ['filter' => 'permission:users.manage']);
    $routes->get('users/(:num)/edit', 'Admin\UserController::edit/$1', ['filter' => 'permission:users.manage']);
    $routes->post('users/(:num)/update', 'Admin\UserController::update/$1', ['filter' => 'permission:users.manage']);
    $routes->post('users/(:num)/roles/assign', 'Admin\UserController::assignRole/$1', ['filter' => 'permission:users.manage']);
    $routes->post('users/(:num)/roles/(:num)/revoke', 'Admin\UserController::revokeRole/$1/$2', ['filter' => 'permission:users.manage']);
    $routes->post('users/(:num)/toggle', 'Admin\UserController::toggle/$1', ['filter' => 'permission:users.manage']);
    $routes->post('users/(:num)/delete', 'Admin\UserController::delete/$1', ['filter' => 'permission:users.manage']);

    // Access Configuration
    $routes->get('access', 'Admin\AccessController::index', ['filter' => 'permission:access.manage']);
    $routes->post('access/mode', 'Admin\AccessController::saveMode', ['filter' => 'permission:access.manage']);
    $routes->post('access/roles/store', 'Admin\AccessController::storeRole', ['filter' => 'permission:access.manage']);
    $routes->post('access/roles/(:num)/update', 'Admin\AccessController::updateRole/$1', ['filter' => 'permission:access.manage']);
    $routes->post('access/roles/(:num)/duplicate', 'Admin\AccessController::duplicateRole/$1', ['filter' => 'permission:access.manage']);
    $routes->post('access/roles/(:num)/delete', 'Admin\AccessController::deleteRole/$1', ['filter' => 'permission:access.manage']);

    // System Settings
    $routes->get('settings', 'Admin\SettingController::index', ['filter' => 'permission:settings.manage,manage_system']);
    $routes->post('settings/save', 'Admin\SettingController::save', ['filter' => 'permission:settings.manage,manage_system']);

    // Content Management
    $routes->get('content', 'Admin\ContentController::index', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('content/save', 'Admin\ContentController::save', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->get('teachers', 'Admin\ContentController::teachers', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->get('gallery', 'Admin\ContentController::gallery', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('content/gallery/upload', 'Admin\ContentController::galleryUpload', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('content/gallery/(:num)/update', 'Admin\ContentController::galleryUpdate/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('content/gallery/(:num)/delete', 'Admin\ContentController::galleryDelete/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('content/teachers/store', 'Admin\ContentController::teacherStore', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('content/teachers/(:num)/update', 'Admin\ContentController::teacherUpdate/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('content/teachers/(:num)/delete', 'Admin\ContentController::teacherDelete/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);

    // Academic Year Management
    $routes->get('academic-years', 'Admin\AcademicYearController::index', ['filter' => 'permission:academic_years.manage,manage_system']);
    $routes->post('academic-years/store', 'Admin\AcademicYearController::store', ['filter' => 'permission:academic_years.manage,manage_system']);
    $routes->post('academic-years/(:num)/activate', 'Admin\AcademicYearController::activate/$1', ['filter' => 'permission:academic_years.manage,manage_system']);
    $routes->post('academic-years/(:num)/update', 'Admin\AcademicYearController::update/$1', ['filter' => 'permission:academic_years.manage,manage_system']);
    $routes->post('academic-years/(:num)/archive', 'Admin\AcademicYearController::archive/$1', ['filter' => 'permission:academic_years.manage,manage_system']);
    $routes->post('academic-years/(:num)/delete', 'Admin\AcademicYearController::delete/$1', ['filter' => 'permission:academic_years.manage,manage_system']);

    // Banner Management
    $routes->get('banners', 'Admin\ContentController::banners', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('banners/store', 'Admin\ContentController::bannerStore', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('banners/(:num)/update', 'Admin\ContentController::bannerUpdate/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('banners/(:num)/delete', 'Admin\ContentController::bannerDelete/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);

    // Testimonial Management
    $routes->get('testimonials', 'Admin\ContentController::testimonials', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('testimonials/store', 'Admin\ContentController::testimonialStore', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('testimonials/(:num)/update', 'Admin\ContentController::testimonialUpdate/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('testimonials/(:num)/delete', 'Admin\ContentController::testimonialDelete/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);

    // Statistic Management
    $routes->get('statistics', 'Admin\ContentController::statistics', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('statistics/store', 'Admin\ContentController::statisticStore', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('statistics/(:num)/update', 'Admin\ContentController::statisticUpdate/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);
    $routes->post('statistics/(:num)/delete', 'Admin\ContentController::statisticDelete/$1', ['filter' => 'permission:public_content.manage,manage_public_homepage']);

    // FAQ Management
    $routes->get('faq', 'Admin\ContentController::faq', ['filter' => 'permission:public_content.manage,manage_faq']);
    $routes->post('faq/store', 'Admin\ContentController::faqStore', ['filter' => 'permission:public_content.manage,manage_faq']);
    $routes->post('faq/(:num)/update', 'Admin\ContentController::faqUpdate/$1', ['filter' => 'permission:public_content.manage,manage_faq']);
    $routes->post('faq/(:num)/delete', 'Admin\ContentController::faqDelete/$1', ['filter' => 'permission:public_content.manage,manage_faq']);

    // Jalur Management
    $routes->get('jalur', 'Admin\JalurController::index', ['filter' => 'permission:jalur.manage,manage_admission_paths']);
    $routes->post('jalur/(:num)/update', 'Admin\JalurController::update/$1', ['filter' => 'permission:jalur.manage,manage_admission_paths']);
    $routes->post('jalur/(:num)/toggle', 'Admin\JalurController::toggle/$1', ['filter' => 'permission:jalur.manage,manage_admission_paths']);

    // Gelombang Management
    $routes->get('gelombang', 'Admin\JalurController::gelombang', ['filter' => 'permission:gelombang.manage,manage_admission_paths']);
    $routes->post('gelombang/store', 'Admin\JalurController::gelombangStore', ['filter' => 'permission:gelombang.manage,manage_admission_paths']);
    $routes->post('gelombang/(:num)/update', 'Admin\JalurController::gelombangUpdate/$1', ['filter' => 'permission:gelombang.manage,manage_admission_paths']);
    $routes->post('gelombang/(:num)/delete', 'Admin\JalurController::gelombangDelete/$1', ['filter' => 'permission:gelombang.manage,manage_admission_paths']);

    // Document Requirement Builder
    $routes->get('document-requirements', 'Admin\DocumentRequirementController::index', ['filter' => 'permission:document_requirements.manage']);
    $routes->post('document-requirements/store', 'Admin\DocumentRequirementController::store', ['filter' => 'permission:document_requirements.manage']);
    $routes->post('document-requirements/(:num)/update', 'Admin\DocumentRequirementController::update/$1', ['filter' => 'permission:document_requirements.manage']);
    $routes->post('document-requirements/(:num)/delete', 'Admin\DocumentRequirementController::delete/$1', ['filter' => 'permission:document_requirements.manage']);

    // Announcement Management
    $routes->get('announcements', 'Admin\AnnouncementController::index', ['filter' => 'permission:public_content.manage,manage_announcements']);
    $routes->get('announcements/create', 'Admin\AnnouncementController::create', ['filter' => 'permission:public_content.manage,manage_announcements']);
    $routes->post('announcements/store', 'Admin\AnnouncementController::store', ['filter' => 'permission:public_content.manage,manage_announcements']);
    $routes->get('announcements/(:num)/edit', 'Admin\AnnouncementController::edit/$1', ['filter' => 'permission:public_content.manage,manage_announcements']);
    $routes->post('announcements/(:num)/update', 'Admin\AnnouncementController::update/$1', ['filter' => 'permission:public_content.manage,manage_announcements']);
    $routes->post('announcements/(:num)/delete', 'Admin\AnnouncementController::delete/$1', ['filter' => 'permission:public_content.manage,manage_announcements']);
    $routes->post('announcements/(:num)/publish', 'Admin\AnnouncementController::publish/$1', ['filter' => 'permission:public_content.manage,publish_content']);

    // Seleksi (Selection Results)
    $routes->get('seleksi', 'Admin\AnnouncementController::seleksi', ['filter' => 'permission:selection.manage,view_selection']);
    $routes->post('seleksi/hitung-ranking', 'Admin\AnnouncementController::calculateRanking', ['filter' => 'permission:selection.manage,calculate_ranking']);
    $routes->post('seleksi/(:num)/update', 'Admin\AnnouncementController::seleksiUpdate/$1', ['filter' => 'permission:selection.manage,set_selection_status,approve_selection']);

    // Backup & Restore
    $routes->get('backup', 'Admin\BackupController::index', ['filter' => 'permission:backup.manage,manage_system']);
    $routes->post('backup/create', 'Admin\BackupController::create', ['filter' => 'permission:backup.manage,manage_system']);
    $routes->post('backup/restore', 'Admin\BackupController::restore', ['filter' => 'permission:backup.manage,manage_system']);
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
    $routes->get('registrants', 'Operator\RegistrantController::index', ['filter' => 'permission:registrants.view,view_registrants']);
    $routes->get('registrants/(:num)', 'Operator\RegistrantController::show/$1', ['filter' => 'permission:registrants.view,view_registrants']);
    $routes->get('registrants/(:num)/edit', 'Operator\RegistrantController::edit/$1', ['filter' => 'permission:registrants.edit,edit_registrant']);
    $routes->post('registrants/(:num)/update', 'Operator\RegistrantController::update/$1', ['filter' => 'permission:registrants.edit,edit_registrant']);

    // Document Verification
    $routes->get('documents/(:num)', 'Operator\DocumentController::index/$1', ['filter' => 'permission:documents.verify,verify_documents,view_documents']);
    $routes->post('documents/(:num)/verify', 'Operator\DocumentController::verify/$1', ['filter' => 'permission:documents.verify,verify_documents']);
    $routes->get('documents/(:num)/view', 'Operator\DocumentController::view/$1', ['filter' => 'permission:documents.verify,download_documents,view_documents']);

    // Export
    $routes->get('export/excel', 'Operator\ExportController::excel', ['filter' => 'permission:exports.download,export_reports,export_dapodik_excel']);
    $routes->get('export/fpd/(:num)', 'Operator\ExportController::fpd/$1', ['filter' => 'permission:exports.download,print_fpd']);

    // Dapodik Validation
    $routes->get('dapodik', 'Operator\DapodikController::index', ['filter' => 'permission:dapodik.view,view_dapodik_checklist']);
    $routes->get('dapodik/(:num)', 'Operator\DapodikController::show/$1', ['filter' => 'permission:dapodik.view,view_dapodik_checklist']);
});

/*
 * -----------------------------------------------------------------------
 * Bendahara Routes (requires Bendahara/Admin role)
 * -----------------------------------------------------------------------
 */
$routes->group('bendahara', ['filter' => ['auth', 'role:bendahara,admin']], function ($routes) {
    $routes->get('/', 'Bendahara\PaymentController::index', ['filter' => 'permission:payments.view']);
    $routes->get('dashboard', 'Bendahara\PaymentController::index', ['filter' => 'permission:payments.view']);
    $routes->get('invoices', 'Bendahara\PaymentController::index', ['filter' => 'permission:payments.view']);
    $routes->get('invoices/(:num)', 'Bendahara\PaymentController::show/$1', ['filter' => 'permission:payments.view']);
    $routes->post('invoices/(:num)/payment', 'Bendahara\PaymentController::recordPayment/$1', ['filter' => 'permission:payments.verify']);
    $routes->post('invoices/(:num)/cancel', 'Bendahara\PaymentController::cancel/$1', ['filter' => 'permission:payments.cancel']);
    $routes->get('invoices/(:num)/slip', 'Bendahara\PaymentController::slip/$1', ['filter' => 'permission:payments.view']);
    $routes->get('export', 'Bendahara\PaymentController::export', ['filter' => 'permission:payments.export']);
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
    $routes->get('daftar', 'Pendaftar\RegistrationController::wizard', ['filter' => 'permission:registration.manage,submit_registration']);
    $routes->get('daftar/step/(:num)', 'Pendaftar\RegistrationController::step/$1', ['filter' => 'permission:registration.manage,submit_registration']);
    $routes->post('daftar/step/(:num)/save', 'Pendaftar\RegistrationController::saveStep/$1', ['filter' => 'permission:registration.manage,submit_registration']);
    $routes->post('daftar/submit', 'Pendaftar\RegistrationController::submit', ['filter' => 'permission:registration.manage,submit_registration']);

    // Documents
    $routes->get('dokumen', 'Pendaftar\DocumentController::index', ['filter' => 'permission:documents.manage_own,view_documents']);
    $routes->post('dokumen/upload', 'Pendaftar\DocumentController::upload', ['filter' => 'permission:documents.manage_own,view_documents']);
    $routes->post('dokumen/(:num)/delete', 'Pendaftar\DocumentController::delete/$1', ['filter' => 'permission:documents.manage_own,view_documents']);

    // Print
    $routes->get('cetak/bukti', 'Pendaftar\DocumentController::printBukti', ['filter' => 'permission:print_registration_card,registration.manage']);
    $routes->get('cetak/kartu', 'Pendaftar\DocumentController::printKartu', ['filter' => 'permission:print_registration_card,registration.manage']);
    $routes->get('cetak/skl', 'Pendaftar\DocumentController::printSkl', ['filter' => 'permission:print_registration_card,registration.manage']);
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
