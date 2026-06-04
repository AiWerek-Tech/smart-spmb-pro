<?php
$settingModel = new \App\Models\SettingModel();
$globalThemeColor = $settingModel->getValue('theme_color', 'purple');
$schoolName = $settingModel->getValue('school_name', 'Smart SPMB Pro');
$schoolLogo = $settingModel->getValue('school_logo', '');
$schoolLogoUrl = !empty($schoolLogo) ? base_url($schoolLogo) : '';
$appInfo = config('AppInfo');
$registrationGate = $registrationGate ?? ['is_open' => true, 'status' => 'unconfigured', 'message' => ''];
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
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/foundation.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin-dashboard.css') ?>" rel="stylesheet">

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
            letter-spacing: 0;
        }

        .wizard-header p {
            margin: 0;
            opacity: 0.85;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .wizard-school-brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 14px;
            color: #ffffff;
            font-weight: 700;
        }

        .wizard-school-logo {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.24);
            overflow: hidden;
        }

        .wizard-school-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
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

        .wizard-nav-btn {
            display: inline-flex;
            justify-content: center;
        }

        body.dark-mode .wizard-footer {
            background: rgba(255, 255, 255, 0.02);
        }

        body.dark-mode .wizard-container {
            box-shadow: var(--sp-shadow-lg);
        }

        @media (max-width: 768px) {
            body {
                min-height: 100dvh;
                padding: 0;
                padding-bottom: calc(82px + env(safe-area-inset-bottom, 0px));
                overflow-x: hidden;
            }

            .container {
                max-width: 100%;
                padding: 0;
            }

            .wizard-container {
                border-radius: 0;
                min-height: 100dvh;
                margin: 0;
                box-shadow: none;
                border: 0;
            }

            .wizard-header {
                position: sticky;
                top: 0;
                z-index: 30;
                padding: calc(18px + env(safe-area-inset-top, 0px)) 20px 18px;
                text-align: left;
            }

            .wizard-school-brand {
                justify-content: flex-start;
                margin-bottom: 10px;
            }

            .wizard-header h2 {
                font-size: 1.25rem;
                line-height: 1.2;
            }

            .wizard-header p {
                font-size: 0.82rem;
            }

            .step-container {
                justify-content: flex-start;
                overflow-x: auto;
                gap: 14px;
                padding: 18px 18px 6px !important;
                margin-bottom: 6px;
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
            }

            .step-container::-webkit-scrollbar {
                display: none;
            }

            .step-container::before {
                display: none;
            }

            .step-item {
                flex: 0 0 48px;
                scroll-snap-align: start;
            }

            .step-circle {
                width: 44px;
                height: 44px;
            }

            .wizard-container > .px-4,
            .wizard-container > .px-4.pb-2 {
                padding-left: 18px !important;
                padding-right: 18px !important;
            }

            .form-control,
            .form-select,
            .btn {
                min-height: 48px;
                border-radius: 14px;
            }

            .form-check-input {
                width: 1.5rem;
                height: 1.5rem;
                margin-top: 0.1rem;
            }

            .form-check {
                min-height: 44px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .file-upload-area {
                padding: 24px 16px;
                border-radius: 18px;
            }

            .wizard-footer {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 40;
                padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
                background: rgba(var(--sp-body-bg-rgb, 248, 250, 252), 0.94);
                backdrop-filter: blur(22px);
                -webkit-backdrop-filter: blur(22px);
                box-shadow: 0 -18px 42px rgba(15, 23, 42, 0.12);
            }

            .wizard-footer .btn {
                flex: 1;
                justify-content: center;
            }

            .wizard-footer .btn.ms-auto {
                margin-left: 0 !important;
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
                <div class="wizard-school-brand">
                    <span class="wizard-school-logo">
                        <?php if ($schoolLogoUrl): ?>
                            <img src="<?= esc($schoolLogoUrl) ?>" alt="Logo <?= esc($schoolName) ?>">
                        <?php else: ?>
                            <i data-lucide="graduation-cap" style="width: 24px; height: 24px; color: #fff;"></i>
                        <?php endif; ?>
                    </span>
                    <span><?= esc($schoolName) ?></span>
                </div>
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

            <?php if (($registrationGate['status'] ?? '') === 'open'): ?>
                <div class="px-4 pb-2">
                    <div class="alert alert-success border-0 d-flex align-items-start mb-0">
                        <i data-lucide="calendar-check" class="me-2 mt-1 flex-shrink-0" style="width: 18px; height: 18px;"></i>
                        <div>
                            <strong>Jadwal Pendaftaran Aktif</strong>
                            <p class="mb-0 small"><?= esc($registrationGate['message'] ?? 'Pendaftaran sedang dibuka.') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Content Slot -->
            <div class="px-4 pb-2">
                <?= $this->renderSection('step_content') ?>
            </div>

            <!-- Footer Navigation -->
            <div class="wizard-footer align-items-center">
                <button type="button" class="btn btn-outline-secondary wizard-nav-btn align-items-center" id="homeBtn">
                    <i data-lucide="home" class="me-2" style="width: 16px; height: 16px;"></i> Beranda
                </button>
                <button type="button" class="btn btn-secondary wizard-nav-btn align-items-center" id="prevBtn" style="display: none;">
                    <i data-lucide="arrow-left" class="me-2" style="width: 16px; height: 16px;"></i> Kembali
                </button>
                
                <!-- Status Draft Indikator -->
                <div class="d-flex align-items-center ms-3 text-muted small" id="draftStatus">
                    <i data-lucide="cloud" class="me-1 text-success" style="width:14px;height:14px;"></i> Draft tersimpan
                </div>

                <button type="button" class="btn btn-primary wizard-nav-btn align-items-center ms-auto" id="nextBtn">
                    Lanjut <i data-lucide="arrow-right" class="ms-2" style="width: 16px; height: 16px;"></i>
                </button>
                <button type="button" class="btn btn-success wizard-nav-btn align-items-center ms-auto" id="submitBtn" style="display: none;">
                    <i data-lucide="check" class="me-2" style="width: 16px; height: 16px;"></i> Selesai & Submit
                </button>
            </div>

            <!-- Footer Copyright -->
            <div class="text-center py-3 mt-2 border-top">
                <small class="text-muted">&copy; <?= date('Y') ?> <?= esc($settingModel->getValue('school_name', 'Smart SPMB Pro')) ?> — Sistem Penerimaan Murid Baru</small>
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
        const userId = <?= (int)session()->get('user_id') ?>;
        const draftKey = `spmb_draft_step_${currentStep}_user_${userId}`;
        const draftTimestampKey = `spmb_draft_step_${currentStep}_user_${userId}_ts`;
        let isDirty = false;

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
                prevBtn.style.display = 'inline-flex';
            }

            if (currentStep === 8) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-flex';
            } else {
                nextBtn.style.display = 'inline-flex';
                submitBtn.style.display = 'none';
            }
        }

        // Previous button (redirects to previous step page)
        document.getElementById('prevBtn')?.addEventListener('click', function() {
            if (currentStep > 1) {
                window.location.href = `<?= base_url('/pendaftar/daftar/step/') ?>${currentStep - 1}`;
            }
        });

        document.getElementById('homeBtn')?.addEventListener('click', async function() {
            const formEl = document.getElementById(`stepForm${currentStep}`);
            if (formEl) {
                const saved = await saveCurrentStep();
                if (!saved) {
                    const leaveAnyway = await swalFire({
                        icon: 'question',
                        title: 'Kembali ke Beranda?',
                        text: 'Perubahan terbaru belum berhasil disimpan. Draft terakhir yang sudah tersimpan tetap aman.',
                        showCancelButton: true,
                        confirmButtonText: 'Tetap ke Beranda',
                        cancelButtonText: 'Lanjut Mengisi'
                    });

                    if (!leaveAnyway.isConfirmed) {
                        return;
                    }
                }
            }

            window.location.href = '<?= base_url('/pendaftar/dashboard') ?>';
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
                        clearLocalDraft();
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

        // Local Storage Draft Logic
        const formEl = document.getElementById(`stepForm${currentStep}`);
        if (formEl) {
            $(formEl).on('input change', function() {
                isDirty = true;
                saveToLocalStorage();
            });
            checkForLocalDraft();
        }

        function saveToLocalStorage() {
            if (!formEl) return;
            const data = {};
            const formData = new FormData(formEl);
            for (const [key, value] of formData.entries()) {
                const input = formEl.querySelector(`[name="${key}"]`);
                if (input && input.type !== 'file' && input.type !== 'password') {
                    data[key] = value;
                }
            }
            localStorage.setItem(draftKey, JSON.stringify(data));
            localStorage.setItem(draftTimestampKey, Date.now().toString());
            updateDraftStatus('local');
        }

        function getLocalDraft() {
            const dataStr = localStorage.getItem(draftKey);
            const tsStr = localStorage.getItem(draftTimestampKey);
            if (dataStr && tsStr) {
                return {
                    data: JSON.parse(dataStr),
                    timestamp: parseInt(tsStr, 10)
                };
            }
            return null;
        }

        function clearLocalDraft() {
            localStorage.removeItem(draftKey);
            localStorage.removeItem(draftTimestampKey);
        }

        function checkForLocalDraft() {
            const draft = getLocalDraft();
            if (!draft) return;

            const ageMs = Date.now() - draft.timestamp;
            if (ageMs > 24 * 60 * 60 * 1000) {
                clearLocalDraft();
                return;
            }

            let hasNewerData = false;
            for (const [key, val] of Object.entries(draft.data)) {
                const currentInput = $(formEl).find(`[name="${key}"]`);
                if (currentInput.length > 0) {
                    const currentVal = currentInput.val();
                    if (val && !currentVal) {
                        hasNewerData = true;
                        break;
                    }
                }
            }

            if (hasNewerData) {
                swalFire({
                    icon: 'info',
                    title: 'Pulihkan Draft?',
                    text: 'Ditemukan draf pengisian sebelumnya yang belum tersimpan di server. Apakah Anda ingin memulihkannya?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pulihkan',
                    cancelButtonText: 'Mulai Baru',
                    confirmButtonColor: themePrimaryColor(),
                }).then((result) => {
                    if (result.isConfirmed) {
                        restoreLocalDraft(draft.data);
                    } else {
                        clearLocalDraft();
                    }
                });
            }
        }

        function restoreLocalDraft(data) {
            for (const [key, val] of Object.entries(data)) {
                const input = formEl.querySelector(`[name="${key}"]`);
                if (!input) continue;

                if (input.type === 'checkbox') {
                    input.checked = (val === '1' || val === 'on' || val === true);
                } else if (input.type === 'radio') {
                    const radio = formEl.querySelector(`[name="${key}"][value="${val}"]`);
                    if (radio) radio.checked = true;
                } else {
                    $(input).val(val).trigger('change');
                }
            }
            isDirty = true;
            swalFire({
                icon: 'success',
                title: 'Draft Dipulihkan',
                text: 'Data draft lokal berhasil dimasukkan ke form.',
                timer: 1500,
                showConfirmButton: false
            });
        }

        function updateDraftStatus(state) {
            const statusEl = document.getElementById('draftStatus');
            if (!statusEl) return;

            let html = '';
            if (state === 'saved') {
                html = '<i data-lucide="cloud" class="me-1 text-success" style="width:14px;height:14px;"></i> Draft tersimpan';
            } else if (state === 'saving') {
                html = '<span class="spinner-border spinner-border-sm me-1 text-primary" role="status" style="width:12px;height:12px;"></span> Menyimpan...';
            } else if (state === 'local') {
                html = '<i data-lucide="database" class="me-1 text-warning" style="width:14px;height:14px;"></i> Draft tersimpan lokal';
            } else if (state === 'offline' || state === 'error') {
                html = '<i data-lucide="cloud-off" class="me-1 text-danger" style="width:14px;height:14px;"></i> Gagal sinkronisasi';
            }

            statusEl.innerHTML = html;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Autosave to Server Interval (Every 30 seconds)
        let autosaveInterval = null;
        if (formEl) {
            autosaveInterval = setInterval(async function() {
                if (isDirty) {
                    updateDraftStatus('saving');
                    const success = await saveCurrentStepSilent();
                    if (success) {
                        isDirty = false;
                        clearLocalDraft();
                        updateDraftStatus('saved');
                    } else {
                        updateDraftStatus('offline');
                    }
                }
            }, 30000);
        }

        // Silent save without redirect or alerts
        async function saveCurrentStepSilent() {
            if (!formEl) return true;
            const formData = new FormData(formEl);
            try {
                const response = await fetch(`<?= base_url('/pendaftar/daftar/step/') ?>${currentStep}/save`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                return result.success === true;
            } catch (error) {
                console.error('Silent save failed:', error);
                return false;
            }
        }

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
                    await swalFire({
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
                await swalFire({
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
