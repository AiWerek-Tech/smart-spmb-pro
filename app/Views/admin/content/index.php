<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<section class="sp-admin-page admin-page-shell animate-fade-in" aria-labelledby="admin-content-title">
    <header class="sp-page-toolbar admin-page-header">
        <div>
            <p class="admin-panel__kicker">Konten Publik</p>
            <h1 id="admin-content-title">Profil Sekolah</h1>
            <p class="admin-page-subtitle">Kelola identitas, sejarah, visi misi, fasilitas, jadwal lanjutan SPMB, kebijakan legal, dan brosur resmi.</p>
        </div>
        <div class="sp-toolbar-actions admin-page-actions">
            <span class="sp-status-pill"><i data-lucide="calendar"></i> <?= esc($activeYear ?? '-') ?></span>
            <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-primary"><i data-lucide="users"></i> Tenaga Pendidik</a>
            <a href="<?= base_url('admin/gallery') ?>" class="btn btn-outline-primary"><i data-lucide="image"></i> Galeri</a>
        </div>
    </header>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card admin-secondary-panel shadow-sm border">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="admin-section-title text-primary"><i class="me-2" data-lucide="file-signature"></i> Sunting Profil Sekolah</h2>
                    <p class="admin-section-subtitle">Data ini menjadi konten utama halaman profil, lingkungan kampus, footer legal, dan brosur SPMB.</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('admin/content/save') ?>" enctype="multipart/form-data" class="sp-compact-form">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="tagline" class="form-label fw-bold small">Slogan / Tagline Sekolah</label>
                            <input type="text" class="form-control" id="tagline" name="tagline" value="<?= esc(old('tagline', $settings['tagline'] ?? '')) ?>" placeholder="Masukkan slogan/tagline sekolah...">
                        </div>

                        <div class="mb-3">
                            <label for="vision" class="form-label fw-bold small">Visi Sekolah <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="vision" name="vision" rows="3" required><?= esc(old('vision', $settings['vision'] ?? '')) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="mission" class="form-label fw-bold small">Misi Sekolah <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="mission" name="mission" rows="5" required><?= esc(old('mission', $settings['mission'] ?? '')) ?></textarea>
                            <small class="text-muted">Gunakan baris baru untuk setiap butir misi.</small>
                        </div>

                        <div class="mb-3">
                            <label for="history" class="form-label fw-bold small">Sejarah Singkat <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="history" name="history" rows="6" required><?= esc(old('history', $settings['history'] ?? '')) ?></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="school_founded_year" class="form-label fw-bold small">Tahun Berdiri</label>
                                <input type="number" class="form-control" id="school_founded_year" name="school_founded_year" value="<?= esc(old('school_founded_year', $settings['school_founded_year'] ?? '')) ?>" min="1900" max="<?= date('Y') + 1 ?>">
                            </div>
                            <div class="col-md-8">
                                <label for="campus_title" class="form-label fw-bold small">Judul Lingkungan & Kampus</label>
                                <input type="text" class="form-control" id="campus_title" name="campus_title" value="<?= esc(old('campus_title', $settings['campus_title'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="school_facilities" class="form-label fw-bold small">Daftar Fasilitas Sekolah</label>
                            <textarea class="form-control" id="school_facilities" name="school_facilities" rows="4" placeholder="Satu fasilitas per baris..."><?= esc(old('school_facilities', $settings['school_facilities'] ?? '')) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="campus_description" class="form-label fw-bold small">Deskripsi Lingkungan & Kampus</label>
                            <textarea class="form-control" id="campus_description" name="campus_description" rows="5"><?= esc(old('campus_description', $settings['campus_description'] ?? '')) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Brosur SPMB Resmi</label>
                            <input type="file" class="form-control" name="brochure_file" accept="application/pdf">
                            <small class="text-muted">
                                PDF maksimal 5 MB.
                                <?php if (!empty($settings['brochure_file'])): ?>
                                    File aktif: <a href="<?= base_url('/brosur') ?>" target="_blank" rel="noopener">lihat/unduh brosur</a>.
                                <?php else: ?>
                                    Belum ada brosur aktif.
                                <?php endif; ?>
                            </small>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="spmb_re_registration_start" class="form-label fw-bold small">Mulai Daftar Ulang</label>
                                <input type="date" class="form-control" id="spmb_re_registration_start" name="spmb_re_registration_start" value="<?= esc(old('spmb_re_registration_start', $settings['spmb_re_registration_start'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="spmb_re_registration_end" class="form-label fw-bold small">Akhir Daftar Ulang</label>
                                <input type="date" class="form-control" id="spmb_re_registration_end" name="spmb_re_registration_end" value="<?= esc(old('spmb_re_registration_end', $settings['spmb_re_registration_end'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="spmb_mpls_start" class="form-label fw-bold small">Mulai MPLS</label>
                                <input type="date" class="form-control" id="spmb_mpls_start" name="spmb_mpls_start" value="<?= esc(old('spmb_mpls_start', $settings['spmb_mpls_start'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="spmb_mpls_end" class="form-label fw-bold small">Akhir MPLS</label>
                                <input type="date" class="form-control" id="spmb_mpls_end" name="spmb_mpls_end" value="<?= esc(old('spmb_mpls_end', $settings['spmb_mpls_end'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="privacy_policy" class="form-label fw-bold small">Kebijakan Privasi</label>
                            <textarea class="form-control" id="privacy_policy" name="privacy_policy" rows="5"><?= esc(old('privacy_policy', $settings['privacy_policy'] ?? '')) ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="terms_conditions" class="form-label fw-bold small">Syarat & Ketentuan</label>
                            <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="5"><?= esc(old('terms_conditions', $settings['terms_conditions'] ?? '')) ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="me-2" data-lucide="save"></i> Perbarui Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-3">
                <a href="<?= base_url('admin/teachers') ?>" class="sp-mobile-record text-decoration-none">
                    <div>
                        <div class="sp-record-title"><i data-lucide="users"></i> Tenaga Pendidik</div>
                        <div class="sp-record-meta">Data guru dikelola terpisah berdasarkan tahun pelajaran aktif.</div>
                    </div>
                    <span class="btn btn-outline-primary btn-sm">Buka</span>
                </a>
                <a href="<?= base_url('admin/gallery') ?>" class="sp-mobile-record text-decoration-none">
                    <div>
                        <div class="sp-record-title"><i data-lucide="image"></i> Galeri Sekolah</div>
                        <div class="sp-record-meta">Foto dan video YouTube untuk homepage, profil, dan lingkungan kampus.</div>
                    </div>
                    <span class="btn btn-outline-primary btn-sm">Buka</span>
                </a>
                <a href="<?= base_url('admin/academic-years') ?>" class="sp-mobile-record text-decoration-none">
                    <div>
                        <div class="sp-record-title"><i data-lucide="calendar-range"></i> Tahun Pelajaran</div>
                        <div class="sp-record-meta">Pusat arsip data dan direktori upload SPMB.</div>
                    </div>
                    <span class="btn btn-outline-primary btn-sm">Buka</span>
                </a>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
