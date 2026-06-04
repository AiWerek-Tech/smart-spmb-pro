<?= $this->extend('pendaftar/registration/wizard_layout') ?>

<?= $this->section('step_content') ?>
<?php
$standardOccupations = $dapodikValues['pekerjaan'] ?? [];
$currentOccupation = $stepData['occupation'] ?? '';
$isOtherOccupation = !empty($currentOccupation) && !in_array($currentOccupation, array_diff($standardOccupations, ['Lainnya']), true);
$selectedOccupation = $isOtherOccupation ? 'Lainnya' : $currentOccupation;
$occupationOtherVal = $isOtherOccupation ? $currentOccupation : '';
?>
<div class="p-4">
    <div class="d-flex align-items-center mb-4">
        <h4 class="wizard-step-title mb-0"><i data-lucide="shield-check"></i> Langkah 5: Data Wali</h4>
        <span class="badge bg-secondary ms-3">Opsional</span>
    </div>
    
    <div class="alert alert-info mb-4">
        <i data-lucide="info"></i> Bagian ini <strong>hanya diisi</strong> jika calon siswa tinggal/dibiayai oleh Wali (selain orang tua kandung). Jika tidak ada, silakan langsung klik <strong>Lanjut</strong>.
    </div>

    <form id="stepForm5" method="POST">
        <?= csrf_field() ?>
        
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i data-lucide="info" class="me-2" style="width: 20px; height: 20px;"></i>
            <div>
                Data harus sesuai dengan <strong>Kartu Keluarga (KK)</strong> terbaru jika menggunakan Wali.
            </div>
        </div>
        
        <div class="row">
            <!-- Nama Wali -->
            <div class="col-md-6 form-group">
                <label for="full_name" class="form-label">Nama Lengkap Wali</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= esc($stepData['full_name'] ?? '') ?>" placeholder="Nama lengkap Wali">
            </div>
            
            <!-- NIK Wali -->
            <div class="col-md-6 form-group">
                <label for="nik" class="form-label">NIK Wali</label>
                <input type="text" class="form-control" id="nik" name="nik" value="<?= esc($stepData['nik'] ?? '') ?>" placeholder="16 digit NIK Wali" maxlength="16">
                <div class="help-text">Jika diisi, harus terdiri dari 16 digit angka.</div>
            </div>
        </div>

        <div class="row">
            <!-- Tempat Lahir -->
            <div class="col-md-6 form-group">
                <label for="birth_place" class="form-label">Tempat Lahir</label>
                <input type="text" class="form-control" id="birth_place" name="birth_place" value="<?= esc($stepData['birth_place'] ?? '') ?>" placeholder="Tempat lahir Wali">
            </div>
            
            <!-- Tanggal Lahir -->
            <div class="col-md-6 form-group">
                <label for="birth_date" class="form-label">Tanggal Lahir</label>
                <input type="text" class="form-control datepicker" id="birth_date" name="birth_date" value="<?= esc($stepData['birth_date'] ?? '') ?>" placeholder="Pilih tanggal lahir">
            </div>
        </div>

        <div class="row">
            <!-- Hubungan dengan Siswa -->
            <div class="col-md-3 form-group">
                <label for="relation" class="form-label">Hubungan Keluarga</label>
                <input type="text" class="form-control" id="relation" name="relation" value="<?= esc($stepData['relation'] ?? '') ?>" placeholder="Contoh: Paman, Tante, Kakek, Kakak">
            </div>

            <!-- Pendidikan Terakhir -->
            <div class="col-md-3 form-group">
                <label for="education" class="form-label">Pendidikan Terakhir</label>
                <select class="form-select" id="education" name="education">
                    <option value="">-- Pilih Pendidikan --</option>
                    <?php foreach ($dapodikValues['pendidikan'] as $edu): ?>
                        <option value="<?= esc($edu) ?>" <?= ($stepData['education'] ?? '') === $edu ? 'selected' : '' ?>><?= esc($edu) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Pekerjaan Utama -->
            <div class="col-md-3 form-group">
                <label for="occupation" class="form-label">Pekerjaan Utama</label>
                <select class="form-select" id="occupation" name="occupation">
                    <option value="">-- Pilih Pekerjaan --</option>
                    <?php foreach ($dapodikValues['pekerjaan'] as $job): ?>
                        <option value="<?= esc($job) ?>" <?= $selectedOccupation === $job ? 'selected' : '' ?>><?= esc($job) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Penghasilan Bulanan -->
            <div class="col-md-3 form-group">
                <label for="income" class="form-label">Penghasilan Bulanan</label>
                <select class="form-select" id="income" name="income">
                    <option value="">-- Pilih Penghasilan --</option>
                    <?php foreach ($dapodikValues['penghasilan'] as $inc): ?>
                        <option value="<?= esc($inc) ?>" <?= ($stepData['income'] ?? '') === $inc ? 'selected' : '' ?>><?= esc($inc) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Pekerjaan Lainnya (Manual Input) -->
        <div class="row" id="occupation_other_group" style="<?= $selectedOccupation === 'Lainnya' ? '' : 'display: none;' ?>">
            <div class="col-md-12 form-group">
                <label for="occupation_other" class="form-label">Sebutkan Pekerjaan Secara Detail</label>
                <input type="text" class="form-control" id="occupation_other" name="occupation_other" value="<?= esc($occupationOtherVal) ?>" placeholder="Sebutkan pekerjaan Wali secara manual">
            </div>
        </div>

        <div class="row">
            <!-- No. HP/WhatsApp -->
            <div class="col-md-6 form-group">
                <label for="phone_number" class="form-label">No. Telepon / WhatsApp</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= esc($stepData['phone_number'] ?? '') ?>" placeholder="Contoh: 081234567890">
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        locale: "id"
    });

    const occupationSelect = document.getElementById('occupation');
    const occupationOtherGroup = document.getElementById('occupation_other_group');
    const occupationOtherInput = document.getElementById('occupation_other');
    const fullNameInput = document.getElementById('full_name');

    function toggleOccupationOther() {
        if (occupationSelect.value === 'Lainnya') {
            occupationOtherGroup.style.display = 'flex';
            // Only require occupation_other if the Wali full name is filled in
            if (fullNameInput && fullNameInput.value.trim() !== '') {
                occupationOtherInput.setAttribute('required', 'required');
            } else {
                occupationOtherInput.removeAttribute('required');
            }
        } else {
            occupationOtherGroup.style.display = 'none';
            occupationOtherInput.removeAttribute('required');
        }
    }

    if (occupationSelect && occupationOtherGroup && occupationOtherInput) {
        occupationSelect.addEventListener('change', toggleOccupationOther);
        if (fullNameInput) {
            fullNameInput.addEventListener('input', toggleOccupationOther);
        }
        toggleOccupationOther();
    }
</script>
<?= $this->endSection() ?>
