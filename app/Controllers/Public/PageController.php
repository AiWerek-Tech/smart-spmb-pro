<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\GalleryModel;
use App\Models\SettingModel;

class PageController extends BaseController
{
    private SettingModel $settingModel;
    private GalleryModel $galleryModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->galleryModel = new GalleryModel();
    }

    public function privacy()
    {
        return view('public/legal', [
            'title' => 'Kebijakan Privasi',
            'heading' => 'Kebijakan Privasi',
            'icon' => 'shield-check',
            'content' => $this->settingModel->getValue('privacy_policy', 'Kebijakan privasi belum dikonfigurasi.'),
        ]);
    }

    public function terms()
    {
        return view('public/legal', [
            'title' => 'Syarat & Ketentuan',
            'heading' => 'Syarat & Ketentuan',
            'icon' => 'file-check-2',
            'content' => $this->settingModel->getValue('terms_conditions', 'Syarat dan ketentuan belum dikonfigurasi.'),
        ]);
    }

    public function campus()
    {
        $facilities = $this->settingModel->getValue('school_facilities', '');
        $facilityList = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|,/', (string) $facilities))));

        return view('public/campus', [
            'title' => 'Lingkungan & Kampus',
            'campusTitle' => $this->settingModel->getValue('campus_title', 'Lingkungan Belajar'),
            'campusDescription' => $this->settingModel->getValue('campus_description', 'Informasi lingkungan sekolah belum dikonfigurasi.'),
            'facilities' => $facilityList,
            'gallery' => $this->galleryModel
                ->where('is_active', 1)
                ->groupStart()
                    ->where('category', 'Lingkungan')
                    ->orWhere('category', 'Fasilitas')
                    ->orWhere('category', 'Kampus')
                ->groupEnd()
                ->orderBy('sort_order', 'ASC')
                ->limit(9)
                ->findAll(),
        ]);
    }
}
