<?= $this->extend('pendaftar/registration/wizard_layout') ?>

<?= $this->section('step_content') ?>
<div class="p-4">
    <h4 class="wizard-step-title"><i data-lucide="heart"></i> Langkah 6: Data Periodik Siswa</h4>
    
    <form id="stepForm6" method="POST">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Tinggi Badan -->
            <div class="col-md-6 form-group">
                <label for="height_cm" class="form-label required-field">Tinggi Badan</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="height_cm" name="height_cm" value="<?= esc($stepData['height_cm'] ?? '') ?>" placeholder="Tinggi badan" min="50" max="250" required>
                    <span class="input-group-text">cm</span>
                </div>
            </div>
            
            <!-- Berat Badan -->
            <div class="col-md-6 form-group">
                <label for="weight_kg" class="form-label required-field">Berat Badan</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="weight_kg" name="weight_kg" value="<?= esc($stepData['weight_kg'] ?? '') ?>" placeholder="Berat badan" min="10" max="200" required>
                    <span class="input-group-text">kg</span>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <!-- KIP (Kartu Indonesia Pintar) -->
        <div class="row align-items-center mb-3">
            <div class="col-md-4">
                <label class="form-label d-block fw-bold">Memiliki Kartu Indonesia Pintar (KIP)?</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input kip-toggle" type="radio" name="has_kip" id="kip_yes" value="1" <?= !empty($stepData['has_kip']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="kip_yes">Ya</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input kip-toggle" type="radio" name="has_kip" id="kip_no" value="0" <?= empty($stepData['has_kip']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="kip_no">Tidak</label>
                </div>
            </div>
            
            <div class="col-md-8 form-group" id="kip_number_container" style="display: <?= !empty($stepData['has_kip']) ? 'block' : 'none' ?>;">
                <label for="kip_number" class="form-label required-field">Nomor KIP</label>
                <input type="text" class="form-control" id="kip_number" name="kip_number" value="<?= esc($stepData['kip_number'] ?? '') ?>" placeholder="6 digit nomor KIP">
            </div>
        </div>

        <!-- KKS (Kartu Keluarga Sejahtera) -->
        <div class="row align-items-center mb-3">
            <div class="col-md-4">
                <label class="form-label d-block fw-bold">Memiliki Kartu Keluarga Sejahtera (KKS)?</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input kks-toggle" type="radio" name="has_kks" id="kks_yes" value="1" <?= !empty($stepData['has_kks']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="kks_yes">Ya</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input kks-toggle" type="radio" name="has_kks" id="kks_no" value="0" <?= empty($stepData['has_kks']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="kks_no">Tidak</label>
                </div>
            </div>
            
            <div class="col-md-8 form-group" id="kks_number_container" style="display: <?= !empty($stepData['has_kks']) ? 'block' : 'none' ?>;">
                <label for="kks_number" class="form-label required-field">Nomor KKS</label>
                <input type="text" class="form-control" id="kks_number" name="kks_number" value="<?= esc($stepData['kks_number'] ?? '') ?>" placeholder="6 digit nomor KKS">
            </div>
        </div>

        <!-- PKH (Program Keluarga Harapan) -->
        <div class="row align-items-center mb-3">
            <div class="col-md-4">
                <label class="form-label d-block fw-bold">Penerima Program Keluarga Harapan (PKH)?</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input pkh-toggle" type="radio" name="has_pkh" id="pkh_yes" value="1" <?= !empty($stepData['pkh_number']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pkh_yes">Ya</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input pkh-toggle" type="radio" name="has_pkh" id="pkh_no" value="0" <?= empty($stepData['pkh_number']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pkh_no">Tidak</label>
                </div>
            </div>
            
            <div class="col-md-8 form-group" id="pkh_number_container" style="display: <?= !empty($stepData['pkh_number']) ? 'block' : 'none' ?>;">
                <label for="pkh_number" class="form-label required-field">Nomor PKH</label>
                <input type="text" class="form-control" id="pkh_number" name="pkh_number" value="<?= esc($stepData['pkh_number'] ?? '') ?>" placeholder="Nomor PKH">
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <!-- Kondisi Khusus / Kesehatan -->
            <div class="col-md-12 form-group">
                <label for="special_condition" class="form-label">Kondisi Khusus / Riwayat Penyakit</label>
                <input type="text" class="form-control" id="special_condition" name="special_condition" value="<?= esc($stepData['special_condition'] ?? 'Tidak Ada Kondisi Khusus') ?>" placeholder="Contoh: Asma, alergi makanan tertentu, dll. Tulis 'Tidak Ada Kondisi Khusus' jika normal">
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // KIP Toggle
    document.querySelectorAll('.kip-toggle').forEach(el => {
        el.addEventListener('change', function() {
            const container = document.getElementById('kip_number_container');
            const input = document.getElementById('kip_number');
            if (this.value === '1') {
                container.style.display = 'block';
                input.setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                input.removeAttribute('required');
                input.value = '';
            }
        });
    });

    // KKS Toggle
    document.querySelectorAll('.kks-toggle').forEach(el => {
        el.addEventListener('change', function() {
            const container = document.getElementById('kks_number_container');
            const input = document.getElementById('kks_number');
            if (this.value === '1') {
                container.style.display = 'block';
                input.setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                input.removeAttribute('required');
                input.value = '';
            }
        });
    });

    // PKH Toggle
    document.querySelectorAll('.pkh-toggle').forEach(el => {
        el.addEventListener('change', function() {
            const container = document.getElementById('pkh_number_container');
            const input = document.getElementById('pkh_number');
            if (this.value === '1') {
                container.style.display = 'block';
                input.setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                input.removeAttribute('required');
                input.value = '';
            }
        });
    });
</script>
<?= $this->endSection() ?>
