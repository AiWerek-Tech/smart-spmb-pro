<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingController extends BaseController
{
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    /**
     * Tampilkan halaman konfigurasi sistem.
     */
    public function index()
    {
        $settings = $this->settingModel->getAllAsArray();

        // Defaults if keys do not exist yet
        $defaults = [
            'school_name'        => 'SMA Smart SPMB Pro',
            'school_logo'        => '',
            'academic_year'      => '2026/2027',
            'phone'              => '(021) 1234-5678',
            'email'              => 'info@sekolah.sch.id',
            'address'            => 'Jl. Raya Pendidikan No. 45, Jakarta',
            'whatsapp'           => '081234567890',
            'maps_embed'         => '',
            'accreditation'      => 'A',
            'accreditation_year' => '2025',
            'npsn'               => '',
            'school_description' => 'Membentuk generasi cerdas, berkarakter, dan siap menghadapi tantangan global.',
            'vision'             => '',
            'mission'            => '',
            'history'               => '',
            'tagline'               => 'Membentuk Generasi Cerdas dan Berkarakter Mulia',
            'theme_color'           => 'purple',
            'theme_color_operator'  => 'navy',
            'theme_color_pendaftar' => 'lightblue',
            'app_version'           => config('AppInfo')->version,
            'developer_name'        => config('AppInfo')->developer,
            'developer_phone'       => config('AppInfo')->developerPhone,
            'developer_email'       => config('AppInfo')->developerEmail,
        ];

        $settings = array_merge($defaults, $settings);

        $data = [
            'title'    => 'Konfigurasi Sistem',
            'settings' => $settings,
        ];

        return view('admin/settings/index', $data);
    }

    /**
     * Simpan pembaruan konfigurasi sistem.
     */
    public function save()
    {
        $rules = [
            'school_name'   => 'required|max_length[150]',
            'academic_year' => 'required|max_length[9]',
            'email'         => 'required|valid_email',
            'phone'         => 'required',
            'address'       => 'required',
            'theme_color'           => 'required|in_list[purple,navy,lightblue,emerald,red,orange,rose]',
            'theme_color_operator'  => 'required|in_list[purple,navy,lightblue,emerald,red,orange,rose]',
            'theme_color_pendaftar' => 'required|in_list[purple,navy,lightblue,emerald,red,orange,rose]',
        ];

        // Validasi opsional untuk logo sekolah jika ada unggahan baru
        $logoFile = $this->request->getFile('school_logo_file');
        if ($logoFile !== null && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $rules['school_logo_file'] = 'is_image[school_logo_file]|max_size[school_logo_file,2048]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $settingsData = [
            'school_name'        => $this->request->getPost('school_name'),
            'academic_year'      => $this->request->getPost('academic_year'),
            'phone'              => $this->request->getPost('phone'),
            'email'              => $this->request->getPost('email'),
            'address'            => $this->request->getPost('address'),
            'whatsapp'           => $this->request->getPost('whatsapp'),
            'maps_embed'         => $this->request->getPost('maps_embed'),
            'accreditation'      => $this->request->getPost('accreditation'),
            'accreditation_year' => $this->request->getPost('accreditation_year'),
            'npsn'               => $this->request->getPost('npsn'),
            'school_description' => $this->request->getPost('school_description'),
            'tagline'               => $this->request->getPost('tagline'),
            'theme_color'           => $this->request->getPost('theme_color'),
            'theme_color_operator'  => $this->request->getPost('theme_color_operator'),
            'theme_color_pendaftar' => $this->request->getPost('theme_color_pendaftar'),
        ];

        // Proses unggah logo sekolah jika valid
        if ($logoFile !== null && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $newName = $logoFile->getRandomName();
            // Pindahkan ke public/uploads/images/
            if ($logoFile->move(FCPATH . 'uploads/images/', $newName)) {
                $settingsData['school_logo'] = 'uploads/images/' . $newName;
            }
        }

        if (!$this->settingModel->setMultiple($settingsData)) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pengaturan.');
        }

        return redirect()->to('admin/settings')->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
