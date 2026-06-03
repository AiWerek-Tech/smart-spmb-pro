<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\GelombangModel;
use App\Models\JalurModel;
use App\Models\FaqModel;
use App\Services\AcademicYearService;
use App\Services\FeeService;

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
    protected AcademicYearService $academicYearService;
    protected FeeService $feeService;

    public function __construct()
    {
        $this->settingModel   = new SettingModel();
        $this->gelombangModel = new GelombangModel();
        $this->jalurModel     = new JalurModel();
        $this->faqModel       = new FaqModel();
        $this->academicYearService = new AcademicYearService($this->settingModel);
        $this->feeService = new FeeService();
    }

    /**
     * Tampilkan halaman informasi SPMB.
     * GET: /spmb
     *
     * Requirements: 3.1 – 3.5
     */
    public function index()
    {
        $academicYear = $this->academicYearService->activeYear();

        // Ambil jadwal per gelombang sesuai tahun pelajaran aktif (Req 3.2)
        $gelombang = $this->gelombangModel->getGelombangWithJalur($academicYear);

        // Ambil jalur beserta persyaratan (Req 3.3)
        $jalurs = $this->jalurModel->findAll();

        $feeSummary = $this->feeService->homepageSummary();

        // Ambil FAQ (Req 3.5)
        $faqs = $this->faqModel->getActiveFaqs();
        $faqs = $this->mergePaymentFaq($faqs, $feeSummary);

        // Ambil informasi alur (hardcoded atau dari settings)
        $alur = [
            ['step' => 1, 'title' => 'Verifikasi Data', 'desc' => 'Cek kelengkapan data pribadi dan dokumen'],
            ['step' => 2, 'title' => 'Seleksi Administrasi', 'desc' => 'Verifikasi dokumen oleh panitia'],
            ['step' => 3, 'title' => 'Seleksi Akademik', 'desc' => 'Ujian tertulis atau tes akademik'],
            ['step' => 4, 'title' => 'Pengumuman Hasil', 'desc' => 'Pengumuman hasil seleksi'],
        ];

        // Ambil data profil sekolah dari settings
        $schoolSettings = $this->settingModel->getSchoolProfile();

        return view('public/spmb', [
            'title'          => 'Informasi SPMB',
            'gelombang'      => $gelombang,
            'jalurs'         => $jalurs,
            'alur'           => $alur,
            'faqs'           => $faqs,
            'schoolSettings' => $schoolSettings,
            'fees'           => $feeSummary['fees'],
            'feeSummary'     => $feeSummary,
            'academicYear'   => $academicYear,
        ]);
    }

    private function mergePaymentFaq(array $faqs, array $feeSummary): array
    {
        $paymentFaq = [
            'question' => 'Apakah pendaftaran ini dikenakan biaya?',
            'answer'   => $feeSummary['payment_faq_answer'],
        ];

        $filtered = array_values(array_filter($faqs, static function (array $faq): bool {
            $question = strtolower((string) ($faq['question'] ?? ''));
            return !str_contains($question, 'biaya') && !str_contains($question, 'pembayaran');
        }));

        array_unshift($filtered, $paymentFaq);

        return $filtered;
    }
}
