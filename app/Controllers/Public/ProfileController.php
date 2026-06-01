<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\GalleryModel;

/**
 * ProfileController — Halaman profil sekolah.
 *
 * Methods:
 * - index() — Tampilkan halaman profil sekolah
 *
 * Requirements: 2.1, 2.2, 2.3, 2.4, 2.5
 */
class ProfileController extends BaseController
{
    protected SettingModel $settingModel;
    protected GalleryModel $galleryModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->galleryModel = new GalleryModel();
    }

    /**
     * Tampilkan halaman profil sekolah.
     * GET: /profil
     *
     * Requirements: 2.1 – 2.5
     */
    public function index()
    {
        // Ambil data profil sekolah dari settings
        $schoolName     = $this->settingModel->getValue('school_name') ?? 'Nama Sekolah';
        $schoolHistory  = $this->settingModel->getValue('school_history') ?? 'Data belum tersedia';
        $schoolVision   = $this->settingModel->getValue('school_vision') ?? 'Data belum tersedia';
        $schoolMission  = $this->settingModel->getValue('school_mission') ?? 'Data belum tersedia';
        $schoolAccred   = $this->settingModel->getValue('school_accreditation') ?? 'Data belum tersedia';
        $schoolAccredYear = $this->settingModel->getValue('school_accreditation_year') ?? '2024';
        $facilities     = $this->settingModel->getValue('school_facilities') ?? 'Perpustakaan, Lab Komputer, Lab Sains, Lapangan Olahraga';

        // Parse facilities (comma-separated string to array)
        $facilitiesArray = array_map('trim', explode(',', $facilities));

        // Ambil galeri aktif dari database
        $gallery = $this->galleryModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();

        // Daftar Tenaga Pendidik / Guru (Fallback dinamis terintegrasi)
        $teachers = [
            [
                'name' => 'Budi Santoso, M.Pd.',
                'role' => 'Kepala Sekolah',
                'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Siti Aminah, S.Pd.',
                'role' => 'Wakil Kepala Sekolah & Kurikulum',
                'photo' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Dr. Ahmad Fauzi, M.Si.',
                'role' => 'Guru IPA / Laboran',
                'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=300&auto=format&fit=crop'
            ],
            [
                'name' => 'Rina Wijaya, S.S.',
                'role' => 'Guru Bahasa Inggris',
                'photo' => 'https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?q=80&w=300&auto=format&fit=crop'
            ],
        ];

        return view('public/profile', [
            'title'          => 'Profil Sekolah',
            'schoolName'     => $schoolName,
            'schoolHistory'  => $schoolHistory,
            'schoolVision'   => $schoolVision,
            'schoolMission'  => $schoolMission,
            'schoolAccred'   => $schoolAccred,
            'schoolAccredYear' => $schoolAccredYear,
            'facilities'     => $facilitiesArray,
            'gallery'        => $gallery,
            'teachers'       => $teachers,
        ]);
    }
}
