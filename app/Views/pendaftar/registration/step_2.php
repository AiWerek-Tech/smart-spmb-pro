<?= $this->extend('pendaftar/registration/wizard_layout') ?>

<?= $this->section('step_content') ?>
<div class="p-4">
    <h4 class="mb-4 text-primary"><i  data-lucide="map"></i> Langkah 2: Alamat & Kontak</h4>
    
    <form id="stepForm2" method="POST">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Alamat Jalan -->
            <div class="col-md-8 form-group">
                <label for="street_address" class="form-label required-field">Alamat Jalan</label>
                <input type="text" class="form-control" id="street_address" name="street_address" value="<?= esc($stepData['street_address'] ?? '') ?>" placeholder="Nama jalan, perumahan, nomor rumah" required>
            </div>
            
            <!-- RT -->
            <div class="col-md-2 form-group">
                <label for="rt" class="form-label">RT</label>
                <input type="number" class="form-control" id="rt" name="rt" value="<?= esc($stepData['rt'] ?? '') ?>" placeholder="000" min="1" max="999">
            </div>

            <!-- RW -->
            <div class="col-md-2 form-group">
                <label for="rw" class="form-label">RW</label>
                <input type="number" class="form-control" id="rw" name="rw" value="<?= esc($stepData['rw'] ?? '') ?>" placeholder="000" min="1" max="999">
            </div>
        </div>

        <div class="row">
            <!-- Dusun/Kampung -->
            <div class="col-md-4 form-group">
                <label for="hamlet" class="form-label">Dusun/Dukuh/Kampung</label>
                <input type="text" class="form-control" id="hamlet" name="hamlet" value="<?= esc($stepData['hamlet'] ?? '') ?>" placeholder="Nama dusun/kampung">
            </div>

            <!-- Kelurahan/Desa -->
            <div class="col-md-4 form-group">
                <label for="village" class="form-label required-field">Kelurahan/Desa</label>
                <input type="text" class="form-control" id="village" name="village" value="<?= esc($stepData['village'] ?? '') ?>" placeholder="Kelurahan/Desa" required>
            </div>

            <!-- Kecamatan -->
            <div class="col-md-4 form-group">
                <label for="subdistrict" class="form-label required-field">Kecamatan</label>
                <input type="text" class="form-control" id="subdistrict" name="subdistrict" value="<?= esc($stepData['subdistrict'] ?? '') ?>" placeholder="Kecamatan" required>
            </div>
        </div>

        <div class="row">
            <!-- Kabupaten/Kota -->
            <div class="col-md-4 form-group">
                <label for="district" class="form-label required-field">Kabupaten/Kota</label>
                <input type="text" class="form-control" id="district" name="district" value="<?= esc($stepData['district'] ?? '') ?>" placeholder="Kabupaten/Kota" required>
            </div>

            <!-- Provinsi -->
            <div class="col-md-4 form-group">
                <label for="province" class="form-label required-field">Provinsi</label>
                <input type="text" class="form-control" id="province" name="province" value="<?= esc($stepData['province'] ?? '') ?>" placeholder="Provinsi" required>
            </div>

            <!-- Kode Pos -->
            <div class="col-md-4 form-group">
                <label for="postal_code" class="form-label required-field">Kode Pos</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?= esc($stepData['postal_code'] ?? '') ?>" placeholder="5 digit kode pos" maxlength="5" required>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <!-- Tempat Tinggal -->
            <div class="col-md-4 form-group">
                <label for="residence_type" class="form-label required-field">Tempat Tinggal</label>
                <select class="form-select" id="residence_type" name="residence_type" required>
                    <option value="">-- Pilih Jenis Tinggal --</option>
                    <?php foreach ($dapodikValues['jenis_tinggal'] as $jenis): ?>
                        <option value="<?= esc($jenis) ?>" <?= ($stepData['residence_type'] ?? '') === $jenis ? 'selected' : '' ?>><?= esc($jenis) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Jarak Rumah ke Sekolah -->
            <div class="col-md-4 form-group">
                <label for="distance_km" class="form-label required-field">Jarak ke Sekolah (KM)</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="distance_km" name="distance_km" value="<?= esc($stepData['distance_km'] ?? '0') ?>" placeholder="Contoh: 1.5" step="0.1" min="0" required>
                    <span class="input-group-text">KM</span>
                </div>
            </div>

            <!-- Transportasi ke Sekolah -->
            <div class="col-md-4 form-group">
                <label for="transport_mode" class="form-label required-field">Moda Transportasi</label>
                <select class="form-select" id="transport_mode" name="transport_mode" required>
                    <option value="">-- Pilih Moda Transportasi --</option>
                    <?php foreach ($dapodikValues['moda_transportasi'] as $moda): ?>
                        <option value="<?= esc($moda) ?>" <?= ($stepData['transport_mode'] ?? '') === $moda ? 'selected' : '' ?>><?= esc($moda) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <!-- No. Telepon/WhatsApp -->
            <div class="col-md-6 form-group">
                <label for="phone_number" class="form-label required-field">No. Telepon / WhatsApp</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= esc($stepData['phone_number'] ?? '') ?>" placeholder="Contoh: 081234567890" required>
                <div class="help-text">Nomor aktif untuk informasi pendaftaran.</div>
            </div>

            <!-- Email Aktif -->
            <div class="col-md-6 form-group">
                <label for="email" class="form-label required-field">Alamat Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= esc($stepData['email'] ?? '') ?>" placeholder="Contoh: nama@domain.com" required>
                <div class="help-text">Gunakan alamat email aktif.</div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
