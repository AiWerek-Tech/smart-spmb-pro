<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('additional_css') ?>
<style>
    /* Premium Styled Settings Pills */
    #settings-tabs .nav-link {
        border-radius: var(--sp-radius-md) !important;
        color: var(--sp-text-color) !important;
        font-weight: 500 !important;
        padding: 0.85rem 1.25rem !important;
        transition: all var(--sp-transition-fast) !important;
        border: 1px solid transparent !important;
        background: transparent !important;
    }

    #settings-tabs .nav-link:hover {
        background-color: rgba(var(--sp-primary-rgb), 0.04) !important;
        color: var(--sp-primary) !important;
    }

    #settings-tabs .nav-link.active {
        background: rgba(var(--sp-primary-rgb), 0.08) !important;
        color: var(--sp-primary) !important;
        font-weight: 600 !important;
        border-color: rgba(var(--sp-primary-rgb), 0.1) !important;
    }

    body.dark-mode #settings-tabs .nav-link {
        color: var(--sp-text-color) !important;
    }

    body.dark-mode #settings-tabs .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.04) !important;
        color: #ffffff !important;
    }

    body.dark-mode #settings-tabs .nav-link.active {
        background: rgba(var(--sp-primary-rgb), 0.15) !important;
        color: #ffffff !important;
        border-color: rgba(var(--sp-primary-rgb), 0.2) !important;
    }

    /* Premium Theme Card Styling */
    .theme-card {
        border: 2px solid var(--sp-border-color) !important;
        background-color: var(--sp-card-bg) !important;
        transition: all var(--sp-transition) !important;
        cursor: pointer;
        border-radius: var(--sp-radius-lg);
        position: relative;
        overflow: hidden;
    }
    
    .theme-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--sp-primary-rgb), 0.02) 0%, transparent 100%);
        opacity: 0;
        transition: opacity var(--sp-transition) !important;
    }

    .theme-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--sp-shadow-md);
        border-color: rgba(var(--sp-primary-rgb), 0.3) !important;
    }
    
    .theme-card:hover::before {
        opacity: 1;
    }

    .theme-card.active {
        border-color: var(--sp-primary) !important;
        background-color: rgba(var(--sp-primary-rgb), 0.04) !important;
        box-shadow: 0 0 0 4px rgba(var(--sp-primary-rgb), 0.08) !important;
    }

    body.dark-mode .theme-card {
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    body.dark-mode .theme-card.active {
        border-color: var(--sp-primary) !important;
        background-color: rgba(var(--sp-primary-rgb), 0.1) !important;
        box-shadow: 0 0 0 4px rgba(var(--sp-primary-rgb), 0.15) !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in">
    <!-- Header Title (Premium Aesthetics) -->
    <div class="col-12 mb-4">
        <div>
            <h3 class="mb-1 fw-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">Konfigurasi Sistem</h3>
            <p class="text-muted mb-0">Kelola identitas sekolah, tahun ajaran aktif, peta lokasi, serta tampilan visual dasbor secara fleksibel.</p>
        </div>
    </div>

    <!-- Main Container Row inside unified Page layout -->
    <div class="col-12">
        <form method="POST" action="<?= base_url('admin/settings/save') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Validation Errors Overlay -->
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Navigation Tabs (Native Bootstrap 5 Left Sidebar Mode) -->
                <div class="col-lg-3 mb-4">
                    <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-3 px-4 mb-2 d-flex align-items-center border-0" id="nav-general-tab" data-bs-toggle="pill" data-bs-target="#nav-general" type="button" role="tab" aria-controls="nav-general" aria-selected="true">
                            <i data-lucide="school" class="me-3" style="width: 18px; height: 18px;"></i> Profil & Tahun Ajaran
                        </button>
                        <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center border-0" id="nav-contact-tab" data-bs-toggle="pill" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">
                            <i data-lucide="map-pin" class="me-3" style="width: 18px; height: 18px;"></i> Kontak & Peta
                        </button>
                        <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center border-0" id="nav-accreditation-tab" data-bs-toggle="pill" data-bs-target="#nav-accreditation" type="button" role="tab" aria-controls="nav-accreditation" aria-selected="false">
                            <i data-lucide="award" class="me-3" style="width: 18px; height: 18px;"></i> Akreditasi
                        </button>
                        <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center border-0" id="nav-theme-tab" data-bs-toggle="pill" data-bs-target="#nav-theme" type="button" role="tab" aria-controls="nav-theme" aria-selected="false">
                            <i data-lucide="palette" class="me-3" style="width: 18px; height: 18px;"></i> Tema & Tampilan
                        </button>
                        <button class="nav-link text-start py-3 px-4 d-flex align-items-center border-0" id="nav-app-tab" data-bs-toggle="pill" data-bs-target="#nav-app" type="button" role="tab" aria-controls="nav-app" aria-selected="false">
                            <i data-lucide="badge-info" class="me-3" style="width: 18px; height: 18px;"></i> Aplikasi
                        </button>
                    </div>
                </div>

                <!-- Content Panels (Right side inside standalone Card) -->
                <div class="col-lg-9">
                    <div class="card shadow-sm border">
                        <div class="card-body p-4">
                            <div class="tab-content" id="settings-tab-content">
                                <!-- PANEL 1: GENERAL PROFILE -->
                                <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                                    <div class="mb-4">
                                        <h5 class="text-primary fw-bold d-flex align-items-center mb-1">
                                            <i data-lucide="school" class="me-2" style="width: 20px; height: 20px;"></i> Profil Sekolah & Tahun Ajaran
                                        </h5>
                                        <p class="text-muted small mb-0">Atur logo sekolah, slogan, serta periode tahun akademik yang aktif saat ini.</p>
                                    </div>
                                    <hr class="mb-4">

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="academic_year" class="form-label fw-bold small">Tahun Ajaran Aktif <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="academic_year" name="academic_year" value="<?= esc(old('academic_year', $settings['academic_year'] ?? '')) ?>" placeholder="Contoh: 2026/2027" required>
                                            <small class="text-muted">Gunakan format YYYY/YYYY (contoh: 2026/2027).</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="school_name" class="form-label fw-bold small">Nama Sekolah <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="school_name" name="school_name" value="<?= esc(old('school_name', $settings['school_name'] ?? '')) ?>" placeholder="Masukkan nama sekolah" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="tagline" class="form-label fw-bold small">Slogan / Tagline Sekolah</label>
                                        <input type="text" class="form-control" id="tagline" name="tagline" value="<?= esc(old('tagline', $settings['tagline'] ?? '')) ?>" placeholder="Masukkan slogan/tagline">
                                    </div>

                                    <!-- Logo Upload View -->
                                    <div class="row align-items-center p-3 rounded border g-3 bg-light">
                                        <div class="col-md-3 text-center">
                                            <?php if (!empty($settings['school_logo'])): ?>
                                                <img src="<?= base_url(esc($settings['school_logo'])) ?>" class="img-fluid rounded border shadow-sm p-1 bg-white" style="max-height: 100px; max-width: 100%;" alt="Logo Sekolah">
                                            <?php else: ?>
                                                <div class="bg-white rounded border d-flex align-items-center justify-content-center p-3 text-muted mx-auto" style="height: 100px; width: 100px;">
                                                    <i data-lucide="image" style="width: 32px; height: 32px;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-9">
                                            <label for="school_logo_file" class="form-label fw-bold small">Pilih Logo Sekolah Baru</label>
                                            <input class="form-control" type="file" id="school_logo_file" name="school_logo_file">
                                            <small class="text-muted d-block mt-1">Format gambar: JPG, PNG, atau GIF. Ukuran maksimum: 2 MB.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- PANEL 2: CONTACT & ADDRESS -->
                                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                                    <div class="mb-4">
                                        <h5 class="text-primary fw-bold d-flex align-items-center mb-1">
                                            <i data-lucide="map-pin" class="me-2" style="width: 20px; height: 20px;"></i> Informasi Kontak & Peta
                                        </h5>
                                        <p class="text-muted small mb-0">Kelola info kontak operasional sekolah serta kode pemetaan lokasi Google Maps.</p>
                                    </div>
                                    <hr class="mb-4">

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="email" class="form-label fw-bold small">Alamat Email Sekolah <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i data-lucide="mail" style="width: 16px; height: 16px;"></i></span>
                                                <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email', $settings['email'] ?? '')) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label fw-bold small">Telepon Sekolah <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i data-lucide="phone" style="width: 16px; height: 16px;"></i></span>
                                                <input type="text" class="form-control" id="phone" name="phone" value="<?= esc(old('phone', $settings['phone'] ?? '')) ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="whatsapp" class="form-label fw-bold small">Nomor WhatsApp Sekolah</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i data-lucide="message-square" style="width: 16px; height: 16px;"></i></span>
                                            <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?= esc(old('whatsapp', $settings['whatsapp'] ?? '')) ?>" placeholder="Contoh: 6281234567890">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label fw-bold small">Alamat Lengkap <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="address" name="address" rows="3" required placeholder="Masukkan alamat lengkap sekolah..."><?= esc(old('address', $settings['address'] ?? '')) ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="maps_embed" class="form-label fw-bold small">Embed Google Maps</label>
                                        <textarea class="form-control font-monospace" id="maps_embed" name="maps_embed" rows="3" placeholder="Tempel kode embed iframe dari Google Maps (Bagikan &gt; Sematkan peta)"><?= esc(old('maps_embed', $settings['maps_embed'] ?? '')) ?></textarea>
                                        <small class="text-muted">Masukkan kode <code>&lt;iframe&gt;</code> sematan lokasi dari Google Maps.</small>
                                    </div>
                                </div>

                                <!-- PANEL 3: ACCREDITATION -->
                                <div class="tab-pane fade" id="nav-accreditation" role="tabpanel" aria-labelledby="nav-accreditation-tab">
                                    <div class="mb-4">
                                        <h5 class="text-primary fw-bold d-flex align-items-center mb-1">
                                            <i data-lucide="award" class="me-2" style="width: 20px; height: 20px;"></i> Data Akreditasi Sekolah
                                        </h5>
                                        <p class="text-muted small mb-0">Atur predikat akreditasi resmi sekolah serta tahun penetapan terbarunya.</p>
                                    </div>
                                    <hr class="mb-4">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="accreditation" class="form-label fw-bold small">Predikat Akreditasi</label>
                                            <select class="form-select" name="accreditation" id="accreditation">
                                                <option value="A" <?= old('accreditation', $settings['accreditation'] ?? '') === 'A' ? 'selected' : '' ?>>Akreditasi A (Sangat Baik)</option>
                                                <option value="B" <?= old('accreditation', $settings['accreditation'] ?? '') === 'B' ? 'selected' : '' ?>>Akreditasi B (Baik)</option>
                                                <option value="C" <?= old('accreditation', $settings['accreditation'] ?? '') === 'C' ? 'selected' : '' ?>>Akreditasi C (Cukup)</option>
                                                <option value="Tidak Terakreditasi" <?= old('accreditation', $settings['accreditation'] ?? '') === 'Tidak Terakreditasi' ? 'selected' : '' ?>>Belum/Tidak Terakreditasi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="accreditation_year" class="form-label fw-bold small">Tahun Akreditasi</label>
                                            <input type="number" class="form-control" name="accreditation_year" id="accreditation_year" value="<?= esc(old('accreditation_year', $settings['accreditation_year'] ?? '')) ?>" min="2000" max="2030" placeholder="Contoh: 2025">
                                        </div>
                                    </div>
                                </div>

                                <!-- PANEL 4: THEME & APPEARANCE -->
                                <div class="tab-pane fade" id="nav-theme" role="tabpanel" aria-labelledby="nav-theme-tab">
                                    <div class="mb-4">
                                        <h5 class="text-primary fw-bold d-flex align-items-center mb-1">
                                            <i data-lucide="palette" class="me-2" style="width: 20px; height: 20px;"></i> Pengaturan Tema Warna Global
                                        </h5>
                                        <p class="text-muted small mb-0">Sesuaikan identitas visual aplikasi. Warna tema default akan diterapkan ke seluruh halaman publik, portal login, dan dasbor.</p>
                                    </div>
                                    <hr class="mb-4">

                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="form-label fw-bold d-block mb-3">Pilihan Tema Warna Default <span class="text-danger">*</span></label>
                                            
                                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                                <!-- Purple Theme (Default) -->
                                                <div class="col">
                                                    <div class="card h-100 theme-card p-3" data-color="purple">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #6366f1; color: white; flex-shrink: 0;">
                                                                <i data-lucide="palette" style="width: 18px; height: 18px;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-0">Indigo / Deep Purple</h6>
                                                                <small class="text-muted">Modern, premium & visioner (Default)</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input theme-radio" type="radio" name="theme_color" id="theme_purple" value="purple" <?= old('theme_color', $settings['theme_color'] ?? 'purple') === 'purple' ? 'checked' : '' ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Navy Theme -->
                                                <div class="col">
                                                    <div class="card h-100 theme-card p-3" data-color="navy">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #1e3a8a; color: white; flex-shrink: 0;">
                                                                <i data-lucide="palette" style="width: 18px; height: 18px;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-0">Navy Blue</h6>
                                                                <small class="text-muted">Profesional, elegan & akademis</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input theme-radio" type="radio" name="theme_color" id="theme_navy" value="navy" <?= old('theme_color', $settings['theme_color'] ?? 'purple') === 'navy' ? 'checked' : '' ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Light Blue Theme -->
                                                <div class="col">
                                                    <div class="card h-100 theme-card p-3" data-color="lightblue">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #0284c7; color: white; flex-shrink: 0;">
                                                                <i data-lucide="palette" style="width: 18px; height: 18px;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-0">Light Blue</h6>
                                                                <small class="text-muted">Teknologis, bersih & ramah</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input theme-radio" type="radio" name="theme_color" id="theme_lightblue" value="lightblue" <?= old('theme_color', $settings['theme_color'] ?? 'purple') === 'lightblue' ? 'checked' : '' ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Emerald Theme -->
                                                <div class="col">
                                                    <div class="card h-100 theme-card p-3" data-color="emerald">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #059669; color: white; flex-shrink: 0;">
                                                                <i data-lucide="palette" style="width: 18px; height: 18px;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-0">Emerald Green</h6>
                                                                <small class="text-muted">Segar, dinamis & terpercaya</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input theme-radio" type="radio" name="theme_color" id="theme_emerald" value="emerald" <?= old('theme_color', $settings['theme_color'] ?? 'purple') === 'emerald' ? 'checked' : '' ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Crimson Red Theme -->
                                                <div class="col">
                                                    <div class="card h-100 theme-card p-3" data-color="red">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #dc2626; color: white; flex-shrink: 0;">
                                                                <i data-lucide="palette" style="width: 18px; height: 18px;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-0">Crimson Red</h6>
                                                                <small class="text-muted">Berani, bersemangat & berwibawa</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input theme-radio" type="radio" name="theme_color" id="theme_red" value="red" <?= old('theme_color', $settings['theme_color'] ?? 'purple') === 'red' ? 'checked' : '' ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sunset Orange Theme -->
                                                <div class="col">
                                                    <div class="card h-100 theme-card p-3" data-color="orange">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #ea580c; color: white; flex-shrink: 0;">
                                                                <i data-lucide="palette" style="width: 18px; height: 18px;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-0">Sunset Orange</h6>
                                                                <small class="text-muted">Kreatif, enerjik & hangat</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input theme-radio" type="radio" name="theme_color" id="theme_orange" value="orange" <?= old('theme_color', $settings['theme_color'] ?? 'purple') === 'orange' ? 'checked' : '' ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Rose Pink Theme -->
                                                <div class="col">
                                                    <div class="card h-100 theme-card p-3" data-color="rose">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #db2777; color: white; flex-shrink: 0;">
                                                                <i data-lucide="palette" style="width: 18px; height: 18px;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-0">Rose Pink</h6>
                                                                <small class="text-muted">Elegan, lembut & prestisius</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input theme-radio" type="radio" name="theme_color" id="theme_rose" value="rose" <?= old('theme_color', $settings['theme_color'] ?? 'purple') === 'rose' ? 'checked' : '' ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PANEL 5: APPLICATION INFO -->
                                <div class="tab-pane fade" id="nav-app" role="tabpanel" aria-labelledby="nav-app-tab">
                                    <div class="mb-4">
                                        <h5 class="text-primary fw-bold d-flex align-items-center mb-1">
                                            <i data-lucide="badge-info" class="me-2" style="width: 20px; height: 20px;"></i> Informasi Aplikasi
                                        </h5>
                                        <p class="text-muted small mb-0">Identitas rilis produksi dan kontak developer resmi.</p>
                                    </div>
                                    <hr class="mb-4">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 rounded border bg-light h-100">
                                                <div class="text-muted small mb-1">Nama Aplikasi</div>
                                                <div class="fw-bold text-dark">Smart SPMB Pro</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 rounded border bg-light h-100">
                                                <div class="text-muted small mb-1">Versi Produksi</div>
                                                <div class="fw-bold text-dark">v<?= esc($settings['app_version'] ?? '1.0.0') ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded border bg-light h-100">
                                                <div class="text-muted small mb-1">Developer</div>
                                                <div class="fw-bold text-dark"><?= esc($settings['developer_name'] ?? 'AiWerek Tech') ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded border bg-light h-100">
                                                <div class="text-muted small mb-1">Kontak</div>
                                                <a class="fw-bold text-decoration-none" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['developer_phone'] ?? '082190822641') ?>" target="_blank" rel="noopener">
                                                    <?= esc($settings['developer_phone'] ?? '082190822641') ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 rounded border bg-light h-100">
                                                <div class="text-muted small mb-1">Email</div>
                                                <a class="fw-bold text-decoration-none" href="mailto:<?= esc($settings['developer_email'] ?? 'aiwerek.tech@gmail.com') ?>">
                                                    <?= esc($settings['developer_email'] ?? 'aiwerek.tech@gmail.com') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Save Footer -->
                        <div class="card-footer bg-light border-top d-flex justify-content-end p-3">
                            <button type="submit" class="btn btn-primary px-4 py-2 d-flex align-items-center shadow-sm">
                                <i data-lucide="save" class="me-2" style="width: 18px; height: 18px;"></i> Simpan Konfigurasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Define on window scope so navbar color switcher can easily sync with settings radios
window.updateThemeCardStyles = function() {
    $('.theme-card').removeClass('active');
    $('.theme-radio:checked').closest('.theme-card').addClass('active');
};

$(document).ready(function() {
        if (window.SpTheme) {
            SpTheme.syncStoredThemeFromServer('<?= esc($settings['theme_color'] ?? 'purple') ?>');
        }

    function previewThemeColor(color) {
        if (window.SpTheme) {
            SpTheme.applyThemeColor(color, { persist: false });
        }
    }

    function initAccreditationSelect2() {
        const $accreditation = $('#accreditation');
        if (!$accreditation.length || typeof $.fn.select2 !== 'function') {
            return;
        }

        if ($accreditation.hasClass('select2-hidden-accessible')) {
            return;
        }

        try {
            $accreditation.select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#nav-accreditation')
            });
        } catch (e) {
            console.error('Select2 accreditation init failed:', e);
        }
    }

    // Re-init Select2 when hidden tab becomes visible; refresh layout on tab change
    $('#settings-tabs button').on('shown.bs.tab', function () {
        const target = $(this).attr('data-bs-target');
        if (target === '#nav-accreditation') {
            initAccreditationSelect2();
        }
        window.dispatchEvent(new Event('resize'));
        if (typeof lucide !== 'undefined') {
            try { lucide.createIcons(); } catch (e) { /* ignore */ }
        }
    });

    // Theme card clicking binds to hidden radio checks
    $('.theme-card').on('click', function() {
        $(this).find('.theme-radio').prop('checked', true);
        window.updateThemeCardStyles();
        previewThemeColor($(this).data('color'));
    });

    $('.theme-radio').on('change', function() {
        previewThemeColor(this.value);
    });

    // Initial active theme styling load
    window.updateThemeCardStyles();

    // Icons inside tab panels (layout already renders sidebar/topbar icons)
    if (typeof lucide !== 'undefined') {
        try {
            lucide.createIcons();
        } catch (e) {
            console.error('Lucide explicit settings initialization failed:', e);
        }
    }
});
</script>
<?= $this->endSection() ?>
