<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('additional_css') ?>
<style>
    .users-mobile-list {
        display: none;
    }

    @media (max-width: 767.98px) {
        .users-table-card {
            display: none;
        }

        .users-mobile-list {
            display: grid;
            gap: 12px;
        }

        .user-mobile-card {
            border: 1px solid var(--sp-card-border);
            border-radius: var(--sp-radius-md);
            background: var(--sp-card-bg);
            box-shadow: var(--sp-shadow-xs);
            padding: 16px;
        }

        .user-mobile-meta {
            display: grid;
            gap: 8px;
            margin: 12px 0;
        }

        .user-mobile-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .user-mobile-actions .btn {
            min-height: 44px;
            padding-left: 8px;
            padding-right: 8px;
        }

        .user-filter-actions {
            gap: 8px;
        }

        .user-filter-actions .btn {
            margin-right: 0 !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in">
    <!-- Header Title -->
    <div class="col-12 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-0 text-primary fw-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;">Kelola Pengguna</h4>
            <p class="text-muted mb-0">Kelola hak akses dan akun untuk Administrator, Operator, dan Pendaftar.</p>
        </div>
        <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary d-flex align-items-center">
            <i data-lucide="user-plus" class="me-2" style="width: 16px; height: 16px;"></i> Tambah Pengguna
        </a>
    </div>

    <!-- Filters Card -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm border">
            <div class="card-body p-3">
                <form method="GET" action="<?= base_url('admin/users') ?>" class="row g-3">
                    <div class="col-md-4">
                        <label for="role" class="form-label small fw-semibold text-muted">Filter Peran</label>
                        <select name="role" id="role" class="form-select select2">
                            <option value="">Semua Peran</option>
                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="operator" <?= $role === 'operator' ? 'selected' : '' ?>>Operator</option>
                            <option value="pendaftar" <?= $role === 'pendaftar' ? 'selected' : '' ?>>Pendaftar</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label small fw-semibold text-muted">Filter Status</label>
                        <select name="status" id="status" class="form-select select2">
                            <option value="">Semua Status</option>
                            <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end user-filter-actions">
                        <button type="submit" class="btn btn-outline-primary w-100 me-2 d-flex align-items-center justify-content-center">
                            <i data-lucide="filter" class="me-2" style="width: 16px; height: 16px;"></i> Saring
                        </button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center">
                            <i data-lucide="rotate-ccw" class="me-2" style="width: 16px; height: 16px;"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Users List Card -->
    <div class="col-12 d-md-none">
        <div class="users-mobile-list">
            <?php if (empty($users)): ?>
                <article class="user-mobile-card text-center text-muted py-5">
                    <i data-lucide="user-x" class="d-block mx-auto mb-3" style="width: 40px; height: 40px; color: var(--sp-text-muted);"></i>
                    <p class="mb-0">Tidak ada data pengguna yang ditemukan.</p>
                </article>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                    <article class="user-mobile-card" aria-label="Pengguna <?= esc($u['name']) ?>">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex align-items-center min-w-0">
                                <div class="avatar me-2 bg-light rounded-circle p-1 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                    <i data-lucide="user" class="text-primary" style="width: 18px; height: 18px;"></i>
                                </div>
                                <div class="min-w-0">
                                    <h6 class="mb-1 text-truncate"><?= esc($u['name']) ?></h6>
                                    <p class="mb-0 small text-muted text-break"><?= esc($u['email']) ?></p>
                                </div>
                            </div>
                            <?php if ($u['is_active']): ?>
                                <span class="badge bg-label-success flex-shrink-0">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-label-danger flex-shrink-0">Nonaktif</span>
                            <?php endif; ?>
                        </div>

                        <div class="user-mobile-meta small">
                            <div class="d-flex justify-content-between gap-3">
                                <span class="text-muted">Peran</span>
                                <span class="fw-semibold text-capitalize"><?= esc($u['role']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between gap-3">
                                <span class="text-muted">Terdaftar</span>
                                <span class="fw-semibold text-end"><?= date('d M Y, H:i', strtotime($u['created_at'])) ?> WIB</span>
                            </div>
                        </div>

                        <div class="user-mobile-actions" aria-label="Aksi pengguna">
                            <form action="<?= base_url('admin/users/'.$u['id'].'/toggle') ?>" method="POST" class="m-0">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm w-100 <?= $u['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                                    <i data-lucide="<?= $u['is_active'] ? 'user-x' : 'user-check' ?>" class="me-1" style="width: 16px; height: 16px;"></i>
                                    <?= $u['is_active'] ? 'Nonaktif' : 'Aktifkan' ?>
                                </button>
                            </form>
                            <a href="<?= base_url('admin/users/'.$u['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary w-100">
                                <i data-lucide="edit-2" class="me-1" style="width: 16px; height: 16px;"></i>Edit
                            </a>
                            <form action="<?= base_url('admin/users/'.$u['id'].'/delete') ?>" method="POST" class="m-0">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-confirm w-100">
                                    <i data-lucide="trash-2" class="me-1" style="width: 16px; height: 16px;"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border users-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 50px;">No</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Peran</th>
                                <th>Status</th>
                                <th>Terdaftar Pada</th>
                                <th class="text-center pe-4" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i data-lucide="user-x" class="d-block mx-auto mb-3" style="width: 40px; height: 40px; color: var(--sp-text-muted);"></i>
                                        <p class="mb-0">Tidak ada data pengguna yang ditemukan.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($users as $u): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2 bg-light rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i data-lucide="user" class="text-primary" style="width: 16px; height: 16px;"></i>
                                                </div>
                                                <span class="fw-semibold text-dark"><?= esc($u['name']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= esc($u['email']) ?></td>
                                        <td>
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="badge bg-label-danger"><i data-lucide="shield" class="me-1 d-inline-block align-middle" style="width: 12px; height: 12px;"></i><span class="align-middle">Admin</span></span>
                                            <?php elseif ($u['role'] === 'operator'): ?>
                                                <span class="badge bg-label-info"><i data-lucide="settings" class="me-1 d-inline-block align-middle" style="width: 12px; height: 12px;"></i><span class="align-middle">Operator</span></span>
                                            <?php else: ?>
                                                <span class="badge bg-label-success"><i data-lucide="graduation-cap" class="me-1 d-inline-block align-middle" style="width: 12px; height: 12px;"></i><span class="align-middle">Pendaftar</span></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($u['is_active']): ?>
                                                <span class="badge bg-label-success px-3 py-1">
                                                    <i data-lucide="check-circle" class="me-1 d-inline-block align-middle" style="width: 12px; height: 12px;"></i> <span class="align-middle">Aktif</span>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-label-danger px-3 py-1">
                                                    <i data-lucide="x-circle" class="me-1 d-inline-block align-middle" style="width: 12px; height: 12px;"></i> <span class="align-middle">Nonaktif</span>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y, H:i', strtotime($u['created_at'])) ?> WIB</td>
                                        <td class="pe-4">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <!-- Toggle Active Form -->
                                                <form action="<?= base_url('admin/users/'.$u['id'].'/toggle') ?>" method="POST" class="m-0">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-icon <?= $u['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?> p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="<?= $u['is_active'] ? 'Nonaktifkan Akun' : 'Aktifkan Akun' ?>">
                                                        <i data-lucide="<?= $u['is_active'] ? 'user-x' : 'user-check' ?>" style="width: 16px; height: 16px;"></i>
                                                    </button>
                                                </form>
 
                                                <!-- Edit button -->
                                                <a href="<?= base_url('admin/users/'.$u['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Data">
                                                    <i data-lucide="edit-2" style="width: 16px; height: 16px;"></i>
                                                </a>
 
                                                <!-- Delete button -->
                                                <form action="<?= base_url('admin/users/'.$u['id'].'/delete') ?>" method="POST" class="m-0">
                                                    <?= csrf_field() ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger p-1 delete-confirm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus Pengguna">
                                                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
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
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        if (window.matchMedia('(min-width: 768px)').matches && $('#usersTable tbody tr').length > 1) {
            $('#usersTable').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json'
                },
                columnDefs: [
                    { orderable: false, targets: 6 } // Disable sorting on Action column
                ],
                drawCallback: function() {
                    // Re-initialize Lucide icons after DataTables rendering
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
