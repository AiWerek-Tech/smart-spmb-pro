<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="admin-page-shell role-page-shell">
    <!-- Back button & Top bar -->
    <div class="role-page-header">
        <a href="<?= base_url('operator/registrants') ?>" class="role-back-link">
            <i class="me-1" data-lucide="arrow-left"></i> Kembali ke Daftar Pendaftar
        </a>
        <div class="role-page-actions">
            <!-- Verify documents link -->
            <a href="<?= base_url('operator/documents/'.$registration['id']) ?>" class="btn btn-sm btn-success me-2">
                <i class="me-1" data-lucide="folder-open"></i> Verifikasi Dokumen
            </a>
            <!-- Export FPD link -->
            <a href="<?= base_url('operator/export/fpd/'.$registration['id']) ?>" class="btn btn-sm btn-outline-danger">
                <i class="me-1" data-lucide="file-text"></i> Cetak Formulir F-PD
            </a>
        </div>
    </div>

    <!-- Candidate Top Card Info -->
    <div>
        <div class="role-summary-card">
                <div class="row align-items-center">
                    <div class="col-auto text-center mb-3 mb-md-0">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($registration['full_name']) ?>&background=7c3aed&color=fff&size=80" class="rounded border p-1" alt="Avatar">
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center flex-wrap">
                            <h1 class="role-page-header__title me-3"><?= esc($registration['full_name']) ?></h1>
                            <span class="badge bg-light text-primary border me-2">Jalur <?= esc($registration['jalur_name']) ?></span>
                            
                            <?php if ($registration['status'] === 'accepted'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Lulus</span>
                            <?php elseif ($registration['status'] === 'rejected'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">Tidak Lulus</span>
                            <?php elseif ($registration['status'] === 'verified'): ?>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1">Berkas Valid</span>
                            <?php else: ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">Proses</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted mb-0 mt-1">No. Pendaftaran: <strong class="text-primary"><?= esc($registration['registration_number']) ?></strong> | NIK: <?= esc($registration['nik']) ?> | NISN: <?= esc($registration['nisn'] ?: '-') ?></p>
                    </div>
                    <div class="col-md-auto text-md-end mt-3 mt-md-0">
                        <div class="bg-light p-2 rounded border text-center">
                            <span class="d-block small text-muted">Kesiapan Dapodik</span>
                            <span class="fw-bold text-dark d-block mt-1">
                                <?php if ($registration['is_dapodik_ready']): ?>
                                    <i class="me-1" data-lucide="check-circle-2"></i>
                                <?php else: ?>
                                    <i class="me-1" data-lucide="alert-circle"></i>
                                <?php endif; ?>
                                <?= number_format($registration['dapodik_percentage'], 0) ?>%
                            </span>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <!-- Details Tab Content -->
    <div>
        <div class="card shadow-sm border">
            <div class="card-header p-0 bg-light border-bottom">
                <ul class="nav nav-tabs border-0" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active py-3 px-4 border-0 rounded-0" id="identitas-tab" data-bs-toggle="tab" data-bs-target="#identitas" type="button" role="tab"><i class="me-1" data-lucide="user"></i> Identitas Siswa</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 px-4 border-0 rounded-0" id="alamat-tab" data-bs-toggle="tab" data-bs-target="#alamat" type="button" role="tab"><i class="me-1" data-lucide="map-pin"></i> Alamat & Kontak</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 px-4 border-0 rounded-0" id="keluarga-tab" data-bs-toggle="tab" data-bs-target="#keluarga" type="button" role="tab"><i class="me-1" data-lucide="users"></i> Orang Tua / Wali</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 px-4 border-0 rounded-0" id="periodic-tab" data-bs-toggle="tab" data-bs-target="#periodic" type="button" role="tab"><i class="me-1" data-lucide="clipboard-list"></i> Data Periodik & Prestasi</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 px-4 border-0 rounded-0" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab"><i class="me-1" data-lucide="folder"></i> Berkas Upload</button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content" id="profileTabsContent">
                    <!-- TAB 1: IDENTITAS SISWA -->
                    <div class="tab-pane fade show active" id="identitas" role="tabpanel">
                        <h5 class="role-subsection-title"><i class="me-1" data-lucide="user"></i> Identitas Calon Siswa (Sesuai NIK/Akte)</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless align-middle text-dark">
                                    <tr>
                                        <td class="fw-semibold text-muted" style="width: 200px;">Nama Lengkap</td>
                                        <td>: <strong><?= esc($registration['full_name']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Jenis Kelamin</td>
                                        <td>: <?= $registration['gender'] === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Tempat / Tanggal Lahir</td>
                                        <td>: <?= esc($registration['birth_place'] ?? '-') ?>, <?= ! empty($registration['birth_date']) ? date('d F Y', strtotime($registration['birth_date'])) : '-' ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Agama</td>
                                        <td>: <?= esc($registration['religion'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Kewarganegaraan</td>
                                        <td>: <?= esc($registration['citizenship'] ?? '-') ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless align-middle text-dark">
                                    <tr>
                                        <td class="fw-semibold text-muted" style="width: 200px;">Nomor Induk Kependudukan (NIK)</td>
                                        <td>: <strong><?= esc($registration['nik']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">NISN</td>
                                        <td>: <?php if (empty($registration['nisn'])): ?><span class="text-muted">Tidak Ada / Belum Mengisi</span><?php else: ?><?= esc($registration['nisn']) ?><?php endif; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">No. Register Akta Lahir</td>
                                        <td>: <?php if (empty($registration['birth_cert_number'])): ?><span class="text-muted">Tidak Ada</span><?php else: ?><?= esc($registration['birth_cert_number']) ?><?php endif; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Kebutuhan Khusus</td>
                                        <td>: <span class="badge bg-light text-danger border border-danger border-opacity-10"><?= esc($registration['special_needs']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Status Hubungan Keluarga</td>
                                        <td>: <?= esc($registration['family_status']) ?: '-' ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: ALAMAT & KONTAK -->
                    <div class="tab-pane fade" id="alamat" role="tabpanel">
                        <h5 class="role-subsection-title"><i class="me-1" data-lucide="map-pin"></i> Alamat Domisili & Kontak Peserta</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless text-dark">
                                    <tr>
                                        <td class="fw-semibold text-muted" style="width: 200px;">Alamat Jalan</td>
                                        <td>: <?= esc($address['street_address'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">RT / RW</td>
                                        <td>: RT <?= esc($address['rt'] ?? '-') ?> / RW <?= esc($address['rw'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Dusun / Desa / Lurah</td>
                                        <td>: Dusun: <?= esc($address['hamlet'] ?? '-') ?> / Desa: <?= esc($address['village'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Kecamatan / Kabupaten</td>
                                        <td>: Kec. <?= esc($address['subdistrict'] ?? '-') ?> / Kab. <?= esc($address['district'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Provinsi / Kode Pos</td>
                                        <td>: Prov. <?= esc($address['province'] ?? '-') ?> / <?= esc($address['postal_code'] ?? '-') ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless text-dark">
                                    <tr>
                                        <td class="fw-semibold text-muted" style="width: 200px;">Jenis Tempat Tinggal</td>
                                        <td>: <?= esc($address['residence_type'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Moda Transportasi</td>
                                        <td>: <?= esc($address['transport_mode'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Jarak Rumah ke Sekolah</td>
                                        <td>: <?= esc($address['distance_km'] ?? '0') ?> km</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">No. Handphone / WA</td>
                                        <td>: <strong><?= esc($contact['phone_number'] ?? '-') ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Email Siswa</td>
                                        <td>: <?= esc($contact['email'] ?? '-') ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: DATA ORANG TUA / WALI -->
                    <div class="tab-pane fade" id="keluarga" role="tabpanel">
                        <div class="row g-4">
                            <!-- Data Ayah -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="role-subsection-title"><i class="me-1" data-lucide="user-check"></i> Data Ayah Kandung</h6>
                                    <table class="table table-sm table-borderless text-dark mb-0">
                                        <tr>
                                            <td class="fw-semibold text-muted" style="width: 150px;">Nama Lengkap</td>
                                            <td>: <?= esc($father['full_name'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">NIK Ayah</td>
                                            <td>: <?= esc($father['nik'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Pendidikan Terakhir</td>
                                            <td>: <?= esc($father['education'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Pekerjaan Utama</td>
                                            <td>: <?= esc($father['occupation'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Penghasilan Bulanan</td>
                                            <td>: <?= esc($father['income'] ?? '-') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Data Ibu -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="role-subsection-title"><i class="me-1" data-lucide="user-astronaut"></i> Data Ibu Kandung</h6>
                                    <table class="table table-sm table-borderless text-dark mb-0">
                                        <tr>
                                            <td class="fw-semibold text-muted" style="width: 150px;">Nama Lengkap</td>
                                            <td>: <?= esc($mother['full_name'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">NIK Ibu</td>
                                            <td>: <?= esc($mother['nik'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Pendidikan Terakhir</td>
                                            <td>: <?= esc($mother['education'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Pekerjaan Utama</td>
                                            <td>: <?= esc($mother['occupation'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Penghasilan Bulanan</td>
                                            <td>: <?= esc($mother['income'] ?? '-') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Data Wali -->
                            <div class="col-md-12">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="role-subsection-title"><i class="me-1" data-lucide="users"></i> Data Wali (Opsional)</h6>
                                    <?php if (empty($guardian['full_name'])): ?>
                                        <p class="text-muted small mb-0">Calon siswa tidak mendaftarkan data Wali (tinggal bersama orang tua kandung).</p>
                                    <?php else: ?>
                                        <table class="table table-sm table-borderless text-dark mb-0">
                                            <tr>
                                                <td class="fw-semibold text-muted" style="width: 150px;">Nama Wali</td>
                                                <td>: <?= esc($guardian['full_name']) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Hubungan Hubungan</td>
                                                <td>: <?= esc($guardian['relation'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">NIK Wali</td>
                                                <td>: <?= esc($guardian['nik'] ?? '-') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-muted">Pendidikan / Pekerjaan</td>
                                                <td>: <?= esc($guardian['education'] ?? '-') ?> / <?= esc($guardian['occupation'] ?? '-') ?></td>
                                            </tr>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: DATA PERIODIK & PRESTASI -->
                    <div class="tab-pane fade" id="periodic" role="tabpanel">
                        <div class="row">
                            <div class="col-md-5 mb-4">
                                <h6 class="role-subsection-title"><i class="me-1" data-lucide="heart"></i> Kondisi Fisik & KIP/KKS</h6>
                                <table class="table table-sm table-borderless text-dark">
                                    <tr>
                                        <td class="fw-semibold text-muted" style="width: 180px;">Tinggi / Berat Badan</td>
                                        <td>: <?= esc($periodic['height_cm'] ?? '-') ?> cm / <?= esc($periodic['weight_kg'] ?? '-') ?> kg</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Nomor KIP (Kartu Pintar)</td>
                                        <td>: <?= esc($periodic['kip_number'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Nomor KKS (Keluarga Sejahtera)</td>
                                        <td>: <?= esc($periodic['kks_number'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted">Nomor PKH (Harapan)</td>
                                        <td>: <?= esc($periodic['pkh_number'] ?? '-') ?></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-7 mb-4">
                                <h6 class="role-subsection-title"><i class="me-1" data-lucide="award"></i> Prestasi Calon Siswa</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped border">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Prestasi</th>
                                                <th>Kategori</th>
                                                <th>Tingkat</th>
                                                <th>Juara</th>
                                                <th>Tahun</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($achievements)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted small py-3">Tidak mendaftarkan data prestasi akademik maupun non-akademik.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($achievements as $ach): ?>
                                                    <tr>
                                                        <td><?= esc($ach['competition_name'] ?? $ach['name'] ?? '-') ?></td>
                                                        <td><?= esc($ach['achievement_type'] ?? $ach['type'] ?? '-') ?></td>
                                                        <td><?= esc($ach['level']) ?></td>
                                                        <td class="fw-semibold"><?= esc($ach['rank']) ?></td>
                                                        <td><?= esc($ach['year']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: BERKAS UPLOAD -->
                    <div class="tab-pane fade" id="docs" role="tabpanel">
                        <h5 class="role-subsection-title"><i class="me-1" data-lucide="folder"></i> Dokumen Berkas yang Diunggah</h5>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle border">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Tipe Dokumen</th>
                                        <th>File Nama</th>
                                        <th>Ukuran</th>
                                        <th>Status Verifikasi</th>
                                        <th class="text-center" style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($documents)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Belum ada dokumen yang diunggah.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($documents as $doc): ?>
                                            <tr>
                                                <td class="fw-semibold">
                                                    <?php 
                                                        $labels = [
                                                            'kk' => 'Kartu Keluarga (KK)',
                                                            'akta' => 'Akte Kelahiran',
                                                            'foto' => 'Pas Foto 3x4',
                                                            'raport' => 'Raport Terakhir',
                                                            'sertifikat' => 'Sertifikat Pendukung',
                                                            'kip_kks' => 'KIP / KKS',
                                                        ];
                                                        echo $labels[$doc['document_type']] ?? esc($doc['document_type']);
                                                    ?>
                                                </td>
                                                <td><span class="small font-monospace"><?= esc($doc['file_name']) ?></span></td>
                                                <td class="small text-muted"><?= number_format($doc['file_size'] / 1024, 1) ?> KB</td>
                                                <td>
                                                    <?php if ($doc['status'] === 'approved'): ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">Disetujui</span>
                                                    <?php elseif ($doc['status'] === 'rejected'): ?>
                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2">Ditolak</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= base_url('operator/documents/'.$doc['id'].'/view') ?>" class="btn btn-xs btn-outline-primary py-0 px-2" target="_blank">
                                                        <i data-lucide="external-link"></i> Buka File
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#profileTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
        if (typeof lucide !== 'undefined') {
            try { lucide.createIcons(); } catch (e) { /* ignore */ }
        }
    });
});
</script>
<?= $this->endSection() ?>
