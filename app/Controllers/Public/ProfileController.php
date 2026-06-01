<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\GalleryModel;
use App\Models\TeacherModel;

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
    protected TeacherModel $teacherModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->galleryModel = new GalleryModel();
        $this->teacherModel = new TeacherModel();
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
        $schoolHistory  = $this->settingModel->getValue('history', 'Data belum tersedia');
        $schoolVision   = $this->settingModel->getValue('vision', 'Data belum tersedia');
        $schoolMission  = $this->settingModel->getValue('mission', 'Data belum tersedia');
        $schoolAccred   = $this->settingModel->getValue('accreditation', 'Data belum tersedia');
        $schoolAccredYear = $this->settingModel->getValue('accreditation_year', '2024');
        $schoolFoundedYear = $this->settingModel->getValue('school_founded_year', '');
        $facilities     = $this->settingModel->getValue('school_facilities', "Perpustakaan\nLab Komputer\nLab Sains\nLapangan Olahraga");

        // Parse facilities from newline or comma separated settings.
        $facilitiesArray = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', (string) $facilities))));

        // Ambil galeri aktif dari database
        $gallery = $this->galleryModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();

        $teachers = $this->teacherModel->activeOrdered();

        return view('public/profile', [
            'title'          => 'Profil Sekolah',
            'schoolName'     => $schoolName,
            'schoolHistory'  => $schoolHistory,
            'schoolVision'   => $schoolVision,
            'schoolMission'  => $schoolMission,
            'schoolAccred'   => $schoolAccred,
            'schoolAccredYear' => $schoolAccredYear,
            'schoolFoundedYear' => $schoolFoundedYear,
            'facilities'     => $facilitiesArray,
            'gallery'        => $gallery,
            'teachers'       => $teachers,
        ]);
    }
}
