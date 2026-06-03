<?= $this->extend('pendaftar/registration/wizard_layout') ?>

<?= $this->section('step_content') ?>
<div class="p-4">
    <div class="d-flex align-items-center mb-4">
        <h4 class="wizard-step-title mb-0"><i data-lucide="trophy"></i> Langkah 7: Data Prestasi Siswa</h4>
        <span class="badge bg-secondary ms-3">Opsional</span>
    </div>
    
    <div class="alert alert-info mb-4">
        <i  data-lucide="info"></i> Tambahkan riwayat prestasi akademik atau non-akademik terbaik yang pernah diperoleh. Jika tidak ada prestasi, Anda bisa langsung mengklik tombol <strong>Lanjut</strong>.
    </div>

    <form id="stepForm7" method="POST">
        <?= csrf_field() ?>
        
        <div id="achievements_container">
            <?php 
            $achievements = $stepData ?? [];
            if (empty($achievements)): 
            ?>
                <!-- Default empty state with 1 empty achievement row -->
                <div class="card mb-3 border achievement-row" data-index="0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.8rem;"><i  data-lucide="medal"></i> Data Prestasi #1</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-achievement-btn" style="display: none;">
                            <i  data-lucide="trash-2"></i> Hapus
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <!-- Nama Prestasi -->
                            <div class="col-md-4 form-group">
                                <label class="form-label">Nama Prestasi / Lomba</label>
                                <input type="text" class="form-control" name="achievements[0][name]" placeholder="Contoh: Juara O2SN Bulutangkis">
                            </div>

                            <!-- Jenis Prestasi -->
                            <div class="col-md-2 form-group">
                                <label class="form-label">Jenis</label>
                                <select class="form-select" name="achievements[0][type]">
                                    <option value="">-- Pilih --</option>
                                    <option value="Sains">Sains/Akademik</option>
                                    <option value="Olahraga">Olahraga</option>
                                    <option value="Seni">Seni/Kesenian</option>
                                    <option value="Keagamaan">Keagamaan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <!-- Tingkat -->
                            <div class="col-md-2 form-group">
                                <label class="form-label">Tingkat</label>
                                <select class="form-select" name="achievements[0][level]">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($dapodikValues['tingkat_prestasi'] as $level): ?>
                                        <option value="<?= esc($level) ?>"><?= esc($level) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Juara / Peringkat -->
                            <div class="col-md-2 form-group">
                                <label class="form-label">Peringkat</label>
                                <select class="form-select" name="achievements[0][rank]">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($dapodikValues['peringkat_prestasi'] as $rank): ?>
                                        <option value="<?= esc($rank) ?>"><?= esc($rank) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tahun -->
                            <div class="col-md-2 form-group">
                                <label class="form-label">Tahun</label>
                                <input type="number" class="form-control" name="achievements[0][year]" placeholder="YYYY" min="2000" max="2026">
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Render existing achievements from draft data -->
                <?php foreach ($achievements as $index => $achievement): ?>
                    <div class="card mb-3 border achievement-row" data-index="<?= $index ?>">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.8rem;"><i  data-lucide="medal"></i> Data Prestasi #<?= $index + 1 ?></span>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-achievement-btn" style="display: <?= $index > 0 ? 'block' : 'none' ?>;">
                                <i  data-lucide="trash-2"></i> Hapus
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="form-label">Nama Prestasi / Lomba</label>
                                    <input type="text" class="form-control" name="achievements[<?= $index ?>][name]" value="<?= esc($achievement['name'] ?? '') ?>" placeholder="Contoh: Juara O2SN Bulutangkis">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label class="form-label">Jenis</label>
                                    <select class="form-select" name="achievements[<?= $index ?>][type]">
                                        <option value="">-- Pilih --</option>
                                        <option value="Sains" <?= ($achievement['type'] ?? '') === 'Sains' ? 'selected' : '' ?>>Sains/Akademik</option>
                                        <option value="Olahraga" <?= ($achievement['type'] ?? '') === 'Olahraga' ? 'selected' : '' ?>>Olahraga</option>
                                        <option value="Seni" <?= ($achievement['type'] ?? '') === 'Seni' ? 'selected' : '' ?>>Seni/Kesenian</option>
                                        <option value="Keagamaan" <?= ($achievement['type'] ?? '') === 'Keagamaan' ? 'selected' : '' ?>>Keagamaan</option>
                                        <option value="Lainnya" <?= ($achievement['type'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>

                                <div class="col-md-2 form-group">
                                    <label class="form-label">Tingkat</label>
                                    <select class="form-select" name="achievements[<?= $index ?>][level]">
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($dapodikValues['tingkat_prestasi'] as $level): ?>
                                            <option value="<?= esc($level) ?>" <?= ($achievement['level'] ?? '') === $level ? 'selected' : '' ?>><?= esc($level) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-2 form-group">
                                    <label class="form-label">Peringkat</label>
                                    <select class="form-select" name="achievements[<?= $index ?>][rank]">
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($dapodikValues['peringkat_prestasi'] as $rank): ?>
                                            <option value="<?= esc($rank) ?>" <?= ($achievement['rank'] ?? '') === $rank ? 'selected' : '' ?>><?= esc($rank) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-2 form-group">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" class="form-control" name="achievements[<?= $index ?>][year]" value="<?= esc($achievement['year'] ?? '') ?>" placeholder="YYYY" min="2000" max="2026">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" class="btn btn-outline-primary" id="add_achievement_btn">
            <i  data-lucide="plus"></i> Tambah Riwayat Prestasi
        </button>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let achievementIndex = document.querySelectorAll('.achievement-row').length;

    // Tambah Baris Prestasi baru
    document.getElementById('add_achievement_btn').addEventListener('click', function() {
        const container = document.getElementById('achievements_container');
        const nextIndex = achievementIndex++;

        const newRowHtml = `
            <div class="card mb-3 border achievement-row" data-index="${nextIndex}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                    <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.8rem;"><i  data-lucide="medal"></i> Data Prestasi #${nextIndex + 1}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-achievement-btn">
                        <i  data-lucide="trash-2"></i> Hapus
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label">Nama Prestasi / Lomba</label>
                            <input type="text" class="form-control" name="achievements[${nextIndex}][name]" placeholder="Contoh: Juara O2SN Bulutangkis">
                        </div>

                        <div class="col-md-2 form-group">
                            <label class="form-label">Jenis</label>
                            <select class="form-select" name="achievements[${nextIndex}][type]">
                                <option value="">-- Pilih --</option>
                                <option value="Sains">Sains/Akademik</option>
                                <option value="Olahraga">Olahraga</option>
                                <option value="Seni">Seni/Kesenian</option>
                                <option value="Keagamaan">Keagamaan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label class="form-label">Tingkat</label>
                            <select class="form-select" name="achievements[${nextIndex}][level]">
                                <option value="">-- Pilih --</option>
                                <?php foreach ($dapodikValues['tingkat_prestasi'] as $level): ?>
                                    <option value="<?= esc($level) ?>"><?= esc($level) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label class="form-label">Peringkat</label>
                            <select class="form-select" name="achievements[${nextIndex}][rank]">
                                <option value="">-- Pilih --</option>
                                <?php foreach ($dapodikValues['peringkat_prestasi'] as $rank): ?>
                                    <option value="<?= esc($rank) ?>"><?= esc($rank) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label class="form-label">Tahun</label>
                            <input type="number" class="form-control" name="achievements[${nextIndex}][year]" placeholder="YYYY" min="2000" max="2026">
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newRowHtml);
        attachRemoveHandler();
    });

    // Handler Hapus baris prestasi
    function attachRemoveHandler() {
        document.querySelectorAll('.remove-achievement-btn').forEach(btn => {
            btn.onclick = function() {
                const row = this.closest('.achievement-row');
                row.remove();
                
                // Recalculate title indexes
                document.querySelectorAll('.achievement-row').forEach((r, idx) => {
                    r.setAttribute('data-index', idx);
                    r.querySelector('.card-header span').innerHTML = `<i  data-lucide="medal"></i> Data Prestasi #${idx + 1}`;
                });
                achievementIndex = document.querySelectorAll('.achievement-row').length;
            };
        });
    }

    // Attach handler to initial row remove buttons
    attachRemoveHandler();
</script>
<?= $this->endSection() ?>
