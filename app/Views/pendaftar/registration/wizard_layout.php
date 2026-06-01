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
    <meta name="description" content="Smart SPMB Pro v<?= esc($appInfo->version) ?> - Form Pendaftaran Online">
    <meta name="author" content="<?= esc($appInfo->developer) ?>">
    <meta name="application-name" content="<?= esc($appInfo->name) ?>">
    <meta name="version" content="<?= esc($appInfo->version) ?>">
    <title><?= $title ?? 'Pendaftaran' ?> - Smart SPMB Pro</title>
    
    <script src="<?= base_url('assets/js/theme-sync.js') ?>"></script>
    <script>
        SpTheme.init({ serverTheme: '<?= esc($globalThemeColor) ?>', scope: 'dashboard' });
    </script>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/foundation.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">

    <!-- Lucide Icons with local fallback -->
    <script src="https://unpkg.com/lucide@0.309.0/dist/umd/lucide.min.js"></script>
    <script>
        if (typeof lucide === 'undefined') {
            document.write('<script src="<?= base_url('assets/js/lucide.min.js') ?>"><\/script>');
        }
    </script>
    
    <style>
        body {
            background-color: var(--sp-body-bg);
            min-height: 100vh;
            padding: 40px 0;
        }

        .wizard-container {
            background: var(--sp-card-bg);
            border-radius: var(--sp-radius-lg);
            box-shadow: var(--sp-shadow-lg);
            border: 1px solid var(--sp-card-border);
            overflow: hidden;
            max-width: 960px;
            margin: 0 auto;
        }

        .wizard-header {
            background: linear-gradient(135deg, var(--sp-primary) 0%, var(--sp-accent) 100%);
            color: white;
            padding: 36px 30px;
            text-align: center;
            position: relative;
        }

        .wizard-header h2 {
            margin: 0;
            font-weight: 800;
            font-size: 1.75rem;
            margin-bottom: 8px;
            color: #ffffff !important;
            letter-spacing: -0.03em;
        }

        .wizard-header p {
            margin: 0;
            opacity: 0.85;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: var(--sp-space-md);
        }

        .required-field::after {
            content: ' *';
            color: var(--sp-danger);
            font-weight: bold;
        }

        .help-text {
            font-size: 0.8rem;
            color: var(--sp-text-muted);
            margin-top: 4px;
        }

        .file-upload-area {
            border: 2px dashed rgba(var(--sp-primary-rgb), 0.3);
            border-radius: var(--sp-radius-md);
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all var(--sp-transition);
            background: rgba(var(--sp-primary-rgb), 0.02);
        }

        .file-upload-area:hover {
            background: rgba(var(--sp-primary-rgb), 0.05);
            border-color: var(--sp-primary);
        }

        .file-upload-area i, .file-upload-area svg {
            width: 32px;
            height: 32px;
            color: var(--sp-primary);
            margin-bottom: 12px;
        }

        .wizard-footer {
            display: flex;
            gap: 12px;
            padding: 24px 30px;
            background: rgba(var(--sp-primary-rgb), 0.02);
            border-top: 1px solid var(--sp-card-border);
        }

        body.dark-mode .wizard-footer {
            background: rgba(255, 255, 255, 0.02);
        }

        body.dark-mode .wizard-container {
            box-shadow: var(--sp-shadow-lg);
        }

        @media (max-width: 768px) {
            .wizard-container {
                border-radius: 0;
                margin: -20px 0;
            }
            .wizard-footer {
                padding: 16px 20px;
            }
        }
    </style>
    <?= $this->renderSection('additional_css') ?>
</head>
<body>
    <script>SpTheme.initDarkMode();</script>
    <div class="container py-2">
        <div class="wizard-container">
            <!-- Header -->
            <div class="wizard-header">
                <h2><i data-lucide="graduation-cap" class="d-inline-block align-middle me-2" style="width: 28px; height: 28px; color: #fff;"></i>Form Pendaftaran Online</h2>
                <p>Selesaikan semua langkah pengisian data calon peserta untuk menyelesaikan pendaftaran</p>
            </div>

            <!-- Steps timeline progress tracker -->
            <div class="step-container mt-4 px-4">
                <?php 
                $stepsList = [
                    1 => 'Identitas',
                    2 => 'Alamat',
                    3 => 'Data Ayah',
                    4 => 'Data Ibu',
                    5 => 'Data Wali',
                    6 => 'Periodik',
                    7 => 'Prestasi',
                    8 => 'Dokumen'
                ];
                $activeStep = $step ?? 1;
                foreach ($stepsList as $i => $label):
                    $statusClass = '';
                    if ($i == $activeStep) {
                        $statusClass = 'active';
                    } elseif ($i < $activeStep) {
                        $statusClass = 'done';
                    }
                ?>
                <div class="step-item <?= $statusClass ?>">
                    <div class="step-circle">
                        <?php if ($i < $activeStep): ?>
                            <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                        <?php else: ?>
                            <?= $i ?>
                        <?php endif; ?>
                    </div>
                    <div class="step-label d-none d-md-block"><?= $label ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="px-4 py-3">
                <hr class="my-1" style="border-color: var(--sp-card-border);">
            </div>

            <!-- Content Slot -->
            <div class="px-4 pb-2">
                <?= $this->renderSection('step_content') ?>
            </div>

            <!-- Footer Navigation -->
            <div class="wizard-footer">
                <button type="button" class="btn btn-secondary d-flex align-items-center" id="prevBtn" style="display: none;">
                    <i data-lucide="arrow-left" class="me-2" style="width: 16px; height: 16px;"></i> Kembali
                </button>
                <button type="button" class="btn btn-primary d-flex align-items-center ms-auto" id="nextBtn">
                    Lanjut <i data-lucide="arrow-right" class="ms-2" style="width: 16px; height: 16px;"></i>
                </button>
                <button type="button" class="btn btn-success d-flex align-items-center ms-auto" id="submitBtn" style="display: none;">
                    <i data-lucide="check" class="me-2" style="width: 16px; height: 16px;"></i> Selesai & Submit
                </button>
            </div>
        </div>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize current step based on PHP variable
        let currentStep = <?= $step ?? 1 ?>;

        function swalFire(options) {
            const themed = window.SpTheme ? SpTheme.mergeSwalOptions(options) : Object.assign({ confirmButtonColor: '#6366f1', cancelButtonColor: '#64748b' }, options);
            return Swal.fire(themed);
        }

        function themePrimaryColor() {
            return window.SpTheme ? SpTheme.getThemePrimary() : getComputedStyle(document.documentElement).getPropertyValue('--sp-primary').trim();
        }
        
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Update footer buttons based on step
        function updateFooterButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            if (currentStep === 1) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'block';
            }

            if (currentStep === 8) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
            } else {
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            }
        }

        // Previous button (redirects to previous step page)
        document.getElementById('prevBtn')?.addEventListener('click', function() {
            if (currentStep > 1) {
                window.location.href = `<?= base_url('/pendaftar/daftar/step/') ?>${currentStep - 1}`;
            }
        });

        // Next button (saves step via AJAX, then redirects to next step page)
        document.getElementById('nextBtn')?.addEventListener('click', function() {
            if (currentStep < 8) {
                // Show loading spinner during save
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan data langkah Anda',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                saveCurrentStep().then(success => {
                    if (success) {
                        Swal.close();
                        window.location.href = `<?= base_url('/pendaftar/daftar/step/') ?>${currentStep + 1}`;
                    }
                });
            }
        });

        // Submit button
        document.getElementById('submitBtn')?.addEventListener('click', function() {
            submitRegistration();
        });

        // Save current step via AJAX
        async function saveCurrentStep() {
            const formEl = document.getElementById(`stepForm${currentStep}`);
            if (!formEl) return true; // No form to save for this step
            
            const formData = new FormData(formEl);
            
            try {
                // Post to CI4 route: /pendaftar/daftar/step/{currentStep}/save
                const response = await fetch(`<?= base_url('/pendaftar/daftar/step/') ?>${currentStep}/save`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (!result.success) {
                    swalFire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: '<ul style="text-align: left; font-size: 0.9rem; margin-top: 8px;">' + 
                              Object.entries(result.errors || {}).map(([key, msg]) => `<li>${msg}</li>`).join('') +
                              '</ul>'
                    });
                    return false;
                }

                return true;
            } catch (error) {
                console.error('Error saving step:', error);
                swalFire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Sistem gagal menyimpan data. Harap coba lagi.'
                });
                return false;
            }
        }

        // Submit registration
        async function submitRegistration() {
            const jalurRadio = document.querySelector('input[name="jalur_id"]:checked');
            const jalurId = jalurRadio?.value;
            
            if (!jalurId) {
                swalFire({
                    icon: 'warning',
                    title: 'Jalur Belum Dipilih',
                    text: 'Silakan pilih salah satu jalur pendaftaran terlebih dahulu.'
                });
                return;
            }

            Swal.fire({
                title: 'Kunci & Kirim Pendaftaran?',
                text: "Data yang sudah dikirim tidak dapat diubah kembali. Pastikan seluruh informasi yang Anda isi sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kirim Sekarang!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Pendaftaran...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch('<?= base_url('/pendaftar/daftar/submit') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                'jalur_id': jalurId,
                                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                            })
                        });

                        const result = await response.json();

                        if (!result.success) {
                            swalFire({
                                icon: 'error',
                                title: 'Pendaftaran Gagal',
                                text: result.message
                            });
                            return;
                        }

                        // Success - redirect to dashboard with sweet confirmation
                        swalFire({
                            icon: 'success',
                            title: 'Pendaftaran Berhasil!',
                            html: `<p>Selamat, pendaftaran Anda telah diterima.</p>
                                   <p>Nomor Pendaftaran Anda:</p>
                                   <h3 style="color: ${themePrimaryColor()}; font-weight: 800; letter-spacing: 1px; font-family: 'Plus Jakarta Sans', sans-serif;">${result.registrationNumber}</h3>
                                   <p style="font-size: 0.85rem;" class="text-muted">Simpan nomor ini untuk melakukan cek hasil pengumuman kelulusan.</p>`,
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = '<?= base_url('/pendaftar/dashboard') ?>';
                        });
                    } catch (error) {
                        console.error('Error submitting registration:', error);
                        swalFire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Terjadi kesalahan sistem saat memproses pendaftaran Anda.'
                        });
                    }
                }
            });
        }

        // Initialize on page load
        updateFooterButtons();
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
