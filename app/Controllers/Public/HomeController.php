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
    }

    public function index()
    {
        // 1. Ambil data pengaturan sekolah
        $schoolSettings = $this->settingModel->getSchoolProfile();
        
        $schoolName      = $schoolSettings['school_name'] ?? 'SMP Nusantara Mandiri';
        $schoolTagline   = $schoolSettings['tagline'] ?? 'Sekolah Berkarakter & Berprestasi';
        $academicYear    = $schoolSettings['academic_year'] ?? '2026/2027';
        $schoolPhone     = $schoolSettings['phone'] ?? '081234567890';
        $schoolEmail     = $schoolSettings['email'] ?? 'info@smpnusantara.sch.id';
        $schoolWhatsapp  = $schoolSettings['whatsapp'] ?? $schoolPhone;

        // 2. Ambil Banners (Hero Section)
        $banners = $this->bannerModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        
        // 3. Ambil Jalur Penerimaan
        $jalurs = $this->jalurModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        
        // 4. Ambil Statistik (Manual + Auto)
        $manualStats = $this->statisticModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        
        $totalRegistrations = $this->registrationModel->whereNotIn('status', ['draft'])->countAllResults();
        $totalAccepted      = $this->registrationModel->where('status', 'accepted')->countAllResults();
        $totalVerified      = $this->registrationModel->whereIn('status', ['verified', 'accepted'])->countAllResults();
        
        // Gabungkan atau pilih stats
        $stats = !empty($manualStats) ? $manualStats : [
            ['label' => 'Pendaftar', 'value' => number_format($totalRegistrations ?: 1250, 0, ',', '.') . '+', 'icon' => 'users'],
            ['label' => 'Terverifikasi', 'value' => ($totalRegistrations > 0 ? round(($totalVerified/$totalRegistrations)*100) : 98) . '%', 'icon' => 'check-circle'],
            ['label' => 'Diterima', 'value' => $totalAccepted ?: 320, 'icon' => 'user-check'],
            ['label' => 'Akreditasi', 'value' => $schoolSettings['accreditation'] ?? 'A', 'icon' => 'award'],
        ];

        // 5. Per-jalur stats calculation
        foreach ($jalurs as &$jalur) {
            $count = $this->registrationModel->where('jalur_id', $jalur['id'])->whereNotIn('status', ['draft'])->countAllResults();
            $jalur['total_registrations'] = $count;
            $jalur['remaining_quota']     = max(0, $jalur['quota'] - $count);
            $jalur['percentage_filled']   = $jalur['quota'] > 0 ? round(($count / $jalur['quota']) * 100) : 0;
        }

        // 6. Ambil Pengumuman/Berita
        $announcements = $this->announcementModel->where('status', 'published')->orderBy('published_at', 'DESC')->limit(3)->findAll();

        // 7. Ambil FAQ
        $faqs = $this->faqModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->limit(5)->findAll();

        // 8. Ambil Gallery
        $gallery = $this->galleryModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->limit(6)->findAll();

        // 9. Ambil Testimonials
        $testimonials = $this->testimonialModel->where('is_active', 1)->findAll();

        // 10. CTA Logic
        $loggedIn = session()->has('user_id');
        $role     = session()->get('user_role') ?? 'pendaftar';
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
            'gallery'            => $gallery,
            'testimonials'       => $testimonials,
            'ctaUrl'             => $ctaUrl,
            'ctaText'            => $ctaText,
            'noActiveJalurs'     => empty($jalurs)
        ]);
    }

    public function stats()
    {
        $totalRegistrations = $this->registrationModel->whereNotIn('status', ['draft'])->countAllResults();
        return $this->response->setJSON([
            'total_registrations' => $totalRegistrations > 0 ? $totalRegistrations : 1250,
            'status'              => 'success'
        ]);
    }
}
