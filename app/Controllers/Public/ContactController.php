<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\SettingModel;

/**
 * ContactController — Halaman kontak dengan informasi sekolah dan Google Maps.
 *
 * Methods:
 * - index() — Tampilkan halaman kontak
 *
 * Requirements: 5.1, 5.2, 5.3
 */
class ContactController extends BaseController
{
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    /**
     * Tampilkan halaman kontak.
     * GET: /kontak
     *
     * Requirements: 5.1 – 5.3
     */
    public function index()
    {
        // Ambil data kontak dari settings
        $schoolName       = $this->settingModel->getValue('school_name', 'Nama Sekolah');
        $schoolAddress    = $this->settingModel->getValue('address', 'Alamat sekolah belum dikonfigurasi');
        $schoolPhone      = $this->settingModel->getValue('phone', 'Kontak belum dikonfigurasi');
        $schoolEmail      = $this->settingModel->getValue('email', 'Email belum dikonfigurasi');
        $schoolWhatsapp   = $this->settingModel->getValue('whatsapp', '');
        $schoolMapsUrl    = $this->resolveMapsEmbedUrl();

        return view('public/contact', [
            'title'          => 'Kontak Kami',
            'schoolName'     => $schoolName,
            'schoolAddress'  => $schoolAddress,
            'schoolPhone'    => $schoolPhone,
            'schoolEmail'    => $schoolEmail,
            'schoolWhatsapp' => $schoolWhatsapp,
            'schoolMapsUrl'  => $schoolMapsUrl,
        ]);
    }

    /**
     * Resolve Google Maps embed URL from admin settings (maps_embed iframe HTML or direct URL).
     */
    private function resolveMapsEmbedUrl(): string
    {
        $default = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613507864!3d-6.194741395493371!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f421a106f2d7%3A0x6e87355f3089d8f6!2sGrand%20Indonesia!5e0!3m2!1sen!2sid!4v1647417551065!5m2!1sen!2sid';

        $lat = trim((string) $this->settingModel->getValue('maps_lat', ''));
        $lng = trim((string) $this->settingModel->getValue('maps_lng', ''));
        $zoom = (int) $this->settingModel->getValue('maps_zoom', '16');
        $zoom = min(20, max(1, $zoom ?: 16));

        if ($lat !== '' && $lng !== '') {
            return 'https://maps.google.com/maps?q=' . rawurlencode("{$lat},{$lng}") . '&z=' . $zoom . '&output=embed';
        }

        $query = trim((string) $this->settingModel->getValue('maps_query', ''));
        if ($query !== '') {
            return 'https://maps.google.com/maps?q=' . rawurlencode($query) . '&z=' . $zoom . '&output=embed';
        }

        $mapsEmbed = trim((string) $this->settingModel->getValue('maps_embed', ''));
        if ($mapsEmbed !== '') {
            if (preg_match('/src=["\']([^"\']+)["\']/i', $mapsEmbed, $matches)) {
                $url = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                if (str_contains($url, '/maps/embed')) {
                    return $url;
                }
            }
            if (str_starts_with($mapsEmbed, 'http') && str_contains($mapsEmbed, '/maps/embed')) {
                return $mapsEmbed;
            }
        }

        foreach (['maps_embed_url', 'maps_url'] as $key) {
            $url = trim($this->settingModel->getValue($key, ''));
            if ($url !== '' && str_contains($url, '/maps/embed')) {
                return $url;
            }
        }

        return $default;
    }
}
