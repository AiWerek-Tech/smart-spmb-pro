<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in justify-content-center">
    <div class="col-md-10">
        <!-- Back button -->
        <div class="mb-3">
            <a href="<?= base_url('operator/registrants/'.$registration['id']) ?>" class="text-decoration-none">
                <i class="me-1" data-lucide="arrow-left"></i> Batal, Kembali ke Profil Detail
            </a>
        </div>

        <form method="POST" action="<?= base_url('operator/registrants/'.$registration['id'].'/update') ?>" autocomplete="off">
            <?= csrf_field() ?>

            <!-- Validation errors -->
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- CARD 1: IDENTITAS SISWA -->
            <div class="card shadow-sm border mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title text-primary"><i class="me-2" data-lucide="user"></i> Koreksi Identitas Siswa</h5>
                    <small class="text-muted">Perbaiki nama, gender, tempat tanggal lahir, NIK, dan NISN.</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-8">
                            <label for="full_name" class="form-label fw-bold small">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" id="full_name" value="<?= old('full_name', $registration['full_name']) ?>" required>
                        </div>
                        <!-- Gender -->
                        <div class="col-md-4">
                            <label for="gender" class="form-label fw-bold small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-select select2" required>
                                <option value="L" <?= old('gender', $registration['gender']) === 'L' ? 'selected' : '' ?>>Laki-laki (L)</option>
                                <option value="P" <?= old('gender', $registration['gender']) === 'P' ? 'selected' : '' ?>>Perempuan (P)</option>
                            </select>
                        </div>

                        <!-- Birth Place & Date -->
                        <div class="col-md-6">
                            <label for="birth_place" class="form-label fw-bold small">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="birth_place" id="birth_place" value="<?= old('birth_place', $registration['birth_place']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label fw-bold small">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr" name="birth_date" id="birth_date" value="<?= old('birth_date', $registration['birth_date']) ?>" required>
                        </div>

                        <!-- Religion & Citizenship -->
                        <div class="col-md-6">
                            <label for="religion" class="form-label fw-bold small">Agama <span class="text-danger">*</span></label>
                            <select name="religion" id="religion" class="form-select select2" required>
                                <option value="Islam" <?= old('religion', $registration['religion']) === 'Islam' ? 'selected' : '' ?>>Islam</option>
                                <option value="Kristen" <?= old('religion', $registration['religion']) === 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                                <option value="Katolik" <?= old('religion', $registration['religion']) === 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                                <option value="Hindu" <?= old('religion', $registration['religion']) === 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                                <option value="Buddha" <?= old('religion', $registration['religion']) === 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                                <option value="Konghucu" <?= old('religion', $registration['religion']) === 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="citizenship" class="form-label fw-bold small">Kewarganegaraan</label>
                            <select name="citizenship" id="citizenship" class="form-select select2">
                                <option value="WNI" <?= old('citizenship', $registration['citizenship']) === 'WNI' ? 'selected' : '' ?>>WNI (Warga Negara Indonesia)</option>
                                <option value="WNA" <?= old('citizenship', $registration['citizenship']) === 'WNA' ? 'selected' : '' ?>>WNA (Warga Negara Asing)</option>
                            </select>
                        </div>

                        <!-- NIK & NISN -->
                        <div class="col-md-6">
                            <label for="nik" class="form-label fw-bold small">Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nik" id="nik" value="<?= old('nik', $registration['nik']) ?>" max_length="16" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nisn" class="form-label fw-bold small">NISN</label>
                            <input type="text" class="form-control" name="nisn" id="nisn" value="<?= old('nisn', $registration['nisn']) ?>" max_length="10">
                        </div>

                        <!-- Special Needs & Family Status -->
                        <div class="col-md-6">
                            <label for="special_needs" class="form-label fw-bold small">Kebutuhan Khusus</label>
                            <input type="text" class="form-control" name="special_needs" id="special_needs" value="<?= old('special_needs', $registration['special_needs']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="family_status" class="form-label fw-bold small">Status Hubungan Keluarga</label>
                            <input type="text" class="form-control" name="family_status" id="family_status" value="<?= old('family_status', $registration['family_status']) ?>" placeholder="Contoh: Anak Kandung">
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: ALAMAT & KONTAK -->
            <div class="card shadow-sm border mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title text-primary"><i class="me-2" data-lucide="map-pin"></i> Koreksi Alamat & Kontak</h5>
                    <small class="text-muted">Perbaiki data lokasi tinggal dan no hp/email.</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Street Address -->
                        <div class="col-md-12">
                            <label for="street_address" class="form-label fw-bold small">Alamat Jalan</label>
                            <input type="text" class="form-control" name="street_address" id="street_address" value="<?= old('street_address', $address['street_address'] ?? '') ?>">
                        </div>

                        <!-- RT / RW -->
                        <div class="col-md-6">
                            <label for="rt" class="form-label fw-bold small">RT</label>
                            <input type="text" class="form-control" name="rt" id="rt" value="<?= old('rt', $address['rt'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="rw" class="form-label fw-bold small">RW</label>
                            <input type="text" class="form-control" name="rw" id="rw" value="<?= old('rw', $address['rw'] ?? '') ?>">
                        </div>

                        <!-- Village & Subdistrict -->
                        <div class="col-md-6">
                            <label for="village" class="form-label fw-bold small">Desa / Kelurahan</label>
                            <input type="text" class="form-control" name="village" id="village" value="<?= old('village', $address['village'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="subdistrict" class="form-label fw-bold small">Kecamatan</label>
                            <input type="text" class="form-control" name="subdistrict" id="subdistrict" value="<?= old('subdistrict', $address['subdistrict'] ?? '') ?>">
                        </div>

                        <!-- District & Province -->
                        <div class="col-md-6">
                            <label for="district" class="form-label fw-bold small">Kabupaten / Kota</label>
                            <input type="text" class="form-control" name="district" id="district" value="<?= old('district', $address['district'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="province" class="form-label fw-bold small">Provinsi</label>
                            <input type="text" class="form-control" name="province" id="province" value="<?= old('province', $address['province'] ?? '') ?>">
                        </div>

                        <!-- Phone & Email -->
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label fw-bold small">No HP / WA Siswa</label>
                            <input type="text" class="form-control" name="phone_number" id="phone_number" value="<?= old('phone_number', $contact['phone_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold small">Email Siswa</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?= old('email', $contact['email'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: ORANG TUA / WALI -->
            <div class="card shadow-sm border mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title text-primary"><i class="me-2" data-lucide="users"></i> Koreksi Orang Tua</h5>
                    <small class="text-muted">Perbaiki data nama dan pekerjaan ayah & ibu.</small>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Ayah -->
                        <div class="col-md-6 border-end">
                            <h6 class="text-dark fw-bold mb-3"><i class="text-primary me-1" data-lucide="user-check"></i> Data Ayah Kandung</h6>
                            <div class="mb-3">
                                <label for="father_name" class="form-label fw-bold small">Nama Ayah</label>
                                <input type="text" class="form-control" name="father_name" id="father_name" value="<?= old('father_name', $father['full_name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="father_nik" class="form-label fw-bold small">NIK Ayah</label>
                                <input type="text" class="form-control" name="father_nik" id="father_nik" value="<?= old('father_nik', $father['nik'] ?? '') ?>" max_length="16">
                            </div>
                            <div class="mb-3">
                                <label for="father_occupation" class="form-label fw-bold small">Pekerjaan Ayah</label>
                                <input type="text" class="form-control" name="father_occupation" id="father_occupation" value="<?= old('father_occupation', $father['occupation'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Ibu -->
                        <div class="col-md-6">
                            <h6 class="text-dark fw-bold mb-3"><i class="text-primary me-1" data-lucide="user"></i> Data Ibu Kandung</h6>
                            <div class="mb-3">
                                <label for="mother_name" class="form-label fw-bold small">Nama Ibu</label>
                                <input type="text" class="form-control" name="mother_name" id="mother_name" value="<?= old('mother_name', $mother['full_name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="mother_nik" class="form-label fw-bold small">NIK Ibu</label>
                                <input type="text" class="form-control" name="mother_nik" id="mother_nik" value="<?= old('mother_nik', $mother['nik'] ?? '') ?>" max_length="16">
                            </div>
                            <div class="mb-3">
                                <label for="mother_occupation" class="form-label fw-bold small">Pekerjaan Ibu</label>
                                <input type="text" class="form-control" name="mother_occupation" id="mother_occupation" value="<?= old('mother_occupation', $mother['occupation'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 4: DATA PERIODIK -->
            <div class="card shadow-sm border mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title text-primary"><i class="me-2" data-lucide="clipboard-list"></i> Koreksi Data Fisik</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="height_cm" class="form-label fw-bold small">Tinggi Badan (cm)</label>
                            <input type="number" class="form-control" name="height_cm" id="height_cm" value="<?= old('height_cm', $periodic['height_cm'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="weight_kg" class="form-label fw-bold small">Berat Badan (kg)</label>
                            <input type="number" class="form-control" name="weight_kg" id="weight_kg" value="<?= old('weight_kg', $periodic['weight_kg'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="kip_number" class="form-label fw-bold small">Nomor KIP</label>
                            <input type="text" class="form-control" name="kip_number" id="kip_number" value="<?= old('kip_number', $periodic['kip_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="kks_number" class="form-label fw-bold small">Nomor KKS</label>
                            <input type="text" class="form-control" name="kks_number" id="kks_number" value="<?= old('kks_number', $periodic['kks_number'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTIONS -->
            <div class="card shadow-sm border mb-5">
                <div class="card-body bg-light border-top d-flex justify-content-end py-3">
                    <a href="<?= base_url('operator/registrants/'.$registration['id']) ?>" class="btn btn-outline-secondary me-2">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="me-1" data-lucide="save"></i> Simpan Hasil Koreksi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
