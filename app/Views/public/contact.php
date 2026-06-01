<?php
/**
 * @var string $schoolAddress
 * @var string $schoolPhone
 * @var string $schoolEmail
 * @var string $schoolWhatsapp
 * @var string $schoolMapsUrl
 */
?>
<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- Header Section -->
<div class="sp-page-header">
    <div class="container animate-fade-up">
        <h1 class="fw-bold">
            <i data-lucide="phone" style="width:28px;height:28px;" class="me-2"></i>
            Hubungi Kami
        </h1>
        <p class="opacity-90">Kami siap membantu menjawab pertanyaan Anda</p>
    </div>
</div>

<!-- Contact Section -->
<section class="sp-section">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-6 mb-4 animate-fade-up">
                <h2 class="sp-section-title text-start ms-0 mb-4">Informasi Kontak</h2>
                <p class="text-muted mb-5">Silakan hubungi kami melalui saluran komunikasi berikut untuk informasi lebih lanjut mengenai pendaftaran siswa baru.</p>

                <!-- Address -->
                <div class="glass-panel p-4 rounded-4 hover-lift border-0 shadow-sm mb-4 d-flex align-items-start gap-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                        <i data-lucide="map-pin" style="width:24px;height:24px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Alamat</h5>
                        <p class="text-muted mb-0 small" style="line-height: 1.7;"><?= esc($schoolAddress ?? 'Alamat sekolah belum dikonfigurasi') ?></p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-sm-6">
                        <!-- Phone -->
                        <div class="glass-panel p-4 rounded-4 hover-lift border-0 shadow-sm h-100 d-flex flex-column align-items-start gap-3">
                            <div class="bg-info bg-opacity-10 text-info p-3 rounded-3">
                                <i data-lucide="phone" style="width:24px;height:24px;"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 small">Telepon</h5>
                                <p class="mb-0 fw-bold">
                                    <a href="tel:<?= str_replace([' ', '(', ')', '-'], '', $schoolPhone ?? '') ?>" class="text-dark">
                                        <?= esc($schoolPhone ?? 'Kontak belum dikonfigurasi') ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <!-- Email -->
                        <div class="glass-panel p-4 rounded-4 hover-lift border-0 shadow-sm h-100 d-flex flex-column align-items-start gap-3">
                            <div class="bg-accent bg-opacity-10 text-accent p-3 rounded-3" style="color: var(--sp-accent) !important;">
                                <i data-lucide="mail" style="width:24px;height:24px;"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 small">Email</h5>
                                <p class="mb-0 fw-bold" style="font-size: 0.9rem;">
                                    <a href="mailto:<?= esc($schoolEmail ?? '') ?>" class="text-dark">
                                        <?= esc($schoolEmail ?? 'Email belum dikonfigurasi') ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp -->
                <div class="glass-panel p-4 rounded-4 hover-lift border-0 shadow-sm mt-4 d-flex align-items-center justify-content-between gap-3 bg-success bg-opacity-5 border-success border-opacity-10">
                    <div class="d-flex align-items-center gap-4">
                        <div class="bg-success text-white p-3 rounded-3 shadow-sm">
                            <i data-lucide="message-circle" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">WhatsApp</h5>
                            <p class="text-muted small mb-0">Respon cepat via Chat</p>
                        </div>
                    </div>
                    <a href="https://wa.me/<?= str_replace(['+', ' '], '', $schoolWhatsapp ?? '') ?>" class="btn btn-success fw-bold px-4 py-2 rounded-3 shadow-sm" target="_blank">
                        Hubungi
                    </a>
                </div>
            </div>

            <!-- Google Maps Embed -->
            <div class="col-lg-6 mb-4 animate-fade-up delay-1">
                <h2 class="sp-section-title text-start ms-0 mb-4">Lokasi Kami</h2>
                <div class="rounded-4 overflow-hidden shadow-lg border border-white border-opacity-20" style="height: 480px;">
                    <iframe 
                        src="<?= esc($schoolMapsUrl ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613507864!3d-6.194741395493371!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f421a106f2d7%3A0x6e87355f3089d8f6!2sGrand%20Indonesia!5e0!3m2!1sen!2sid!4v1647417551065!5m2!1sen!2sid') ?>" 
                        width="100%" 
                        height="100%" 
                        style="border:0;"
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi sekolah di Google Maps">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="sp-section-alt">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 animate-fade-up">
                <div class="text-center mb-5">
                    <h2 class="sp-section-title">Kirim Pesan</h2>
                    <p class="text-muted">Punya pertanyaan spesifik? Kirimkan pesan Anda dan tim kami akan segera menghubungi Anda.</p>
                </div>
                <div class="glass-panel p-4 p-lg-5 rounded-4 shadow-sm border-0">
                    <form method="POST" action="#" id="contactForm">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold small text-muted text-uppercase">Nama Lengkap</label>
                                <input type="text" class="form-control bg-light border-0 py-3 px-4 rounded-3" id="name" name="name" placeholder="Masukkan nama Anda" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold small text-muted text-uppercase">Alamat Email</label>
                                <input type="email" class="form-control bg-light border-0 py-3 px-4 rounded-3" id="email" name="email" placeholder="email@contoh.com" required>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label fw-bold small text-muted text-uppercase">Subjek Pesan</label>
                                <input type="text" class="form-control bg-light border-0 py-3 px-4 rounded-3" id="subject" name="subject" placeholder="Apa yang ingin Anda tanyakan?" required>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label fw-bold small text-muted text-uppercase">Pesan</label>
                                <textarea class="form-control bg-light border-0 py-3 px-4 rounded-3" id="message" name="message" rows="5" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                            </div>
                            <div class="col-12 text-center mt-5">
                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-3 fw-bold shadow-sm">
                                    <i data-lucide="send" style="width:20px;height:20px;" class="me-2"></i>
                                    Kirim Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Support Section -->
<section class="sp-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4 animate-fade-up">
                <div class="glass-panel p-4 rounded-4 text-center hover-lift border-0 shadow-sm h-100">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-flex mb-4">
                        <i data-lucide="clock" style="width:32px;height:32px;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Jam Operasional</h5>
                    <p class="text-muted small mb-0">Senin - Jumat<br><span class="fw-bold text-dark">07:30 - 15:30 WIB</span></p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-up delay-1">
                <div class="glass-panel p-4 rounded-4 text-center hover-lift border-0 shadow-sm h-100">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle d-inline-flex mb-4">
                        <i data-lucide="calendar-off" style="width:32px;height:32px;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Hari Libur</h5>
                    <p class="text-muted small mb-0">Sabtu, Minggu<br>&amp; Hari Besar Nasional</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-up delay-2">
                <div class="glass-panel p-4 rounded-4 text-center hover-lift border-0 shadow-sm h-100">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle d-inline-flex mb-4">
                        <i data-lucide="headset" style="width:32px;height:32px;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Respon Cepat</h5>
                    <p class="text-muted small mb-0">Tim kami akan merespon pesan Anda dalam<br><span class="fw-bold text-dark">maksimal 24 jam kerja</span></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
