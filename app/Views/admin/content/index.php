<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="row animate-fade-in">
    <!-- Header Page -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 text-primary">Profil & Galeri Sekolah</h4>
                <p class="text-muted mb-0">Kelola profil, lingkungan kampus, kebijakan legal, serta galeri foto/video yang tampil di website publik.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/banners') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="me-1" data-lucide="image"></i> Banner Hero
                </a>
                <a href="<?= base_url('admin/testimonials') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="me-1" data-lucide="message-square"></i> Testimoni
                </a>
                <a href="<?= base_url('admin/statistics') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="me-1" data-lucide="bar-chart-2"></i> Statistik
                </a>
            </div>
        </div>
    </div>

    <!-- LEFT SIDE: Profile Settings -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title text-primary"><i class="me-2" data-lucide="file-signature"></i> Sunting Profil Sekolah</h5>
                <small class="text-muted">Gunakan bahasa Indonesia yang baku dan formal.</small>
            </div>
            
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/content/save') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Tagline -->
                    <div class="mb-3">
                        <label for="tagline" class="form-label fw-bold small">Slogan / Tagline Sekolah</label>
                        <input type="text" class="form-control" id="tagline" name="tagline" value="<?= esc(old('tagline', $settings['tagline'] ?? '')) ?>" placeholder="Masukkan slogan/tagline sekolah...">
                    </div>

                    <!-- Vision -->
                    <div class="mb-3">
                        <label for="vision" class="form-label fw-bold small">Visi Sekolah <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="vision" name="vision" rows="3" required placeholder="Masukkan visi sekolah..."><?= esc(old('vision', $settings['vision'] ?? '')) ?></textarea>
                    </div>

                    <!-- Mission -->
                    <div class="mb-3">
                        <label for="mission" class="form-label fw-bold small">Misi Sekolah <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="mission" name="mission" rows="5" required placeholder="Masukkan misi sekolah (gunakan poin-poin/bullet)..."><?= esc(old('mission', $settings['mission'] ?? '')) ?></textarea>
                        <small class="text-muted">Gunakan pemisah baris baru untuk setiap butir misi sekolah.</small>
                    </div>

                    <!-- History -->
                    <div class="mb-4">
                        <label for="history" class="form-label fw-bold small">Sejarah Singkat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="history" name="history" rows="6" required placeholder="Tuliskan sejarah singkat berdirinya sekolah..."><?= esc(old('history', $settings['history'] ?? '')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="school_founded_year" class="form-label fw-bold small">Tahun Berdiri</label>
                        <input type="number" class="form-control" id="school_founded_year" name="school_founded_year" value="<?= esc(old('school_founded_year', $settings['school_founded_year'] ?? '')) ?>" min="1900" max="<?= date('Y') + 1 ?>" placeholder="Contoh: 2010">
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="school_facilities" class="form-label fw-bold small">Daftar Fasilitas Sekolah</label>
                        <textarea class="form-control" id="school_facilities" name="school_facilities" rows="4" placeholder="Satu fasilitas per baris..."><?= esc(old('school_facilities', $settings['school_facilities'] ?? '')) ?></textarea>
                        <small class="text-muted">Data ini dipakai pada halaman Profil dan Lingkungan & Kampus.</small>
                    </div>

                    <div class="mb-3">
                        <label for="campus_title" class="form-label fw-bold small">Judul Lingkungan & Kampus</label>
                        <input type="text" class="form-control" id="campus_title" name="campus_title" value="<?= esc(old('campus_title', $settings['campus_title'] ?? '')) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="campus_description" class="form-label fw-bold small">Deskripsi Lingkungan & Kampus</label>
                        <textarea class="form-control" id="campus_description" name="campus_description" rows="5"><?= esc(old('campus_description', $settings['campus_description'] ?? '')) ?></textarea>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Brosur SPMB Resmi</label>
                        <input type="file" class="form-control" name="brochure_file" accept="application/pdf">
                        <small class="text-muted">
                            Unggah PDF maksimal 5 MB. 
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

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="privacy_policy" class="form-label fw-bold small">Kebijakan Privasi</label>
                        <textarea class="form-control" id="privacy_policy" name="privacy_policy" rows="5"><?= esc(old('privacy_policy', $settings['privacy_policy'] ?? '')) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="terms_conditions" class="form-label fw-bold small">Syarat & Ketentuan</label>
                        <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="5"><?= esc(old('terms_conditions', $settings['terms_conditions'] ?? '')) ?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="me-2" data-lucide="save"></i> Perbarui Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Teacher + Gallery Management -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title text-primary"><i class="me-2" data-lucide="users"></i> Tenaga Pendidik</h5>
                    <small class="text-muted">Data guru tampil pada halaman Profil Sekolah.</small>
                </div>
                <span class="badge bg-label-primary rounded"><?= count($teachers ?? []) ?> Guru</span>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= base_url('admin/content/teachers/store') ?>" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded border">
                    <?= csrf_field() ?>
                    <label class="form-label fw-bold small mb-2"><i class="me-1 text-primary" data-lucide="user-plus"></i> Tambah Guru</label>
                    <div class="row g-2">
                        <div class="col-12">
                            <input type="text" class="form-control form-control-sm" name="name" placeholder="Nama lengkap guru" required>
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control form-control-sm" name="role" placeholder="Jabatan / mata pelajaran" required>
                        </div>
                        <div class="col-12">
                            <input type="file" class="form-control form-control-sm" name="teacher_photo" accept="image/*">
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" name="sort_order" value="0" min="0" placeholder="Urutan">
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="new_teacher_active" checked>
                                <label class="form-check-label small" for="new_teacher_active">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary btn-sm px-3 w-100" type="submit">
                                <i data-lucide="plus"></i> Tambahkan Guru
                            </button>
                        </div>
                    </div>
                </form>

                <div class="d-flex flex-column gap-2 overflow-auto" style="max-height: 380px;">
                    <?php if (empty($teachers)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fs-1 mb-2" data-lucide="users"></i>
                            <p class="mb-0">Belum ada data guru.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <?php
                                $teacherPhoto = !empty($teacher['photo'])
                                    ? ((strpos($teacher['photo'], 'http') === 0) ? esc($teacher['photo']) : base_url(esc($teacher['photo'])))
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($teacher['name'] ?? 'Guru') . '&background=6366f1&color=fff&size=96';
                            ?>
                            <div class="p-2 rounded border bg-white d-flex align-items-center gap-3">
                                <img src="<?= $teacherPhoto ?>" alt="<?= esc($teacher['name']) ?>" class="rounded-circle object-fit-cover border" style="width:48px;height:48px;" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($teacher['name'] ?? 'Guru') ?>&background=6366f1&color=fff&size=96'">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark text-truncate"><?= esc($teacher['name']) ?></div>
                                    <div class="small text-muted text-truncate"><?= esc($teacher['role']) ?></div>
                                </div>
                                <span class="badge <?= ($teacher['is_active'] ?? 0) ? 'bg-success' : 'bg-secondary' ?> bg-opacity-10 <?= ($teacher['is_active'] ?? 0) ? 'text-success' : 'text-secondary' ?>"><?= ($teacher['is_active'] ?? 0) ? 'Aktif' : 'Nonaktif' ?></span>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTeacherModal<?= $teacher['id'] ?>" title="Edit Guru">
                                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                                </button>
                                <form action="<?= base_url('admin/content/teachers/'.$teacher['id'].'/delete') ?>" method="POST" onsubmit="return confirm('Hapus data guru ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Guru">
                                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="modal fade" id="editTeacherModal<?= $teacher['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="<?= base_url('admin/content/teachers/'.$teacher['id'].'/update') ?>" enctype="multipart/form-data">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Data Guru</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Nama Lengkap</label>
                                                    <input type="text" class="form-control" name="name" value="<?= esc($teacher['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Jabatan / Mata Pelajaran</label>
                                                    <input type="text" class="form-control" name="role" value="<?= esc($teacher['role']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Ganti Foto</label>
                                                    <input type="file" class="form-control" name="teacher_photo" accept="image/*">
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label small fw-bold">Urutan</label>
                                                        <input type="number" class="form-control" name="sort_order" value="<?= (int) ($teacher['sort_order'] ?? 0) ?>" min="0">
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-end mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="is_active" id="teacher-active-<?= $teacher['id'] ?>" <?= ($teacher['is_active'] ?? 0) ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="teacher-active-<?= $teacher['id'] ?>">Aktif</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title text-primary"><i class="me-2" data-lucide="images"></i> Galeri Sekolah</h5>
                    <small class="text-muted">Foto dan video yang tampil di homepage, halaman galeri, dan lingkungan kampus.</small>
                </div>
                <span class="badge bg-label-primary rounded"><?= count($gallery) ?> Item</span>
            </div>
            
            <div class="card-body">
                <!-- Upload form -->
                <form method="POST" action="<?= base_url('admin/content/gallery/upload') ?>" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded border">
                    <?= csrf_field() ?>
                    <label class="form-label fw-bold small mb-2"><i class="me-1 text-primary" data-lucide="upload"></i> Tambah Item Galeri</label>
                    <div class="row g-2">
                        <div class="col-12">
                            <input type="text" class="form-control form-control-sm" name="title" placeholder="Judul galeri" required>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control form-control-sm" name="description" rows="2" placeholder="Deskripsi singkat..."></textarea>
                        </div>
                        <div class="col-6">
                            <select class="form-select form-select-sm gallery-media-type" name="media_type" data-target="#new-video-url" data-file="#gallery_image">
                                <option value="photo">Foto</option>
                                <option value="video">Video YouTube</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control form-control-sm" name="category" placeholder="Kategori, mis. Fasilitas">
                        </div>
                        <div class="col-12">
                            <input type="file" class="form-control form-control-sm" id="gallery_image" name="gallery_image" required>
                            <small class="text-muted">Foto: wajib unggah gambar. Video: gambar opsional sebagai thumbnail.</small>
                        </div>
                        <div class="col-12 d-none" id="new-video-url">
                            <input type="url" class="form-control form-control-sm" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" name="sort_order" value="0" min="0" placeholder="Urutan">
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="new_gallery_active" checked>
                                <label class="form-check-label small" for="new_gallery_active">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary btn-sm px-3 w-100" type="submit">
                                <i data-lucide="plus"></i> Tambahkan ke Galeri
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Grid list -->
                <div class="row g-2 overflow-auto" style="max-height: 620px;">
                    <?php if (empty($gallery)): ?>
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fs-1 mb-2" data-lucide="images"></i>
                            <p class="mb-0">Belum ada foto galeri.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($gallery as $item): ?>
                            <div class="col-6 col-sm-4 col-md-6 position-relative gallery-item-wrapper" style="height: 150px;">
                                <img src="<?= (strpos($item['image'], 'http') === 0) ? esc($item['image']) : base_url(esc($item['image'])) ?>" class="img-fluid w-100 h-100 rounded border object-fit-cover shadow-xs" alt="<?= esc($item['title']) ?>" onerror="this.onerror=null;this.src='<?= base_url('assets/img/gallery-placeholder.svg') ?>';">
                                <button type="button" class="btn btn-primary btn-sm p-1 rounded-circle position-absolute top-0 start-0 m-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; border: none;" title="Edit Item" data-bs-toggle="modal" data-bs-target="#editGalleryModal<?= $item['id'] ?>">
                                    <i data-lucide="pencil" style="width:14px;height:14px;"></i>
                                </button>
                                <span class="badge <?= ($item['media_type'] ?? 'photo') === 'video' ? 'bg-danger' : 'bg-primary' ?> position-absolute bottom-0 start-0 m-2"><?= ($item['media_type'] ?? 'photo') === 'video' ? 'Video' : 'Foto' ?></span>
                                
                                <!-- Delete Overlay Trigger -->
                                <form action="<?= base_url('admin/content/gallery/'.$item['id'].'/delete') ?>" method="POST" onsubmit="return confirm('Hapus foto ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm p-1 rounded-circle position-absolute top-0 end-0 m-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background-color: rgba(255, 62, 29, 0.85); border: none;" title="Hapus Foto">
                                        <i class="font-size-xs" data-lucide="trash-2"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="modal fade" id="editGalleryModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <form method="POST" action="<?= base_url('admin/content/gallery/'.$item['id'].'/update') ?>" enctype="multipart/form-data">
                                            <?= csrf_field() ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Item Galeri</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <label class="form-label small fw-bold">Judul</label>
                                                        <input type="text" class="form-control" name="title" value="<?= esc($item['title']) ?>" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Jenis Media</label>
                                                        <select class="form-select gallery-media-type" name="media_type" data-target="#edit-video-url-<?= $item['id'] ?>" data-file="#edit-gallery-image-<?= $item['id'] ?>">
                                                            <option value="photo" <?= ($item['media_type'] ?? 'photo') === 'photo' ? 'selected' : '' ?>>Foto</option>
                                                            <option value="video" <?= ($item['media_type'] ?? 'photo') === 'video' ? 'selected' : '' ?>>Video YouTube</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-bold">Deskripsi</label>
                                                        <textarea class="form-control" name="description" rows="3"><?= esc($item['description'] ?? '') ?></textarea>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Kategori</label>
                                                        <input type="text" class="form-control" name="category" value="<?= esc($item['category'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-bold">Urutan</label>
                                                        <input type="number" class="form-control" name="sort_order" value="<?= (int) ($item['sort_order'] ?? 0) ?>" min="0">
                                                    </div>
                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <div class="form-check form-switch mb-2">
                                                            <input class="form-check-input" type="checkbox" name="is_active" id="gallery-active-<?= $item['id'] ?>" <?= ($item['is_active'] ?? 0) ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="gallery-active-<?= $item['id'] ?>">Aktif</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 <?= ($item['media_type'] ?? 'photo') === 'video' ? '' : 'd-none' ?>" id="edit-video-url-<?= $item['id'] ?>">
                                                        <label class="form-label small fw-bold">URL YouTube</label>
                                                        <input type="url" class="form-control" name="video_url" value="<?= esc($item['video_url'] ?? '') ?>">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-bold">Ganti Thumbnail/Foto</label>
                                                        <input type="file" class="form-control" id="edit-gallery-image-<?= $item['id'] ?>" name="gallery_image">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function syncMediaType(select) {
        const target = document.querySelector(select.dataset.target);
        const fileInput = document.querySelector(select.dataset.file);
        const isVideo = select.value === 'video';
        if (target) target.classList.toggle('d-none', !isVideo);
        const videoInput = target ? target.querySelector('input[name="video_url"]') : null;
        if (videoInput) videoInput.required = isVideo;
        if (fileInput) fileInput.required = !isVideo && fileInput.id === 'gallery_image';
    }

    document.querySelectorAll('.gallery-media-type').forEach(function(select) {
        syncMediaType(select);
        select.addEventListener('change', function() {
            syncMediaType(select);
        });
    });
});
</script>
<?= $this->endSection() ?>
