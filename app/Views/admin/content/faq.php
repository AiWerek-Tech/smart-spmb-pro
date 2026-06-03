<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-faq-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konten Publik</p>
            <h1 id="admin-faq-title">FAQ</h1>
            <p class="admin-page-subtitle">Kelola daftar pertanyaan yang sering diajukan pendaftar beserta jawabannya.</p>
        </div>
    </header>

    <div class="row g-3">
    <!-- LEFT SIDE: FAQ List Table -->
    <div class="col-lg-8">
        <div class="card admin-secondary-panel shadow-sm border">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="list-ol"></i> Daftar FAQ</h2>
                <p class="admin-section-subtitle">Diurutkan berdasarkan urutan tampilan kecil ke besar.</p>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">Urutan</th>
                                <th>Pertanyaan</th>
                                <th>Status</th>
                                <th class="text-center pe-4" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($faqs)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fs-1 mb-3" data-lucide="help-circle"></i>
                                        <p class="mb-0">Belum ada data FAQ yang dibuat.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($faqs as $f): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-primary">#<?= esc($f['sort_order']) ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark mb-1"><?= esc($f['question']) ?></div>
                                            <div class="text-muted small text-truncate" style="max-width: 400px;"><?= esc($f['answer']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($f['is_active']): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <!-- Edit Trigger button (Loads values in Modal via JS) -->
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-faq-btn me-1" 
                                                        data-id="<?= $f['id'] ?>"
                                                        data-question="<?= esc($f['question']) ?>"
                                                        data-answer="<?= esc($f['answer']) ?>"
                                                        data-sort="<?= $f['sort_order'] ?>"
                                                        data-active="<?= $f['is_active'] ?>"
                                                        title="Edit FAQ">
                                                    <i  data-lucide="edit"></i>
                                                </button>

                                                <!-- Delete button -->
                                                <form action="<?= base_url('admin/faq/'.$f['id'].'/delete') ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-confirm" title="Hapus FAQ">
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
        </div>
    </div>

    <!-- RIGHT SIDE: Add FAQ Form -->
    <div class="col-lg-4">
        <div class="card admin-secondary-panel shadow-sm border">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="plus"></i> Tambah FAQ Baru</h2>
                <p class="admin-section-subtitle">Masukkan data tanya jawab baru.</p>
            </div>
            
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/faq/store') ?>">
                    <?= csrf_field() ?>

                    <!-- Question -->
                    <div class="mb-3">
                        <label for="question" class="form-label fw-bold small">Pertanyaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="question" name="question" placeholder="Contoh: Kapan pendaftaran ditutup?" required value="<?= old('question') ?>">
                    </div>

                    <!-- Answer -->
                    <div class="mb-3">
                        <label for="answer" class="form-label fw-bold small">Jawaban <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="answer" name="answer" rows="5" required placeholder="Tuliskan penjelasan jawaban lengkap..." style="resize: none;"><?= old('answer') ?></textarea>
                    </div>

                    <!-- Sort Order -->
                    <div class="mb-3">
                        <label for="sort_order" class="form-label fw-bold small">Urutan Tampil <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= old('sort_order', count($faqs) + 1) ?>" min="1" required>
                    </div>

                    <!-- Active Switch -->
                    <div class="mb-4">
                        <div class="form-check form-switch p-0 ps-5 d-flex align-items-center">
                            <input class="form-check-input ms-n5 me-2" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked style="width: 2.5em; height: 1.25em;">
                            <label class="form-check-label fw-bold small" for="is_active">Aktifkan segera (Terbit)</label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="me-2" data-lucide="save"></i> Simpan FAQ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</section>

<!-- ================= EDIT FAQ MODAL ================= -->
<div class="modal fade" id="editFaqModal" tabindex="-1" aria-labelledby="editFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFaqModalLabel"><i class="me-1 text-primary" data-lucide="edit"></i> Edit FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFaqForm" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <!-- Question -->
                    <div class="mb-3">
                        <label for="edit_question" class="form-label fw-bold small">Pertanyaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_question" name="question" required>
                    </div>

                    <!-- Answer -->
                    <div class="mb-3">
                        <label for="edit_answer" class="form-label fw-bold small">Jawaban <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_answer" name="answer" rows="5" required style="resize: none;"></textarea>
                    </div>

                    <!-- Sort Order -->
                    <div class="mb-3">
                        <label for="edit_sort_order" class="form-label fw-bold small">Urutan Tampil <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="1" required>
                    </div>

                    <!-- Active Switch -->
                    <div class="mb-0">
                        <div class="form-check form-switch p-0 ps-5 d-flex align-items-center">
                            <input class="form-check-input ms-n5 me-2" type="checkbox" role="switch" id="edit_is_active" name="is_active" value="1" style="width: 2.5em; height: 1.25em;">
                            <label class="form-check-label fw-bold small" for="edit_is_active">Aktifkan segera (Terbit)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="me-1" data-lucide="save"></i> Perbarui FAQ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Edit button triggers modal populating
        $('.edit-faq-btn').on('click', function() {
            const id = $(this).data('id');
            const question = $(this).data('question');
            const answer = $(this).data('answer');
            const sort = $(this).data('sort');
            const active = $(this).data('active');

            // Populate forms
            $('#edit_question').val(question);
            $('#edit_answer').val(answer);
            $('#edit_sort_order').val(sort);
            
            if (active == 1) {
                $('#edit_is_active').prop('checked', true);
            } else {
                $('#edit_is_active').prop('checked', false);
            }

            // Set Form action endpoint
            $('#editFaqForm').attr('action', `<?= base_url('admin/faq') ?>/${id}/update`);

            // Show Modal
            const editModal = new bootstrap.Modal(document.getElementById('editFaqModal'));
            editModal.show();
        });
    });
</script>
<?= $this->endSection() ?>
