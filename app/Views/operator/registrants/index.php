<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('additional_css') ?>
<style>
    .registrant-mobile-list {
        display: none;
    }

    @media (max-width: 767.98px) {
        .registrants-table-card {
            display: none;
        }

        .registrant-mobile-list {
            display: grid;
            gap: 12px;
        }

        .registrant-mobile-card {
            border: 1px solid var(--sp-card-border);
            border-radius: var(--sp-radius-md);
            background: var(--sp-card-bg);
            box-shadow: var(--sp-shadow-xs);
            padding: 16px;
        }

        .registrant-mobile-meta {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            margin: 12px 0;
        }

        .registrant-mobile-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .registrant-mobile-actions .btn {
            min-height: 44px;
            padding-left: 8px;
            padding-right: 8px;
        }

        .registrant-filter-actions {
            gap: 8px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-shell role-page-shell">
    <!-- Header Page -->
    <div class="role-page-header">
        <div>
            <h1 class="role-page-header__title">Kelola Calon Peserta</h1>
            <p class="role-page-header__subtitle">Kelola berkas, edit profil, dan validasi kelengkapan berkas Dapodik calon murid baru.</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div>
        <div class="card shadow-sm border admin-filter-panel">
            <div class="card-body p-3">
                <form method="GET" action="<?= base_url('operator/registrants') ?>" class="row g-3">
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
                        <label for="status" class="form-label small fw-bold">Status Berkas</label>
                        <select name="status" id="status" class="form-select select2">
                            <option value="">Semua Status</option>
                            <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Baru (Submitted)</option>
                            <option value="verified" <?= $status === 'verified' ? 'selected' : '' ?>>Lolos Verifikasi (Verified)</option>
                            <option value="accepted" <?= $status === 'accepted' ? 'selected' : '' ?>>Diterima (Accepted)</option>
                            <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label small fw-bold">Cari Nama / No Pendaftaran</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Nama, NIK, NISN..." value="<?= esc($search) ?>">
                    </div>
                    <div class="col-md-3 role-filter-actions registrant-filter-actions">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="me-2" data-lucide="search"></i> Saring
                        </button>
                        <a href="<?= base_url('operator/registrants') ?>" class="btn btn-outline-secondary w-100">
                            <i class="me-2" data-lucide="rotate-ccw"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Registrants Table Card -->
    <div>
        <div class="registrant-mobile-list">
            <?php if (empty($registrants)): ?>
                <div class="sp-empty-state">
                    <div class="sp-empty-state-icon">
                        <i data-lucide="user-x"></i>
                    </div>
                    <p class="sp-empty-state-title">Tidak Ada Pendaftar</p>
                    <p class="sp-empty-state-text">Tidak ada calon peserta yang sesuai dengan filter saat ini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($registrants as $r): ?>
                    <?php
                        $percentage = max(0, min((float) $r['dapodik_percentage'], 100));
                        $barClass = 'bg-danger';
                        if ($percentage >= 100) {
                            $barClass = 'bg-success';
                        } elseif ($percentage >= 70) {
                            $barClass = 'bg-warning';
                        }

                        $statusLabel = 'Proses';
                        $statusClass = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                        if ($r['status'] === 'accepted') {
                            $statusLabel = 'Diterima';
                            $statusClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                        } elseif ($r['status'] === 'rejected') {
                            $statusLabel = 'Ditolak';
                            $statusClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                        } elseif ($r['status'] === 'verified') {
                            $statusLabel = 'Berkas Valid';
                            $statusClass = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                        }
                    ?>
                    <article class="registrant-mobile-card" aria-label="Pendaftar <?= esc($r['full_name']) ?>">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-bold text-primary small"><?= esc($r['registration_number']) ?></div>
                                <h5 class="mb-1 mt-1"><?= esc($r['full_name']) ?></h5>
                                <div class="text-muted small">NIK <?= esc($r['nik']) ?> | NISN <?= esc($r['nisn'] ?: '-') ?></div>
                            </div>
                            <span class="badge <?= $statusClass ?> rounded-pill px-3 py-1"><?= esc($statusLabel) ?></span>
                        </div>

                        <div class="registrant-mobile-meta">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Jalur</span>
                                <span class="badge bg-label-secondary"><?= esc($r['jalur_name']) ?></span>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Kesiapan Dapodik</span>
                                    <span class="fw-bold small"><?= number_format($percentage, 0) ?>%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar <?= $barClass ?>" role="progressbar" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percentage ?>%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="registrant-mobile-actions">
                            <a href="<?= base_url('operator/registrants/'.$r['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i data-lucide="eye" class="me-1"></i> Detail
                            </a>
                            <a href="<?= base_url('operator/documents/'.$r['id']) ?>" class="btn btn-sm btn-outline-success">
                                <i data-lucide="folder-open" class="me-1"></i> Dokumen
                            </a>
                            <a href="<?= base_url('operator/registrants/'.$r['id'].'/edit') ?>" class="btn btn-sm btn-outline-warning">
                                <i data-lucide="edit" class="me-1"></i> Koreksi
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="card shadow-sm border registrants-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="registrantsTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No. Pendaftaran</th>
                                <th>Nama Calon Siswa</th>
                                <th>Jalur Pendaftaran</th>
                                <th>Persentase Dapodik</th>
                                <th>Status Hasil</th>
                                <th class="text-center pe-4" style="width: 250px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registrants)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fs-1 mb-3" data-lucide="user-x"></i>
                                        <p class="mb-0">Tidak ada pendaftar yang ditemukan.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registrants as $r): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?= esc($r['registration_number']) ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark mb-1"><?= esc($r['full_name']) ?></div>
                                            <div class="small text-muted">NIK: <?= esc($r['nik']) ?> | NISN: <?= esc($r['nisn'] ?: '-') ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= esc($r['jalur_name']) ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                                $percentage = max(0, min((float)$r['dapodik_percentage'], 100));
                                                $barClass = 'bg-danger';
                                                if ($percentage >= 100) {
                                                    $barClass = 'bg-success';
                                                } elseif ($percentage >= 70) {
                                                    $barClass = 'bg-warning';
                                                }
                                            ?>
                                            <div class="d-flex align-items-center" style="max-width: 150px;">
                                                <div class="progress w-100 me-2" style="height: 6px;">
                                                    <div class="progress-bar <?= $barClass ?>" role="progressbar" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $percentage ?>%;"></div>
                                                </div>
                                                <small class="fw-bold text-dark"><?= number_format($percentage, 0) ?>%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($r['status'] === 'accepted'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Diterima</span>
                                            <?php elseif ($r['status'] === 'rejected'): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">Ditolak</span>
                                            <?php elseif ($r['status'] === 'verified'): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1">Berkas Valid</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">Proses</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="role-table-actions">
                                                <!-- Detail button -->
                                                <a href="<?= base_url('operator/registrants/'.$r['id']) ?>" class="btn btn-sm btn-outline-primary me-1 px-2" title="Detail Profil Siswa">
                                                    <i class="me-1" data-lucide="eye"></i> Detail
                                                </a>

                                                <!-- Verification button -->
                                                <a href="<?= base_url('operator/documents/'.$r['id']) ?>" class="btn btn-sm btn-outline-success me-1 px-2" title="Verifikasi Dokumen">
                                                    <i class="me-1" data-lucide="folder-open"></i> Dokumen
                                                </a>

                                                <!-- Correct profile errors button -->
                                                <a href="<?= base_url('operator/registrants/'.$r['id'].'/edit') ?>" class="btn btn-sm btn-outline-warning px-2" title="Koreksi Profil Siswa">
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
        if (window.matchMedia('(min-width: 768px)').matches && $('#registrantsTable tbody tr').length > 1) {
            $('#registrantsTable').DataTable({
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
