<?= $this->extend('pendaftar/registration/wizard_layout') ?>

<?= $this->section('step_content') ?>
<div class="p-4">
    <h4 class="mb-4 text-primary"><i  data-lucide="user"></i> Langkah 1: Identitas Calon Siswa</h4>
    
    <form id="stepForm1" method="POST">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Nama Lengkap -->
            <div class="col-md-6 form-group">
                <label for="full_name" class="form-label required-field">Nama Lengkap</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= esc($stepData['full_name'] ?? '') ?>" placeholder="Nama sesuai akta lahir" required>
            </div>
            
            <!-- Jenis Kelamin -->
            <div class="col-md-6 form-group">
                <label class="form-label required-field">Jenis Kelamin</label>
                <div class="mt-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_l" value="L" <?= ($stepData['gender'] ?? '') === 'L' ? 'checked' : '' ?> required>
                        <label class="form-check-label" for="gender_l">Laki-laki</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_p" value="P" <?= ($stepData['gender'] ?? '') === 'P' ? 'checked' : '' ?> required>
                        <label class="form-check-label" for="gender_p">Perempuan</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Tempat Lahir -->
            <div class="col-md-6 form-group">
                <label for="birth_place" class="form-label required-field">Tempat Lahir</label>
                <input type="text" class="form-control" id="birth_place" name="birth_place" value="<?= esc($stepData['birth_place'] ?? '') ?>" placeholder="Kota/Kabupaten tempat lahir" required>
            </div>
            
            <!-- Tanggal Lahir -->
            <div class="col-md-6 form-group">
                <label for="birth_date" class="form-label required-field">Tanggal Lahir</label>
                <input type="text" class="form-control datepicker" id="birth_date" name="birth_date" value="<?= esc($stepData['birth_date'] ?? '') ?>" placeholder="Pilih tanggal lahir" required>
            </div>
        </div>

        <div class="row">
            <!-- Agama -->
            <div class="col-md-4 form-group">
                <label for="religion" class="form-label required-field">Agama</label>
                <select class="form-select" id="religion" name="religion" required>
                    <option value="">-- Pilih Agama --</option>
                    <?php foreach ($dapodikValues['agama'] as $agama): ?>
                        <option value="<?= esc($agama) ?>" <?= ($stepData['religion'] ?? '') === $agama ? 'selected' : '' ?>><?= esc($agama) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Kewarganegaraan -->
            <div class="col-md-4 form-group">
                <label for="citizenship" class="form-label required-field">Kewarganegaraan</label>
                <select class="form-select" id="citizenship" name="citizenship" required>
                    <option value="WNI" <?= ($stepData['citizenship'] ?? 'WNI') === 'WNI' ? 'selected' : '' ?>>Warga Negara Indonesia (WNI)</option>
                    <option value="WNA" <?= ($stepData['citizenship'] ?? '') === 'WNA' ? 'selected' : '' ?>>Warga Negara Asing (WNA)</option>
                </select>
            </div>

            <!-- Status Hubungan Keluarga -->
            <div class="col-md-4 form-group">
                <label for="family_status" class="form-label required-field">Status dalam Keluarga</label>
                <select class="form-select" id="family_status" name="family_status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Anak Kandung" <?= ($stepData['family_status'] ?? '') === 'Anak Kandung' ? 'selected' : '' ?>>Anak Kandung</option>
                    <option value="Anak Tiri" <?= ($stepData['family_status'] ?? '') === 'Anak Tiri' ? 'selected' : '' ?>>Anak Tiri</option>
                    <option value="Anak Angkat" <?= ($stepData['family_status'] ?? '') === 'Anak Angkat' ? 'selected' : '' ?>>Anak Angkat</option>
                    <option value="Lainnya" <?= ($stepData['family_status'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                </select>
            </div>
        </div>

        <div class="row">
            <!-- NIK -->
            <div class="col-md-6 form-group">
                <label for="nik" class="form-label required-field">NIK (Nomor Induk Kependudukan)</label>
                <input type="text" class="form-control" id="nik" name="nik" value="<?= esc($stepData['nik'] ?? '') ?>" placeholder="16 digit nomor di Kartu Keluarga" maxlength="16" required>
                <div class="help-text">Harus terdiri dari 16 digit angka.</div>
            </div>

            <!-- NISN -->
            <div class="col-md-6 form-group">
                <label for="nisn" class="form-label">NISN (Nomor Induk Siswa Nasional)</label>
                <input type="text" class="form-control" id="nisn" name="nisn" value="<?= esc($stepData['nisn'] ?? '') ?>" placeholder="10 digit NISN aktif (jika ada)" maxlength="10">
                <div class="help-text">Kosongkan jika belum memiliki NISN. Harus 10 digit.</div>
            </div>
        </div>

        <div class="row">
            <!-- Nomor Register Akta Lahir -->
            <div class="col-md-6 form-group">
                <label for="birth_cert_number" class="form-label">No. Registrasi Akta Lahir</label>
                <input type="text" class="form-control" id="birth_cert_number" name="birth_cert_number" value="<?= esc($stepData['birth_cert_number'] ?? '') ?>" placeholder="Contoh: 12345/LU/2010">
            </div>

            <!-- Kebutuhan Khusus -->
            <div class="col-md-6 form-group">
                <label for="special_needs" class="form-label required-field">Kebutuhan Khusus</label>
                <select class="form-select" id="special_needs" name="special_needs" required>
                    <?php foreach ($dapodikValues['kondisi_khusus'] as $specialNeed): ?>
                        <option value="<?= esc($specialNeed) ?>" <?= ($stepData['special_needs'] ?? 'Tidak Ada') === $specialNeed ? 'selected' : '' ?>><?= esc($specialNeed) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Initialize Flatpickr for birth date
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        locale: "id"
    });
</script>
<?= $this->endSection() ?>
