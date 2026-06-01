<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ActivityLogFilter — Middleware untuk mencatat log aktivitas kritis.
 *
 * Mencatat aktivitas berikut ke tabel activity_logs:
 * - Login (berhasil/gagal)
 * - Logout
 * - Perubahan data pendaftar
 * - Verifikasi/penolakan dokumen
 * - Ekspor data (Excel, PDF F-PD, Kartu Peserta)
 * - Backup dan restore database
 * - Perubahan pengaturan sistem
 * - Perubahan status seleksi
 *
 * Sesuai Requirement 17.4, 25.6.
 */
class ActivityLogFilter implements FilterInterface
{
    /**
     * Pola URL dan action yang dicatat (POST requests).
     * Format: 'pola_uri' => ['action' => 'nama_aksi', 'entity_type' => 'tipe_entitas']
     */
    private array $postPatterns = [
        'auth/login'                  => ['action' => 'login_attempt',       'entity_type' => null],
        'operator/registrants/update' => ['action' => 'edit_registrant',     'entity_type' => 'registrations'],
        'operator/documents/verify'   => ['action' => 'verify_document',     'entity_type' => 'student_documents'],
        'operator/export'             => ['action' => 'export_data',         'entity_type' => null],
        'admin/backup/download'       => ['action' => 'backup_create',       'entity_type' => null],
        'admin/backup/restore'        => ['action' => 'backup_restore',      'entity_type' => null],
        'admin/settings/save'         => ['action' => 'update_settings',     'entity_type' => 'settings'],
        'admin/seleksi/update'        => ['action' => 'update_seleksi',      'entity_type' => 'registrations'],
        'admin/announcements/publish' => ['action' => 'publish_announcement','entity_type' => 'announcements'],
        'pendaftar/daftar/submit'     => ['action' => 'submit_registration', 'entity_type' => 'registrations'],
        'pendaftar/dokumen/upload'    => ['action' => 'upload_document',     'entity_type' => 'student_documents'],
    ];

    /**
     * Pola URL dan action yang dicatat (GET requests).
     * Digunakan untuk logout dan ekspor file.
     */
    private array $getPatterns = [
        'auth/logout'              => ['action' => 'logout',       'entity_type' => null],
        'operator/export/excel'    => ['action' => 'export_excel', 'entity_type' => null],
        'operator/export/fpd'      => ['action' => 'export_fpd',   'entity_type' => 'students'],
        'pendaftar/cetak/bukti'    => ['action' => 'cetak_bukti',  'entity_type' => null],
        'pendaftar/cetak/kartu'    => ['action' => 'cetak_kartu',  'entity_type' => null],
    ];

    /**
     * Before filter — tidak ada tindakan sebelum request.
     * Logging dilakukan di after filter agar status response tersedia.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Logging dilakukan di after filter
        return null;
    }

    /**
     * After filter — catat aktivitas setelah request diproses.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $method = strtolower($request->getMethod());
        $uri    = ltrim($request->getUri()->getPath(), '/');

        // Tentukan action berdasarkan method dan URI
        $logInfo = null;

        if ($method === 'post') {
            $logInfo = $this->matchPattern($uri, $this->postPatterns);
        } elseif ($method === 'get') {
            $logInfo = $this->matchPattern($uri, $this->getPatterns);
        }

        // Tidak ada pola yang cocok, tidak perlu log
        if ($logInfo === null) {
            return null;
        }

        // Ambil entity_id dari URI jika ada (angka di akhir URI)
        $entityId = $this->extractEntityId($uri);

        // Ambil user_id dari sesi (bisa null untuk login attempt yang gagal)
        $userId = session()->get('user_id');

        // Ambil user agent dengan aman
        $userAgent = '';
        try {
            $agentString = $request->getUserAgent()->getAgentString();
            $userAgent   = substr((string) $agentString, 0, 500);
        } catch (\Throwable $e) {
            $userAgent = '';
        }

        try {
            $db = db_connect();
            $db->table('activity_logs')->insert([
                'user_id'     => $userId ?: null,
                'action'      => $logInfo['action'],
                'entity_type' => $logInfo['entity_type'],
                'entity_id'   => $entityId,
                'ip_address'  => $request->getIPAddress(),
                'user_agent'  => $userAgent,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Catat ke log file CI4 tanpa mengganggu response
            log_message('error', 'ActivityLogFilter: Gagal mencatat log aktivitas - ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Cocokkan URI dengan pola yang terdaftar.
     *
     * @param string $uri      URI yang sedang diproses
     * @param array  $patterns Daftar pola yang akan dicocokkan
     *
     * @return array|null Array ['action' => ..., 'entity_type' => ...] atau null jika tidak cocok
     */
    private function matchPattern(string $uri, array $patterns): ?array
    {
        foreach ($patterns as $pattern => $info) {
            if (str_contains($uri, $pattern)) {
                return $info;
            }
        }

        return null;
    }

    /**
     * Ekstrak entity_id dari URI (angka di segmen terakhir URI).
     *
     * Contoh: 'operator/registrants/update/42' → 42
     *         'operator/documents/verify/15'   → 15
     *
     * @param string $uri
     *
     * @return int|null
     */
    private function extractEntityId(string $uri): ?int
    {
        $segments = explode('/', rtrim($uri, '/'));
        $last     = end($segments);

        if (ctype_digit($last)) {
            return (int) $last;
        }

        return null;
    }
}
