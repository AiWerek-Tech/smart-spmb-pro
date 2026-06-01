<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\GelombangModel;
use App\Models\JalurModel;
use App\Models\FaqModel;

/**
 * SpmbController — Halaman informasi SPMB (jadwal, persyaratan, alur, FAQ).
 *
 * Methods:
 * - index() — Tampilkan halaman informasi SPMB
 *
 * Requirements: 3.1, 3.2, 3.3, 3.4, 3.5
 */
class SpmbController extends BaseController
{
    protected SettingModel $settingModel;
    protected GelombangModel $gelombangModel;
    protected JalurModel $jalurModel;
    protected FaqModel $faqModel;

    public function __construct()
    {
        $this->settingModel   = new SettingModel();
        $this->gelombangModel = new GelombangModel();
        $this->jalurModel     = new JalurModel();
        $this->faqModel       = new FaqModel();
    }

    /**
     * Tampilkan halaman informasi SPMB.
     * GET: /spmb
     *
     * Requirements: 3.1 – 3.5
     */
    public function index()
    {
        // Ambil jadwal per gelombang (Req 3.2)
        $gelombang = $this->gelombangModel->findAll();

        // Ambil jalur beserta persyaratan (Req 3.3)
        $jalurs = $this->jalurModel->findAll();

        // Ambil FAQ (Req 3.5)
        $faqs = $this->faqModel->getActiveFaqs();

        // Ambil informasi alur (hardcoded atau dari settings)
        $alur = [
            ['step' => 1, 'title' => 'Verifikasi Data', 'desc' => 'Cek kelengkapan data pribadi dan dokumen'],
            ['step' => 2, 'title' => 'Seleksi Administrasi', 'desc' => 'Verifikasi dokumen oleh panitia'],
            ['step' => 3, 'title' => 'Seleksi Akademik', 'desc' => 'Ujian tertulis atau tes akademik'],
            ['step' => 4, 'title' => 'Pengumuman Hasil', 'desc' => 'Pengumuman hasil seleksi'],
        ];

        // Ambil data profil sekolah dari settings
        $schoolSettings = $this->settingModel->getSchoolProfile();

        // Data Informasi Biaya SPMB (Integrasi MVC dinamis)
        $fees = [
            [
                'name' => 'Biaya Pendaftaran & Formulir',
                'amount' => 'Rp 250.000',
                'period' => 'Satu Kali',
                'desc' => 'Mencakup formulir online, verifikasi berkas, kartu ujian seleksi, dan biaya administrasi pendaftaran.',
                'icon' => 'file-text'
            ],
            [
                'name' => 'Sumbangan Pengembangan (Uang Pangkal)',
                'amount' => 'Rp 3.500.000',
                'period' => 'Satu Kali',
                'desc' => 'Mencakup 5 stel seragam lengkap, paket buku pelajaran, asuransi siswa, pemeliharaan laboratorium komputer/bahasa, perpustakaan, dan fasilitas sekolah.',
                'icon' => 'building-2'
            ],
            [
                'name' => 'Uang Sekolah Bulanan (SPP)',
                'amount' => 'Rp 750.000',
                'period' => 'Bulanan',
                'desc' => 'Mencakup biaya operasional belajar mengajar, ekstrakurikuler wajib dan pilihan, evaluasi berkala, e-learning, dan kegiatan OSIS.',
                'icon' => 'credit-card'
            ]
        ];

        return view('public/spmb', [
            'title'          => 'Informasi SPMB',
            'gelombang'      => $gelombang,
            'jalurs'         => $jalurs,
            'alur'           => $alur,
            'faqs'           => $faqs,
            'schoolSettings' => $schoolSettings,
            'fees'           => $fees,
        ]);
    }
}
