<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CsrfFilter — Middleware untuk validasi token CSRF pada request POST.
 *
 * Menolak request POST tanpa token CSRF yang valid dengan HTTP 403.
 * Sesuai Requirement 6.9, 25.1.
 *
 * Catatan: CodeIgniter 4 sudah memiliki built-in CSRF protection via
 * filter 'csrf' di Filters.php globals. Filter kustom ini digunakan
 * sebagai lapisan tambahan untuk route-route tertentu yang memerlukan
 * validasi CSRF eksplisit.
 *
 * Cara penggunaan di Routes.php:
 *   'filter' => 'csrfcheck'
 */
class CsrfFilter implements FilterInterface
{
    /**
     * Validasi token CSRF pada setiap request POST.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Hanya periksa request POST
        if (strtolower($request->getMethod()) !== 'post') {
            return null;
        }

        $security  = service('security');
        $tokenName = $security->getTokenName();

        // Cek token di body POST atau header X-CSRF-TOKEN (untuk AJAX)
        $postToken   = $request->getPost($tokenName);
        $headerToken = $request->getHeaderLine('X-CSRF-TOKEN');

        $token = $postToken ?? $headerToken;

        // Jika token tidak ada, tolak request
        if (empty($token)) {
            return $this->denyAccess();
        }

        // Verifikasi token menggunakan security service CI4
        try {
            if (! $security->verify($request)) {
                return $this->denyAccess();
            }
        } catch (\Throwable $e) {
            log_message('warning', 'CsrfFilter: Token verification failed - ' . $e->getMessage());
            return $this->denyAccess();
        }

        return null;
    }

    /**
     * After filter — tidak diperlukan untuk CsrfFilter.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tindakan setelah request
    }

    /**
     * Kembalikan respons HTTP 403.
     *
     * @return ResponseInterface
     */
    private function denyAccess(): ResponseInterface
    {
        $response = service('response');
        $response->setStatusCode(403);
        $response->setBody(view('errors/403'));

        return $response;
    }
}
