<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-seleksi-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Data SPMB</p>
            <h1 id="admin-seleksi-title">Hasil Seleksi & Kelulusan</h1>
            <p class="admin-page-subtitle">Tentukan status kelulusan peserta SPMB menjadi <strong>Diterima</strong> atau <strong>Ditolak</strong>.</p>
        </div>
        <div class="admin-page-actions">
            <form action="<?= base_url('admin/seleksi/hitung-ranking') ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghitung ulang ranking seluruh pendaftar?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-warning shadow-sm">
                    <i class="me-2" data-lucide="calculator"></i> Kalkulasi Ranking
                </button>
            </form>
        </div>
    </header>

    <!-- Filters Card -->
    <section class="admin-filter-panel" aria-label="Filter hasil seleksi">
        <form method="GET" action="<?= base_url('admin/seleksi') ?>" class="row g-3">
            <div class="col-md-3">
                <label for="jalur" class="form-label small fw-bold">Jalur Pendaftaran</label>
                <select name="jalur" id="jalur" class="form-select select2">
                    <option value="">Semua Jalur</option>
                    <?php foreach ($jalur as $j): ?>
                        <option value="<?= $j['id'] ?>" <?= $jalurId == $j['id'] ? 'selected' : '' ?>><?= esc($j['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label small fw-bold">Status Verifikasi</label>
                <select name="status" id="status" class="form-select select2">
                    <option value="">Semua Status</option>
                    <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Baru (Submitted)</option>
                    <option value="verified" <?= $status === 'verified' ? 'selected' : '' ?>>Berkas Valid (Verified)</option>
                    <option value="accepted" <?= $status === 'accepted' ? 'selected' : '' ?>>Diterima (Accepted)</option>
                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label small fw-bold">Cari Nama / No Pendaftaran</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari nama, NIK, NISN..." value="<?= esc($search) ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="me-2" data-lucide="search"></i> Saring
                </button>
                <a href="<?= base_url('admin/seleksi') ?>" class="btn btn-outline-secondary w-100">
                    <i class="me-2" data-lucide="rotate-ccw"></i> Reset
                </a>
            </div>
        </form>
    </section>

    <!-- Applicants List Card -->
    <section class="admin-secondary-panel" aria-labelledby="seleksi-table-title">
        <div class="admin-secondary-panel__header">
            <div>
                <h2 class="admin-section-title" id="seleksi-table-title">Daftar Keputusan Seleksi</h2>
                <p class="admin-section-subtitle">Urutkan, filter, lalu tetapkan status kelulusan peserta.</p>
            </div>
        </div>
        <div class="card-body p-0">
                <div class="table-responsive admin-table-shell">
                    <table id="seleksiTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Pendaftaran</th>
                                <th>Nama Lengkap / NIK</th>
                                <th>Jalur Pendaftaran</th>
                                <th>Skor Jarak</th>
                                <th>Skor Prestasi</th>
                                <th>Total Skor</th>
                                <th>Kesiapan Dapodik</th>
                                <th>Status Hasil</th>
                                <th class="text-center pe-4" style="width: 250px;">Aksi Seleksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registrants)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fs-1 mb-3" data-lucide="user-x"></i>
                                        <p class="mb-0">Tidak ada pendaftar yang memenuhi kriteria pencarian.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registrants as $r): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-bold text-primary"><?= esc($r['registration_number']) ?></span>
                                            <div class="small text-muted">TA <?= esc($r['academic_year']) ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($r['full_name']) ?></div>
                                            <div class="small text-muted">NIK: <?= esc($r['nik']) ?> | NISN: <?= esc($r['nisn'] ?: '-') ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= esc($r['jalur_name']) ?></span>
                                        </td>
                                        <td>
                                            <?= number_format((float)($r['score_distance'] ?? 0), 2) ?>
                                        </td>
                                        <td>
                                            <?= number_format((float)($r['score_achievement'] ?? 0), 0) ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary"><?= number_format((float)($r['score_total'] ?? 0), 2) ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                                $ready = (int)$r['is_dapodik_ready'];
                                                $percentage = (float)$r['dapodik_percentage'];
                                            ?>
                                            <div class="d-flex align-items-center">
                                                <span class="badge <?= $ready ? 'bg-label-success' : 'bg-label-warning' ?> rounded-pill px-2 me-2">
                                                    <?= $ready ? 'Siap Dapodik' : 'Belum Lengkap' ?>
                                                </span>
                                                <small class="fw-bold text-dark"><?= number_format($percentage, 0) ?>%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($r['status'] === 'accepted'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="check-circle-2"></i> LULUS / DITERIMA
                                                </span>
                                            <?php elseif ($r['status'] === 'rejected'): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="x-circle"></i> TIDAK LULUS
                                                </span>
                                            <?php elseif ($r['status'] === 'verified'): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="check-double"></i> BERKAS VALID
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="spin"></i> PROSES SELEKSI
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <!-- Terima button form -->
                                                <form action="<?= base_url('admin/seleksi/'.$r['id'].'/update') ?>" method="POST" class="me-1">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="btn btn-sm btn-success d-flex align-items-center" <?= $r['status'] === 'accepted' ? 'disabled' : '' ?> title="Terima Calon Peserta">
                                                        <i class="me-1" data-lucide="check"></i> Terima
                                                    </button>
                                                </form>
 
                                                <!-- Tolak button form -->
                                                <form action="<?= base_url('admin/seleksi/'.$r['id'].'/update') ?>" method="POST" class="me-1">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center" <?= $r['status'] === 'rejected' ? 'disabled' : '' ?> title="Tolak Calon Peserta">
                                                        <i class="me-1" data-lucide="x"></i> Tolak
                                                    </button>
                                                </form>
 
                                                <!-- Revert to verified -->
                                                <form action="<?= base_url('admin/seleksi/'.$r['id'].'/update') ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="verified">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary btn-icon" <?= in_array($r['status'], ['submitted', 'verified'], true) ? 'disabled' : '' ?> title="Kembalikan Ke Status Berkas Valid" style="width: 32px; height: 32px;">
                                                        <i  data-lucide="rotate-ccw"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </div>
    </section>
</section>
<?= $this->endSection() ?>
 
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        if ($('#seleksiTable tbody tr').length > 1) {
            $('#seleksiTable').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json'
                },
                order: [[5, 'desc']], // Sort by Total Skor descending by default
                columnDefs: [
                    { orderable: false, targets: 8 } // Disable sorting on Action column
                ]
            });
        }
    });
</script>
<?= $this->endSection() ?>
