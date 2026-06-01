<!-- Profile Detail Modal -->
<div class="modal fade" id="profileDetailModal" tabindex="-1" aria-labelledby="profileDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileDetailModalLabel">
                    <i class="me-2 text-primary" data-lucide="user"></i> Detail Profil Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('user_name') ?? 'User') ?>&background=7c3aed&color=fff&size=90" class="rounded-circle border p-1" alt="Avatar">
                    <h5 class="mt-2 text-dark fw-bold"><?= esc(session()->get('user_name')) ?></h5>
                    <span class="badge bg-label-primary px-3 py-1 rounded-pill text-uppercase"><?= esc(session()->get('user_role')) ?></span>
                </div>
                <table class="table table-sm table-borderless text-dark">
                    <tr>
                        <td class="fw-semibold text-muted" style="width: 150px;">Email</td>
                        <td>: <?= esc(session()->get('user_email') ?: 'user@smartspmbpro.sch.id') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Status Akun</td>
                        <td>: <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0">Aktif</span></td>
                    </tr>
                    <tr>
                        <td class="fw-semibold text-muted">Dibuat Tanggal</td>
                        <td>: 29 May 2026</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Panduan Penggunaan Modal -->
<div class="modal fade" id="panduanModal" tabindex="-1" aria-labelledby="panduanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="panduanModalLabel">
                    <i class="me-2 text-primary" data-lucide="book-open"></i> Panduan Penggunaan Platform
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <div class="row">
                    <div class="col-md-4 text-center border-end d-none d-md-block">
                        <div class="p-3 bg-light rounded mb-3">
                            <i data-lucide="info" class="text-primary d-block mx-auto mb-2" style="width: 40px; height: 40px;"></i>
                            <h6 class="fw-bold">Tentang Platform</h6>
                            <p class="small text-muted mb-0">Sistem Penerimaan Peswa Baru Online yang modern, transparan, dan efisien.</p>
                        </div>
                        <div class="badge bg-label-info w-100 p-2">Versi <?= esc($appInfo->version ?? '1.0.0') ?> Pro</div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-3">Langkah Pendaftaran Calon Siswa:</h6>
                        <ol class="ps-3 mb-4 small text-muted">
                            <li class="mb-2"><strong>Lengkapi Profil:</strong> Isi seluruh formulir pendaftaran secara valid mulai dari biodata, alamat, hingga riwayat akademik.</li>
                            <li class="mb-2"><strong>Unggah Dokumen:</strong> Unggah dokumen persyaratan wajib seperti Kartu Keluarga, Akta Kelahiran, Pas Foto, dan rapor.</li>
                            <li class="mb-2"><strong>Finalisasi Berkas:</strong> Cek kembali semua data. Jika sudah yakin, lakukan submit/finalisasi berkas untuk review panitia.</li>
                            <li class="mb-2"><strong>Pantau Status:</strong> Operator sekolah akan meninjau berkas Anda. Status akan berubah menjadi <em>Berkas Valid</em> atau <em>Ditolak</em>.</li>
                            <li class="mb-2"><strong>Pengumuman Akhir:</strong> Hasil kelulusan akan diumumkan sesuai jadwal gelombang.</li>
                        </ol>
                        <h6 class="fw-bold mb-2">Peran Akun Lainnya:</h6>
                        <ul class="ps-3 small text-muted">
                            <li class="mb-1"><strong>Operator:</strong> Melakukan verifikasi berkas calon pendaftar & validasi Dapodik.</li>
                            <li class="mb-1"><strong>Admin:</strong> Mengelola kuota jalur, gelombang pendaftaran, FAQ, konfigurasi sistem, & hasil ranking seleksi.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Bantuan & Kontak Modal -->
<div class="modal fade" id="bantuanModal" tabindex="-1" aria-labelledby="bantuanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bantuanModalLabel">
                    <i class="me-2 text-primary" data-lucide="help-circle"></i> Bantuan & Layanan Kontak
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4 text-dark text-center">
                <i data-lucide="message-square" class="text-primary d-block mx-auto mb-3" style="width: 48px; height: 48px;"></i>
                <h5 class="fw-bold mb-1">Hubungi Helpdesk Kami</h5>
                <p class="small text-muted mb-4">Jika Anda mengalami kendala teknis atau memiliki pertanyaan terkait SPMB, silakan hubungi tim kami.</p>
                
                <div class="d-flex flex-column gap-2 text-start">
                    <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small text-muted d-block">WhatsApp Call Center</span>
                            <span class="fw-bold text-dark"><?= esc($settingModel->getValue('whatsapp', $appInfo->developerPhone ?? '082190822641')) ?></span>
                        </div>
                        <a href="https://wa.me/<?= esc($supportPhone ?: ($appInfo->developerWhatsapp ?? '6282190822641')) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-success">
                            <i data-lucide="phone-call" style="width: 14px; height: 14px;"></i> Chat
                        </a>
                    </div>
                    <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small text-muted d-block">Email Dukungan</span>
                            <span class="fw-bold text-dark"><?= esc($supportEmail ?: ($appInfo->developerEmail ?? 'aiwerek.tech@gmail.com')) ?></span>
                        </div>
                        <a href="mailto:<?= esc($supportEmail ?: ($appInfo->developerEmail ?? 'aiwerek.tech@gmail.com')) ?>" class="btn btn-sm btn-primary">
                            <i data-lucide="mail" style="width: 14px; height: 14px;"></i> Kirim
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
