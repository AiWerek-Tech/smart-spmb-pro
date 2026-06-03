<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="admin-page-shell animate-fade-in" aria-labelledby="admin-backup-title">
    <header class="admin-page-header">
        <div>
            <p class="admin-panel__kicker">Keamanan Data</p>
            <h1 id="admin-backup-title">Backup & Restore Database</h1>
            <p class="admin-page-subtitle">Kelola cadangan data sistem SPMB untuk menjaga keamanan informasi dan memulihkannya jika diperlukan.</p>
        </div>
    </header>

    <div class="admin-page-grid admin-page-grid--two">
        <!-- BACKUP PANEL -->
        <section class="admin-secondary-panel h-100" aria-labelledby="backup-export-title">
            <div class="admin-secondary-panel__header">
                <div>
                    <h2 class="admin-section-title text-primary" id="backup-export-title"><i data-lucide="download" class="me-2 d-inline-block align-middle"></i> Ekspor Cadangan</h2>
                    <p class="admin-section-subtitle">Ekspor seluruh skema dan data database ke dalam file SQL.</p>
                </div>
            </div>

            <div class="admin-secondary-panel__body d-flex flex-column justify-content-between h-100">
                <div>
                    <div class="admin-soft-zone text-center py-4 mb-3 d-flex justify-content-center align-items-center" style="height: 120px;">
                        <i data-lucide="database" class="text-primary" style="width: 64px; height: 64px; filter: drop-shadow(0 4px 10px rgba(99, 102, 241, 0.25));"></i>
                    </div>
                    <p class="text-muted small">
                        Melakukan backup database secara rutin sangat disarankan sebelum melakukan update besar, perbaikan sistem, atau penutupan gelombang pendaftaran.
                    </p>
                    <ul class="small text-muted ps-3">
                        <li>Menyimpan seluruh data pengguna, pendaftar, dan pengaturan.</li>
                        <li>Format file yang diunduh adalah `.sql` yang dapat dibaca di MySQL.</li>
                        <li>Dapat di-restore kembali kapan saja melalui menu restore di sebelah kanan.</li>
                    </ul>
                </div>

                <form method="POST" action="<?= base_url('admin/backup/create') ?>" class="d-grid mt-3">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i data-lucide="file-down" class="me-2 d-inline-block align-middle" style="width: 20px; height: 20px;"></i> Unduh Cadangan Database
                    </button>
                </form>
            </div>
        </section>

        <!-- RESTORE PANEL -->
        <section class="admin-secondary-panel h-100" aria-labelledby="backup-restore-title">
            <div class="admin-secondary-panel__header">
                <div>
                    <h2 class="admin-section-title text-danger" id="backup-restore-title"><i data-lucide="upload" class="me-2 d-inline-block align-middle"></i> Impor Cadangan</h2>
                    <p class="admin-section-subtitle">Pulihkan struktur dan data database dari file SQL eksternal.</p>
                </div>
            </div>

            <div class="admin-secondary-panel__body">
                <!-- Caution Alert Banner -->
                <div class="admin-danger-zone p-3 mb-4">
                    <div class="d-flex align-items-start">
                        <i data-lucide="alert-triangle" class="me-3 mt-1" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
                        <div>
                            <h2 class="admin-section-title text-warning mb-1">Peringatan Kritis</h2>
                            <p class="mb-0 small">
                                Proses pemulihan (restore) akan <strong>menimpa dan menghapus semua data saat ini</strong> dengan data dari file cadangan yang Anda unggah. Tindakan ini tidak dapat dibatalkan!
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="<?= base_url('admin/backup/restore') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- File input -->
                    <div class="mb-3">
                        <label for="backup_file" class="form-label fw-bold small">Pilih File Cadangan (.sql) <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="backup_file" name="backup_file" accept=".sql" required>
                        <small class="text-muted d-block mt-1">Ukuran maksimal file: 10 MB. Harus berupa ekstensi file `.sql`.</small>
                    </div>

                    <!-- Risk confirmation check -->
                    <div class="mb-4 admin-soft-zone p-3">
                        <div class="form-check form-switch p-0 ps-5 d-flex align-items-center">
                            <input class="form-check-input ms-n5 me-2" type="checkbox" role="switch" id="confirm" name="confirm" value="1" style="width: 2.2em; height: 1.1em; cursor: pointer;">
                            <label class="form-check-label fw-bold small" for="confirm" style="cursor: pointer;">
                                Saya memahami risiko ini dan setuju menimpa data sistem saat ini
                            </label>
                        </div>
                    </div>

                    <!-- Action Submit -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger btn-lg" id="restore-btn" disabled>
                            <i data-lucide="database" class="me-2 d-inline-block align-middle" style="width: 20px; height: 20px;"></i> Mulai Pemulihan Database
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const confirmCheckbox = document.getElementById("confirm");
        const restoreButton = document.getElementById("restore-btn");

        // Enable or disable restore button based on confirmation check
        confirmCheckbox.addEventListener("change", function() {
            restoreButton.disabled = !this.checked;
        });

        // Safe confirmation alert on restore form submit
        restoreButton.closest("form").addEventListener("submit", function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Terakhir',
                text: "Apakah Anda benar-benar yakin ingin memulihkan database? Seluruh data saat ini akan terhapus total!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff3e1d',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, Mulai Restore!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Pemulihan...',
                        text: 'Harap tunggu, proses ini membutuhkan waktu beberapa detik.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    this.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
