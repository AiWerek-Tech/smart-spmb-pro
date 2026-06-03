<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-announcements-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konten Publik</p>
            <h1 id="admin-announcements-title">Kelola Pengumuman</h1>
            <p class="admin-page-subtitle">Kelola info pengumuman resmi sekolah untuk calon pendaftar.</p>
        </div>
        <div class="admin-page-actions">
            <a href="<?= base_url('admin/announcements/create') ?>" class="btn btn-primary d-flex align-items-center">
                <i class="me-2" data-lucide="plus"></i> Buat Pengumuman
            </a>
        </div>
    </header>

    <!-- Announcements List Card -->
    <section class="admin-secondary-panel" aria-labelledby="announcements-table-title">
        <div class="admin-secondary-panel__header">
            <div>
                <h2 class="admin-section-title" id="announcements-table-title">Daftar Pengumuman</h2>
                <p class="admin-section-subtitle">Atur draft, publikasi, dan arsip pengumuman resmi.</p>
            </div>
        </div>
        <div class="card-body p-0">
                <div class="table-responsive admin-table-shell">
                    <table id="announcementsTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 50px;">No</th>
                                <th>Judul Pengumuman</th>
                                <th>Status Terbit</th>
                                <th>Tanggal Publikasi</th>
                                <th>Dibuat Tanggal</th>
                                <th class="text-center pe-4" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($announcements)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fs-1 mb-3" data-lucide="bullhorn"></i>
                                        <p class="mb-0">Belum ada pengumuman yang dibuat.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($announcements as $a): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark mb-1"><?= esc($a['title']) ?></div>
                                            <div class="text-muted small text-truncate" style="max-width: 400px;"><?= strip_tags($a['content']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($a['status'] === 'published'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="send"></i> Terbit
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                                    <i class="me-1" data-lucide="file-alt"></i> Draft
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small">
                                            <?php if (!empty($a['published_at'])): ?>
                                                <span class="text-dark fw-semibold"><i class="text-muted me-1" data-lucide="calendar"></i> <?= date('d M Y, H:i', strtotime($a['published_at'])) ?> WIB</span>
                                            <?php else: ?>
                                                <span class="text-muted italic">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                                        <td class="pe-4 text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <!-- Publish Toggle Form -->
                                                <form action="<?= base_url('admin/announcements/'.$a['id'].'/publish') ?>" method="POST" class="me-1">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-icon <?= $a['status'] === 'published' ? 'btn-outline-warning' : 'btn-outline-success' ?> p-1" style="width: 32px; height: 32px;" title="<?= $a['status'] === 'published' ? 'Tarik Kembali (Draft)' : 'Terbitkan Sekarang' ?>">
                                                        <?php if ($a['status'] === 'published'): ?>
                                                            <i data-lucide="eye-off"></i>
                                                        <?php else: ?>
                                                            <i data-lucide="eye"></i>
                                                        <?php endif; ?>
                                                    </button>
                                                </form>

                                                <!-- Edit button -->
                                                <a href="<?= base_url('admin/announcements/'.$a['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary p-1 me-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Pengumuman">
                                                    <i  data-lucide="edit"></i>
                                                </a>

                                                <!-- Delete button -->
                                                <form action="<?= base_url('admin/announcements/'.$a['id'].'/delete') ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger p-1 delete-confirm" style="width: 32px; height: 32px;" title="Hapus Pengumuman">
                                                        <i  data-lucide="trash-2"></i>
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
        if ($('#announcementsTable tbody tr').length > 1) {
            $('#announcementsTable').DataTable({
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
