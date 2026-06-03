<?= $this->extend('pendaftar/registration/wizard_layout') ?>

<?= $this->section('step_content') ?>
<div class="p-4">
    <h4 class="wizard-step-title"><i data-lucide="user-check"></i> Langkah 3: Data Ayah Kandung</h4>
    
    <form id="stepForm3" method="POST">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Nama Ayah -->
            <div class="col-md-6 form-group">
                <label for="full_name" class="form-label required-field">Nama Lengkap Ayah</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= esc($stepData['full_name'] ?? '') ?>" placeholder="Nama lengkap sesuai KK" required>
            </div>
            
            <!-- NIK Ayah -->
            <div class="col-md-6 form-group">
                <label for="nik" class="form-label required-field">NIK Ayah</label>
                <input type="text" class="form-control" id="nik" name="nik" value="<?= esc($stepData['nik'] ?? '') ?>" placeholder="16 digit NIK Ayah" maxlength="16" required>
                <div class="help-text">Harus terdiri dari 16 digit angka.</div>
            </div>
        </div>

        <div class="row">
            <!-- Tempat Lahir -->
            <div class="col-md-6 form-group">
                <label for="birth_place" class="form-label required-field">Tempat Lahir</label>
                <input type="text" class="form-control" id="birth_place" name="birth_place" value="<?= esc($stepData['birth_place'] ?? '') ?>" placeholder="Tempat lahir Ayah" required>
            </div>
            
            <!-- Tanggal Lahir -->
            <div class="col-md-6 form-group">
                <label for="birth_date" class="form-label required-field">Tanggal Lahir</label>
                <input type="text" class="form-control datepicker" id="birth_date" name="birth_date" value="<?= esc($stepData['birth_date'] ?? '') ?>" placeholder="Pilih tanggal lahir" required>
            </div>
        </div>

        <div class="row">
            <!-- Pendidikan Terakhir -->
            <div class="col-md-4 form-group">
                <label for="education" class="form-label required-field">Pendidikan Terakhir</label>
                <select class="form-select" id="education" name="education" required>
                    <option value="">-- Pilih Pendidikan --</option>
                    <?php foreach ($dapodikValues['pendidikan'] as $edu): ?>
                        <option value="<?= esc($edu) ?>" <?= ($stepData['education'] ?? '') === $edu ? 'selected' : '' ?>><?= esc($edu) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Pekerjaan Utama -->
            <div class="col-md-4 form-group">
                <label for="occupation" class="form-label required-field">Pekerjaan Utama</label>
                <select class="form-select" id="occupation" name="occupation" required>
                    <option value="">-- Pilih Pekerjaan --</option>
                    <?php foreach ($dapodikValues['pekerjaan'] as $job): ?>
                        <option value="<?= esc($job) ?>" <?= ($stepData['occupation'] ?? '') === $job ? 'selected' : '' ?>><?= esc($job) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Penghasilan Bulanan -->
            <div class="col-md-4 form-group">
                <label for="income" class="form-label required-field">Penghasilan Bulanan</label>
                <select class="form-select" id="income" name="income" required>
                    <option value="">-- Pilih Penghasilan --</option>
                    <?php foreach ($dapodikValues['penghasilan'] as $inc): ?>
                        <option value="<?= esc($inc) ?>" <?= ($stepData['income'] ?? '') === $inc ? 'selected' : '' ?>><?= esc($inc) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <!-- No. HP/WhatsApp -->
            <div class="col-md-6 form-group">
                <label for="phone_number" class="form-label required-field">No. Telepon / WhatsApp</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= esc($stepData['phone_number'] ?? '') ?>" placeholder="Contoh: 081234567890" required>
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
</script>
<?= $this->endSection() ?>
