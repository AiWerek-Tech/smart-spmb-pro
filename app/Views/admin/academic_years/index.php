<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('additional_css') ?>
<link href="<?= base_url('assets/css/admin-academic-years.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$years = $years ?? [];
$yearSummary = $yearSummary ?? ['total' => count($years), 'ready' => 0, 'archived' => 0];
$activeRow = $activeRow ?? null;

$formatDate = static function (?string $date): string {
    if (empty($date)) {
        return '-';
    }

    return date('d M Y', strtotime($date));
};

$periodText = static function (array $year) use ($formatDate): string {
    return $formatDate($year['starts_at'] ?? null) . ' sampai ' . $formatDate($year['ends_at'] ?? null);
};

$statusMeta = static function (array $year): array {
    if ((int) ($year['is_active'] ?? 0) === 1) {
        return ['label' => 'Aktif', 'tone' => 'success', 'icon' => 'check-circle-2'];
    }

    if ((int) ($year['is_archived'] ?? 0) === 1) {
        return ['label' => 'Arsip', 'tone' => 'muted', 'icon' => 'archive'];
    }

    return ['label' => 'Siap', 'tone' => 'primary', 'icon' => 'circle'];
};
?>

<section class="academic-years-page admin-page-shell" aria-labelledby="academic-years-title">
    <header class="academic-years-hero admin-page-header">
        <div>
            <p class="academic-years-kicker">Sumber Kebenaran SPMB</p>
            <h1 id="academic-years-title">Tahun Pelajaran</h1>
            <p class="admin-page-subtitle">Tentukan konteks aktif untuk pendaftaran, dashboard, dokumen, publikasi, dan folder upload.</p>
        </div>
        <div class="academic-years-hero__actions">
            <span class="academic-years-active-pill">
                <i data-lucide="calendar-check" aria-hidden="true"></i>
                <?= esc($activeYear) ?> aktif
            </span>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAcademicYear" data-academic-year-create-trigger>
                <i data-lucide="plus" class="me-2" aria-hidden="true"></i>Tambah Tahun
            </button>
        </div>
    </header>

    <div class="academic-years-summary" aria-label="Ringkasan tahun pelajaran">
        <article class="academic-years-summary-card" data-academic-year-summary-card>
            <span><i data-lucide="calendar-range" aria-hidden="true"></i></span>
            <div>
                <strong><?= number_format((int) ($yearSummary['total'] ?? 0), 0, ',', '.') ?></strong>
                <p>Total Tahun</p>
            </div>
        </article>
        <article class="academic-years-summary-card" data-academic-year-summary-card>
            <span><i data-lucide="check-circle-2" aria-hidden="true"></i></span>
            <div>
                <strong><?= esc($activeYear) ?></strong>
                <p>Tahun Aktif</p>
            </div>
        </article>
        <article class="academic-years-summary-card" data-academic-year-summary-card>
            <span><i data-lucide="archive" aria-hidden="true"></i></span>
            <div>
                <strong><?= number_format((int) ($yearSummary['archived'] ?? 0), 0, ',', '.') ?></strong>
                <p>Diarsipkan</p>
            </div>
        </article>
    </div>

    <div class="academic-years-grid">
        <section class="academic-years-panel admin-secondary-panel" aria-labelledby="academic-years-list-title">
            <div class="academic-years-panel__header">
                <div>
                    <p class="academic-years-kicker">Kelola periode</p>
                    <h2 id="academic-years-list-title">Daftar Tahun Pelajaran</h2>
                </div>
                <p>Aktivasi satu tahun akan otomatis menonaktifkan tahun lainnya.</p>
            </div>

            <?php if (empty($years)): ?>
                <?= view('components/empty_state', ['icon' => 'calendar-range', 'title' => 'Belum ada tahun pelajaran', 'text' => 'Tambahkan tahun pertama agar konteks SPMB bisa digunakan.']) ?>
            <?php else: ?>
                <div class="academic-years-table-wrap" data-academic-year-table>
                    <table class="academic-years-table">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($years as $year): ?>
                                <?php $status = $statusMeta($year); ?>
                                <tr
                                    data-academic-year-row
                                    data-id="<?= (int) $year['id'] ?>"
                                    data-year="<?= esc($year['year']) ?>"
                                    data-active="<?= (int) ($year['is_active'] ?? 0) ?>"
                                    data-archived="<?= (int) ($year['is_archived'] ?? 0) ?>"
                                >
                                    <td>
                                        <strong><?= esc($year['year']) ?></strong>
                                        <span><?= esc($year['label'] ?: 'Tahun Pelajaran ' . $year['year']) ?></span>
                                    </td>
                                    <td><?= esc($periodText($year)) ?></td>
                                    <td>
                                        <span class="academic-year-status academic-year-status--<?= esc($status['tone']) ?>">
                                            <i data-lucide="<?= esc($status['icon']) ?>" aria-hidden="true"></i><?= esc($status['label']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($year['notes'] ?: '-') ?></td>
                                    <td>
                                        <div class="academic-year-actions">
                                            <?php if ((int) ($year['is_active'] ?? 0) !== 1): ?>
                                                <form action="<?= base_url('admin/academic-years/' . (int) $year['id'] . '/activate') ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="academic-year-icon-btn" title="Aktifkan tahun">
                                                        <i data-lucide="check" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <button type="button" class="academic-year-icon-btn" data-bs-toggle="modal" data-bs-target="#editAcademicYear<?= (int) $year['id'] ?>" title="Edit tahun">
                                                <i data-lucide="pencil" aria-hidden="true"></i>
                                            </button>
                                            <?php if ((int) ($year['is_active'] ?? 0) !== 1): ?>
                                                <form action="<?= base_url('admin/academic-years/' . (int) $year['id'] . '/archive') ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="academic-year-icon-btn" title="Ubah status arsip">
                                                        <i data-lucide="archive" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                                <form action="<?= base_url('admin/academic-years/' . (int) $year['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Hapus tahun pelajaran ini? Data terkait tahun tersebut tidak ikut dihapus.');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="academic-year-icon-btn academic-year-icon-btn--danger" title="Hapus tahun">
                                                        <i data-lucide="trash-2" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="academic-years-mobile-list" data-academic-year-mobile-list>
                    <?php foreach ($years as $year): ?>
                        <?php $status = $statusMeta($year); ?>
                        <article
                            class="academic-year-card"
                            data-academic-year-card
                            data-id="<?= (int) $year['id'] ?>"
                            data-year="<?= esc($year['year']) ?>"
                            data-active="<?= (int) ($year['is_active'] ?? 0) ?>"
                            data-archived="<?= (int) ($year['is_archived'] ?? 0) ?>"
                        >
                            <div class="academic-year-card__top">
                                <div>
                                    <strong><?= esc($year['year']) ?></strong>
                                    <p><?= esc($year['label'] ?: 'Tahun Pelajaran ' . $year['year']) ?></p>
                                </div>
                                <span class="academic-year-status academic-year-status--<?= esc($status['tone']) ?>">
                                    <i data-lucide="<?= esc($status['icon']) ?>" aria-hidden="true"></i><?= esc($status['label']) ?>
                                </span>
                            </div>
                            <dl>
                                <div>
                                    <dt>Periode</dt>
                                    <dd><?= esc($periodText($year)) ?></dd>
                                </div>
                                <div>
                                    <dt>Catatan</dt>
                                    <dd><?= esc($year['notes'] ?: '-') ?></dd>
                                </div>
                            </dl>
                            <div class="academic-year-card__actions">
                                <?php if ((int) ($year['is_active'] ?? 0) !== 1): ?>
                                    <form action="<?= base_url('admin/academic-years/' . (int) $year['id'] . '/activate') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-success">
                                            <i data-lucide="check-circle-2" class="me-2" aria-hidden="true"></i>Aktifkan
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <button type="button" class="btn btn-outline-primary" data-academic-year-edit="<?= (int) $year['id'] ?>" data-bs-toggle="modal" data-bs-target="#editAcademicYear<?= (int) $year['id'] ?>">
                                    <i data-lucide="pencil" class="me-2" aria-hidden="true"></i>Edit
                                </button>
                                <?php if ((int) ($year['is_active'] ?? 0) !== 1): ?>
                                    <form action="<?= base_url('admin/academic-years/' . (int) $year['id'] . '/archive') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-secondary">
                                            <i data-lucide="archive" class="me-2" aria-hidden="true"></i><?= (int) ($year['is_archived'] ?? 0) === 1 ? 'Buka' : 'Arsip' ?>
                                        </button>
                                    </form>
                                    <form action="<?= base_url('admin/academic-years/' . (int) $year['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Hapus tahun pelajaran ini? Data terkait tahun tersebut tidak ikut dihapus.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" data-academic-year-delete="<?= (int) $year['id'] ?>">
                                            <i data-lucide="trash-2" class="me-2" aria-hidden="true"></i>Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
</section>

<div class="modal fade" id="createAcademicYear" tabindex="-1" aria-labelledby="createAcademicYearTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content academic-year-modal">
            <div class="modal-header">
                <div>
                    <p class="academic-years-kicker">Periode baru</p>
                    <h2 id="createAcademicYearTitle">Tambah Tahun</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= base_url('admin/academic-years/store') ?>" method="POST" data-academic-year-create-form>
                <?= csrf_field() ?>
                <div class="modal-body academic-year-form">
                    <div>
                        <label for="create_year">Tahun Pelajaran <span class="text-danger">*</span></label>
                        <input id="create_year" type="text" name="year" class="form-control" placeholder="2027/2028" inputmode="numeric" required>
                        <small>Gunakan format 2027/2028.</small>
                    </div>
                    <div>
                        <label for="create_label">Label</label>
                        <input id="create_label" type="text" name="label" class="form-control" placeholder="Tahun Pelajaran 2027/2028">
                    </div>
                    <div class="academic-year-form__dates">
                        <div>
                            <label for="create_starts_at">Mulai</label>
                            <input id="create_starts_at" type="date" name="starts_at" class="form-control">
                        </div>
                        <div>
                            <label for="create_ends_at">Selesai</label>
                            <input id="create_ends_at" type="date" name="ends_at" class="form-control">
                        </div>
                    </div>
                    <div>
                        <label for="create_notes">Catatan</label>
                        <textarea id="create_notes" name="notes" class="form-control" rows="3" placeholder="Contoh: periode penerimaan tahun depan."></textarea>
                    </div>
                    <label class="academic-year-switch" for="create_activate_now">
                        <input id="create_activate_now" type="checkbox" name="activate_now" value="1">
                        <span>Aktifkan sebagai tahun berjalan</span>
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="me-2" aria-hidden="true"></i>Simpan Tahun Pelajaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($years as $year): ?>
    <div class="modal fade" id="editAcademicYear<?= (int) $year['id'] ?>" tabindex="-1" aria-labelledby="editAcademicYearTitle<?= (int) $year['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content academic-year-modal">
                <div class="modal-header">
                    <div>
                        <p class="academic-years-kicker">Edit periode</p>
                        <h2 id="editAcademicYearTitle<?= (int) $year['id'] ?>">Tahun <?= esc($year['year']) ?></h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="<?= base_url('admin/academic-years/' . (int) $year['id'] . '/update') ?>" method="POST" data-academic-year-edit-form="<?= (int) $year['id'] ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body academic-year-form">
                        <div>
                            <label for="edit_label_<?= (int) $year['id'] ?>">Label</label>
                            <input id="edit_label_<?= (int) $year['id'] ?>" type="text" name="label" class="form-control" value="<?= esc($year['label'] ?: 'Tahun Pelajaran ' . $year['year']) ?>">
                        </div>
                        <div class="academic-year-form__dates">
                            <div>
                                <label for="edit_starts_at_<?= (int) $year['id'] ?>">Mulai</label>
                                <input id="edit_starts_at_<?= (int) $year['id'] ?>" type="date" name="starts_at" class="form-control" value="<?= esc($year['starts_at'] ?? '') ?>">
                            </div>
                            <div>
                                <label for="edit_ends_at_<?= (int) $year['id'] ?>">Selesai</label>
                                <input id="edit_ends_at_<?= (int) $year['id'] ?>" type="date" name="ends_at" class="form-control" value="<?= esc($year['ends_at'] ?? '') ?>">
                            </div>
                        </div>
                        <div>
                            <label for="edit_notes_<?= (int) $year['id'] ?>">Catatan</label>
                            <textarea id="edit_notes_<?= (int) $year['id'] ?>" name="notes" class="form-control" rows="3"><?= esc($year['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="me-2" aria-hidden="true"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection() ?>
