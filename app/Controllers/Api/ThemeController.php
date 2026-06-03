<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use CodeIgniter\HTTP\ResponseInterface;

class ThemeController extends BaseController
{
    /**
     * Menyimpan warna tema pilihan peran saat ini via AJAX.
     * POST /api/theme/save
     */
    public function save(): ResponseInterface
    {
        $role = session()->get('user_base_role') ?? session()->get('user_role');
        if (empty($role)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesi pengguna tidak valid.',
            ])->setStatusCode(401);
        }

        $color = $this->request->getPost('color');
        $validThemes = ['purple', 'navy', 'lightblue', 'emerald', 'red', 'orange', 'rose'];

        if (!in_array($color, $validThemes, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilihan warna tema tidak valid.',
            ])->setStatusCode(400);
        }

        // Tentukan key setting berdasarkan role
        if ($role === 'admin') {
            $key = 'theme_color';
        } elseif ($role === 'operator') {
            $key = 'theme_color_operator';
        } else {
            $key = 'theme_color_pendaftar';
        }

        $settingModel = new SettingModel();
        if ($settingModel->setValue($key, $color)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Warna tema peran berhasil diperbarui secara permanen.',
                'role'    => $role,
                'color'   => $color,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menyimpan perubahan ke server.',
        ])->setStatusCode(500);
    }
}
