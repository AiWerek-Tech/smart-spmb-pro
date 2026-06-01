<?php

namespace Config;

use App\Filters\ActivityLogFilter;
use App\Filters\AuthFilter;
use App\Filters\CsrfFilter;
use App\Filters\RoleFilter;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Alias filter untuk mempermudah pembacaan di Routes.php.
     *
     * - auth        : Cek sesi aktif; redirect ke login jika tidak ada sesi
     * - role        : Cek peran pengguna dari sesi; HTTP 403 jika tidak sesuai
     * - csrfcheck   : Validasi token CSRF pada request POST; HTTP 403 jika tidak valid
     * - activitylog : Catat log aktivitas kritis ke tabel activity_logs
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        // Filter kustom aplikasi
        'auth'          => AuthFilter::class,
        'role'          => RoleFilter::class,
        'csrfcheck'     => CsrfFilter::class,
        'activitylog'   => ActivityLogFilter::class,
    ];

    /**
     * Filter yang selalu dijalankan sebelum dan sesudah setiap request.
     *
     * - csrf     : Perlindungan CSRF bawaan CI4 untuk semua request POST
     * - toolbar  : Debug toolbar (hanya aktif di environment development)
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf',
            // 'invalidchars',
        ],
        'after' => [
            // DebugToolbar is intentionally not injected into application pages.
            // It pollutes the DOM/accessibility tree and exposes internal state.
            // Enable it locally only when actively debugging a backend issue.
            // 'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * Filter berdasarkan HTTP method.
     * Tidak digunakan saat ini; autentikasi dan CSRF ditangani per-route.
     */
    public array $methods = [];

    /**
     * Filter berdasarkan pola URI.
     *
     * - auth        : Wajib login untuk mengakses area dashboard
     * - activitylog : Catat log aktivitas untuk area dashboard dan autentikasi
     *
     * Catatan: RoleFilter (role) tidak didaftarkan di sini karena memerlukan
     * argumen peran yang berbeda per grup route. RoleFilter didaftarkan
     * langsung di Routes.php menggunakan sintaks 'role:admin', 'role:operator,admin',
     * atau 'role:pendaftar'.
     */
    public array $filters = [
        'auth' => [
            'before' => [
                'admin/*',
                'operator/*',
                'pendaftar/*',
            ],
        ],
        'activitylog' => [
            'after' => [
                'admin/*',
                'operator/*',
                'pendaftar/*',
                'auth/login',
                'auth/logout',
            ],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        
        if (ENVIRONMENT === 'testing') {
            $key = array_search('csrf', $this->globals['before'], true);
            if ($key !== false) {
                unset($this->globals['before'][$key]);
                $this->globals['before'] = array_values($this->globals['before']);
            }
        }
    }
}
