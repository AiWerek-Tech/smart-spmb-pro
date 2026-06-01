<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter — Middleware untuk memeriksa sesi aktif.
 *
 * Jika pengguna tidak memiliki sesi aktif, redirect ke halaman login.
 * Sesuai Requirement 6.8, 7.6.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Cek apakah pengguna memiliki sesi aktif.
     * Jika tidak, redirect ke halaman login (bukan HTTP 403).
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->has('user_id') || empty(session()->get('user_id'))) {
            // Simpan URL yang dituju agar bisa redirect setelah login
            session()->set('redirect_url', current_url());

            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        return null;
    }

    /**
     * After filter — tidak diperlukan untuk AuthFilter.
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
}
