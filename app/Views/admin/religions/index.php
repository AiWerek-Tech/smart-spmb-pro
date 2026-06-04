<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-religions-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konfigurasi Sistem</p>
            <h1 id="admin-religions-title">Sub-Agama & Aliran</h1>
            <p class="admin-page-subtitle">Kelola daftar agama resmi dan aliran/sub-agama yang tersedia pada formulir pendaftaran.</p>
        </div>
        <div class="admin-page-actions">
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addReligionModal">
                <i class="me-1" data-lucide="plus-circle"></i> Tambah Agama
            </button>
        </div>
    </header>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="me-2" data-lucide="check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="me-2" data-lucide="alert-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="me-2" data-lucide="alert-circle"></i> <strong>Gagal menyimpan data:</strong>
            <ul class="mb-0 mt-1 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if (empty($religions)): ?>
            <div class="col-12 text-center py-5 text-muted card shadow-xs border">
                <i data-lucide="info" class="mb-2 text-muted" style="width: 48px; height: 48px;"></i>
                <p class="mb-0 fw-semibold">Belum ada data agama</p>
                <small>Silakan tambahkan agama baru.</small>
            </div>
        <?php else: ?>
            <?php foreach ($religions as $rel): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border">
                        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title m-0 text-dark fw-bold"><?= esc($rel['name']) ?></h5>
                            </div>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i data-lucide="more-vertical" class="text-muted" style="width: 18px; height: 18px;"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item edit-religion-btn" href="javascript:void(0);" data-id="<?= $rel['id'] ?>" data-name="<?= esc($rel['name']) ?>">
                                        <i class="me-2" data-lucide="edit-2" style="width:14px;height:14px;"></i> Edit Nama
                                    </a>
                                    <a class="dropdown-item text-danger delete-religion-btn" href="javascript:void(0);" data-id="<?= $rel['id'] ?>" data-name="<?= esc($rel['name']) ?>">
                                        <i class="me-2" data-lucide="trash-2" style="width:14px;height:14px;"></i> Hapus Agama
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <h6 class="text-muted small fw-semibold mb-2">Aliran / Sub-Agama:</h6>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <?php if (empty($rel['subgroups'])): ?>
                                    <span class="text-muted small italic">Belum ada aliran ditambahkan</span>
                                <?php else: ?>
                                    <?php foreach ($rel['subgroups'] as $sub): ?>
                                        <div class="badge bg-label-primary border d-flex align-items-center gap-1 py-2 px-3" style="font-size:0.8rem;">
                                            <span><?= esc($sub['name']) ?></span>
                                            <a href="javascript:void(0);" class="text-danger ms-1 delete-subgroup-btn" data-id="<?= $sub['id'] ?>" data-name="<?= esc($sub['name']) ?>" title="Hapus Aliran">
                                                <i data-lucide="x" style="width:12px;height:12px;"></i>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 pt-0 pb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 add-subgroup-btn" data-id="<?= $rel['id'] ?>" data-name="<?= esc($rel['name']) ?>">
                                <i class="me-1" data-lucide="plus" style="width:14px;height:14px;"></i> Tambah Aliran / Aliran
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Tambah Agama -->
<div class="modal fade" id="addReligionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/religions/store') ?>" method="POST" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Tambah Agama Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="rel_name" class="form-label required-field">Nama Agama</label>
                    <input type="text" class="form-control" id="rel_name" name="name" placeholder="Contoh: Islam, Kristen, Katolik" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Agama -->
<div class="modal fade" id="editReligionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editReligionForm" method="POST" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Edit Nama Agama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="edit_rel_name" class="form-label required-field">Nama Agama</label>
                    <input type="text" class="form-control" id="edit_rel_name" name="name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Sub-Agama / Aliran -->
<div class="modal fade" id="addSubgroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/religions/subgroups/store') ?>" method="POST" class="modal-content">
            <?= csrf_field() ?>
            <input type="hidden" id="sub_religion_id" name="religion_id">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Tambah Aliran - <span id="sub_religion_name_title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="sub_name" class="form-label required-field">Nama Aliran / Sub-Agama</label>
                    <input type="text" class="form-control" id="sub_name" name="name" placeholder="Contoh: Sunni, Syiah, Protestan" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Edit Agama Modal
        $('.edit-religion-btn').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            $('#edit_rel_name').val(name);
            $('#edit_religionForm').attr('action', `<?= base_url('admin/religions') ?>/${id}/update`);
            $('#editReligionModal').modal('show');
        });

        // Tambah Sub-Agama Modal
        $('.add-subgroup-btn').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            $('#sub_religion_id').val(id);
            $('#sub_religion_name_title').text(name);
            $('#sub_name').val('');
            $('#addSubgroupModal').modal('show');
        });

        // Delete Agama
        $('.delete-religion-btn').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            Swal.fire({
                title: 'Hapus Agama?',
                text: `Apakah Anda yakin ingin menghapus agama "${name}" beserta seluruh aliran di bawahnya? Data siswa dengan agama ini akan kehilangan relasinya.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('<form>', {
                        method: 'POST',
                        action: `<?= base_url('admin/religions') ?>/${id}/delete`
                    });
                    form.append($('`<input>`', {
                        type: 'hidden',
                        name: '<?= csrf_token() ?>',
                        value: '<?= csrf_hash() ?>'
                    }));
                    $('body').append(form);
                    form.submit();
                }
            });
        });

        // Delete Subgroup
        $('.delete-subgroup-btn').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            Swal.fire({
                title: 'Hapus Aliran?',
                text: `Apakah Anda yakin ingin menghapus aliran "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $('<form>', {
                        method: 'POST',
                        action: `<?= base_url('admin/religions/subgroups') ?>/${id}/delete`
                    });
                    form.append($('`<input>`', {
                        type: 'hidden',
                        name: '<?= csrf_token() ?>',
                        value: '<?= csrf_hash() ?>'
                    }));
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
