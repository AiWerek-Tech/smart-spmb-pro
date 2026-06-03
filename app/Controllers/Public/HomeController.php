<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\JalurModel;
use App\Models\RegistrationModel;
use App\Models\AnnouncementModel;
use App\Models\FaqModel;
use App\Models\BannerModel;
use App\Models\TestimonialModel;
use App\Models\GalleryModel;
use App\Models\StatisticModel;
use App\Models\GelombangModel;
use App\Services\AcademicYearService;
use App\Services\FeeService;

/**
 * HomeController — Halaman beranda/homepage publik.
 */
class HomeController extends BaseController
{
    protected SettingModel $settingModel;
    protected JalurModel $jalurModel;
    protected RegistrationModel $registrationModel;
    protected AnnouncementModel $announcementModel;
    protected FaqModel $faqModel;
    protected BannerModel $bannerModel;
    protected TestimonialModel $testimonialModel;
    protected GalleryModel $galleryModel;
    protected StatisticModel $statisticModel;
    protected GelombangModel $gelombangModel;
    protected AcademicYearService $academicYearService;
    protected FeeService $feeService;

    public function __construct()
    {
        $this->settingModel       = new SettingModel();
        $this->jalurModel         = new JalurModel();
        $this->registrationModel  = new RegistrationModel();
        $this->announcementModel  = new AnnouncementModel();
        $this->faqModel           = new FaqModel();
        $this->bannerModel        = new BannerModel();
        $this->testimonialModel   = new TestimonialModel();
        $this->galleryModel       = new GalleryModel();
        $this->statisticModel     = new StatisticModel();
        $this->gelombangModel     = new GelombangModel();
        $this->academicYearService = new AcademicYearService();
        $this->feeService          = new FeeService();
    }

    public function index()
    {
        // 1. Ambil data pengaturan sekolah
        $schoolSettings = $this->settingModel->getSchoolProfile();
        
        $schoolName      = $schoolSettings['school_name'] ?? 'SMP Nusantara Mandiri';
        $schoolTagline   = $schoolSettings['tagline'] ?? 'Sekolah Berkarakter & Berprestasi';
        $academicYear    = $this->academicYearService->activeYear();
        $schoolPhone     = $schoolSettings['phone'] ?? '081234567890';
        $schoolEmail     = $schoolSettings['email'] ?? 'info@smpnusantara.sch.id';
        $schoolWhatsapp  = $schoolSettings['whatsapp'] ?? $schoolPhone;

        // 2. Ambil Banners (Hero Section)
        $banners = $this->bannerModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        
        // 3. Ambil Jalur Penerimaan dengan jumlah pendaftar sekaligus (hindari N+1 query)
        $jalurs = $this->jalurModel->getJalurWithRegistrantCount($academicYear);
        // Filter hanya yang aktif dan urutkan
        $jalurs = array_filter($jalurs, fn($j) => $j['is_active'] == 1);
        usort($jalurs, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
        $jalurs = array_values($jalurs);
        
        // 4. Ambil Statistik. Nilai inti dihitung dari database, metadata tetap bisa diatur admin.
        $manualStats = $this->statisticModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        
        $totalRegistrations = $this->registrationModel
            ->where('academic_year', $academicYear)
            ->whereNotIn('status', ['draft'])
            ->countAllResults();
        $totalAccepted = $this->registrationModel
            ->where('academic_year', $academicYear)
            ->where('status', 'accepted')
            ->countAllResults();
        $totalVerified = $this->registrationModel
            ->where('academic_year', $academicYear)
            ->whereIn('status', ['verified', 'accepted'])
            ->countAllResults();

        $stats = $this->buildHomepageStats(
            $manualStats,
            $totalRegistrations,
            $totalVerified,
            $totalAccepted,
            (string) ($schoolSettings['accreditation'] ?? 'A')
        );

        // 5. Per-jalur stats calculation (data sudah ada dari JOIN, tidak perlu query tambahan)
        foreach ($jalurs as &$jalur) {
            $count = (int) ($jalur['registrant_count'] ?? 0);
            $quota = (int) ($jalur['quota'] ?? 0);
            $jalur['total_registrations'] = $count;
            $jalur['remaining_quota']     = max(0, $quota - $count);
            $jalur['percentage_filled']   = $quota > 0 ? min(100, round(($count / $quota) * 100)) : 0;
        }
        unset($jalur); // break reference

        // 6. Ambil Pengumuman/Berita
        $announcements = $this->announcementModel->where('status', 'published')->orderBy('published_at', 'DESC')->limit(3)->findAll();

        $feeSummary = $this->feeService->homepageSummary();

        // 7. Ambil FAQ
        $faqs = $this->faqModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->limit(5)->findAll();
        $faqs = $this->mergePaymentFaq($faqs, $feeSummary);

        // 8. Ambil Gallery
        $gallery = $this->galleryModel
            ->where('academic_year', $academicYear)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->limit(6)
            ->findAll();
        $campusTitle = $this->settingModel->getValue('campus_title', 'Lingkungan Belajar');
        $campusDescription = $this->settingModel->getValue('campus_description', 'Informasi lingkungan sekolah belum dikonfigurasi.');

        $spmbSchedule = $this->buildSpmbSchedule($academicYear);

        // 9. Ambil Testimonials (limit 6 untuk performa)
        $testimonials = $this->testimonialModel->where('is_active', 1)->orderBy('id', 'DESC')->limit(6)->findAll();

        // 10. CTA Logic
        $loggedIn = session()->has('user_id');
        $role     = session()->get('user_base_role') ?? session()->get('user_role') ?? 'pendaftar';
        $ctaUrl   = $loggedIn ? base_url($role . '/dashboard') : base_url('auth/register');
        $ctaText  = $loggedIn ? 'Dashboard Saya' : 'Mulai Pendaftaran';

        return view('public/home', [
            'title'              => 'Beranda',
            'schoolName'         => $schoolName,
            'schoolTagline'      => $schoolTagline,
            'academicYear'       => $academicYear,
            'schoolPhone'        => $schoolPhone,
            'schoolEmail'        => $schoolEmail,
            'schoolWhatsapp'     => $schoolWhatsapp,
            'schoolSettings'     => $schoolSettings,
            'banners'            => $banners,
            'jalurs'             => $jalurs,
            'stats'              => $stats,
            'totalRegistrations' => $totalRegistrations,
            'announcements'      => $announcements,
            'faqs'               => $faqs,
            'feeSummary'         => $feeSummary,
            'gallery'            => $gallery,
            'campusTitle'        => $campusTitle,
            'campusDescription'  => $campusDescription,
            'spmbSchedule'       => $spmbSchedule,
            'testimonials'       => $testimonials,
            'ctaUrl'             => $ctaUrl,
            'ctaText'            => $ctaText,
            'noActiveJalurs'     => empty($jalurs)
        ]);
    }

    public function stats()
    {
        $totalRegistrations = $this->registrationModel
            ->where('academic_year', $this->academicYearService->activeYear())
            ->whereNotIn('status', ['draft'])
            ->countAllResults();
        return $this->response->setJSON([
            'total_registrations' => $totalRegistrations,
            'status'              => 'success'
        ]);
    }

    private function buildHomepageStats(array $manualStats, int $totalRegistrations, int $totalVerified, int $totalAccepted, string $accreditation): array
    {
        $verifiedRate = $totalRegistrations > 0 ? (int) round(($totalVerified / $totalRegistrations) * 100) : 0;
        $computed = [
            'pendaftar' => [
                'label'       => 'Pendaftar',
                'value'       => number_format($totalRegistrations, 0, ',', '.'),
                'icon'        => 'users',
                'description' => 'Total non-draft',
            ],
            'terverifikasi' => [
                'label'       => 'Terverifikasi',
                'value'       => $verifiedRate . '%',
                'icon'        => 'check-circle',
                'description' => number_format($totalVerified, 0, ',', '.') . ' berkas valid',
            ],
            'diterima' => [
                'label'       => 'Diterima',
                'value'       => number_format($totalAccepted, 0, ',', '.'),
                'icon'        => 'user-check',
                'description' => 'Lulus seleksi',
            ],
            'akreditasi' => [
                'label'       => 'Akreditasi',
                'value'       => $accreditation !== '' ? $accreditation : '-',
                'icon'        => 'award',
                'description' => 'Data profil sekolah',
            ],
        ];

        $manualByLabel = [];
        foreach ($manualStats as $stat) {
            $manualByLabel[strtolower(trim((string) ($stat['label'] ?? '')))] = $stat;
        }

        $stats = [];

        foreach ($computed as $key => $stat) {
            $manual = $manualByLabel[$key] ?? [];
            $stats[] = array_merge($manual, $stat, [
                'icon' => ($manual['icon'] ?? '') ?: $stat['icon'],
            ]);
        }

        foreach ($manualStats as $stat) {
            $key = strtolower(trim((string) ($stat['label'] ?? '')));
            if (!isset($computed[$key])) {
                $stats[] = $stat;
            }
        }

        return array_slice($stats, 0, 4);
    }

    private function buildSpmbSchedule(string $academicYear): array
    {
        $schedule = [];
        $gelombang = $this->gelombangModel->getGelombangWithJalur($academicYear);

        foreach ($gelombang as $item) {
            $schedule[] = [
                'title'       => $item['name'] . ' - ' . ($item['jalur_name'] ?? 'Jalur SPMB'),
                'date_range'  => $this->dateRange($item['open_date'] ?? null, $item['close_date'] ?? null),
                'description' => 'Masa pendaftaran calon siswa.',
                'icon'        => 'calendar-plus',
                'is_active'   => (int) ($item['is_active'] ?? 0) === 1,
            ];

            if (!empty($item['announcement_date'])) {
                $schedule[] = [
                    'title'       => 'Pengumuman ' . $item['name'],
                    'date_range'  => $this->formatDate($item['announcement_date']),
                    'description' => 'Hasil seleksi diumumkan sesuai gelombang.',
                    'icon'        => 'megaphone',
                    'is_active'   => (int) ($item['is_active'] ?? 0) === 1,
                ];
            }
        }

        $reRegistration = $this->dateRange(
            $this->settingModel->getValue('spmb_re_registration_start', ''),
            $this->settingModel->getValue('spmb_re_registration_end', '')
        );
        if ($reRegistration !== '-') {
            $schedule[] = [
                'title'       => 'Daftar Ulang',
                'date_range'  => $reRegistration,
                'description' => 'Khusus peserta yang dinyatakan lulus.',
                'icon'        => 'clipboard-check',
                'is_active'   => true,
            ];
        }

        $mpls = $this->dateRange(
            $this->settingModel->getValue('spmb_mpls_start', ''),
            $this->settingModel->getValue('spmb_mpls_end', '')
        );
        if ($mpls !== '-') {
            $schedule[] = [
                'title'       => 'MPLS',
                'date_range'  => $mpls,
                'description' => 'Masa Pengenalan Lingkungan Sekolah.',
                'icon'        => 'school',
                'is_active'   => true,
            ];
        }

        return $schedule;
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

        return array_slice($filtered, 0, 5);
    }

    private function dateRange(?string $start, ?string $end): string
    {
        $start = trim((string) $start);
        $end = trim((string) $end);

        if ($start === '' && $end === '') {
            return '-';
        }

        if ($start === $end || $end === '') {
            return $this->formatDate($start);
        }

        return $this->formatDate($start) . ' - ' . $this->formatDate($end);
    }

    private function formatDate(?string $date): string
    {
        if (!$date || strtotime($date) === false) {
            return '-';
        }

        return date('d M Y', strtotime($date));
    }
}
