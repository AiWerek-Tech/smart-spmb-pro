<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in">
    <!-- Header Page -->
    <div class="col-12 mb-4">
        <div>
            <h4 class="mb-0 text-primary">Validasi & Kesiapan Dapodik</h4>
            <p class="text-muted mb-0">Cek status kelengkapan 11 data F-PD wajib untuk sinkronisasi sistem Dapodik sekolah.</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border">
            <div class="card-body p-3">
                <form method="GET" action="<?= base_url('operator/dapodik') ?>" class="row g-3">
                    <div class="col-md-3">
                        <label for="jalur" class="form-label small fw-bold">Jalur Pendaftaran</label>
                        <select name="jalur" id="jalur" class="form-select select2">
                            <option value="">Semua Jalur</option>
                            <option value="1" <?= $jalurId == '1' ? 'selected' : '' ?>>Zonasi</option>
                            <option value="2" <?= $jalurId == '2' ? 'selected' : '' ?>>Afirmasi</option>
                            <option value="3" <?= $jalurId == '3' ? 'selected' : '' ?>>Prestasi</option>
                            <option value="4" <?= $jalurId == '4' ? 'selected' : '' ?>>Perpindahan Tugas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label small fw-bold">Status Kesiapan</label>
                        <select name="status" id="status" class="form-select select2">
                            <option value="">Semua Status</option>
                            <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Siap Dapodik (100%)</option>
                            <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Belum Lengkap (<100%)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label small fw-bold">Cari Nama / No Pendaftaran</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari nama, NIK..." value="<?= esc($search) ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2">
                            <i class="me-2" data-lucide="search"></i> Saring
                        </button>
                        <a href="<?= base_url('operator/dapodik') ?>" class="btn btn-outline-secondary w-100">
                            <i class="me-2" data-lucide="rotate-ccw"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dapodik List Card -->
    <div class="col-12">
        <div class="card shadow-sm border">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="dapodikTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Pendaftaran</th>
                                <th>Nama Calon Siswa</th>
                                <th>NIK / NISN</th>
                                <th>Kelengkapan</th>
                                <th>Status Dapodik</th>
                                <th class="text-center pe-4" style="width: 250px;">Aksi Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registrants)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fs-1 mb-3" data-lucide="user-slash"></i>
                                        <p class="mb-0">Tidak ada pendaftar yang ditemukan.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registrants as $r): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?= esc($r['registration_number']) ?></td>
                                        <td><span class="fw-semibold text-dark"><?= esc($r['full_name']) ?></span></td>
                                        <td>
                                            <div class="small text-dark fw-semibold">NIK: <?= esc($r['nik']) ?></div>
                                            <div class="small text-muted">NISN: <?= esc($r['nisn'] ?: '-') ?></div>
                                        </td>
                                        <td>
                                            <?php 
                                                $percentage = (float)$r['dapodik_percentage'];
                                                $barClass = 'bg-danger';
                                                if ($percentage >= 100) {
                                                    $barClass = 'bg-success';
                                                } elseif ($percentage >= 70) {
                                                    $barClass = 'bg-warning';
                                                }
                                            ?>
                                            <div class="d-flex align-items-center" style="max-width: 150px;">
                                                <div class="progress w-100 me-2" style="height: 6px;">
                                                    <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $percentage ?>%;"></div>
                                                </div>
                                                <small class="fw-bold text-dark"><?= number_format($percentage, 0) ?>%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($r['is_dapodik_ready']): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="check-circle-2"></i> SIAP DAPODIK
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="alert-circle"></i> BELUM LENGKAP
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <!-- Review validity report button -->
                                                <a href="<?= base_url('operator/dapodik/'.$r['id']) ?>" class="btn btn-sm btn-outline-primary me-1 px-3" title="Periksa Rincian Data">
                                                    <i class="me-1" data-lucide="clipboard-check"></i> Periksa Rincian
                                                </a>

                                                <!-- Correct profile errors button -->
                                                <a href="<?= base_url('operator/registrants/'.$r['id'].'/edit') ?>" class="btn btn-sm btn-outline-warning px-3" title="Koreksi Profil">
                                                    <i class="me-1" data-lucide="edit"></i> Koreksi
                                                </a>
                                            </div>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        if ($('#dapodikTable tbody tr').length > 1) {
            $('#dapodikTable').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json'
                },
                columnDefs: [
                    { orderable: false, targets: 5 } // Disable sorting on Action column
                ]
            });
        }
    });
</script>
<?= $this->endSection() ?>
