<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RoleFilter — Middleware untuk memeriksa peran pengguna dari sesi.
 *
 * Jika peran pengguna tidak sesuai dengan yang diizinkan, kembalikan HTTP 403.
 * Sesuai Requirement 7.4, 7.5.
 *
 * Penggunaan di Routes.php:
 *   'filter' => 'auth,role:admin'
 *   'filter' => 'auth,role:operator,admin'
 *   'filter' => 'auth,role:pendaftar'
 */
class RoleFilter implements FilterInterface
{
    /**
     * Cek apakah peran pengguna sesuai dengan yang diizinkan.
     *
     * Argumen berisi daftar peran yang diizinkan, dipisahkan koma.
     * Contoh: ['admin'] atau ['operator,admin']
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika tidak ada argumen, tolak akses
        if (empty($arguments)) {
            return $this->denyAccess();
        }

        $userRole = session()->get('user_role');

        // Jika tidak ada peran di sesi, tolak akses
        // (AuthFilter seharusnya sudah menangani ini lebih dulu)
        if (empty($userRole)) {
            return $this->denyAccess();
        }

        // Argumen bisa berupa array dengan elemen yang dipisahkan koma
        // Contoh: ['operator,admin'] → allowedRoles = ['operator', 'admin']
        $allowedRoles = [];
        foreach ($arguments as $arg) {
            $roles = explode(',', $arg);
            foreach ($roles as $role) {
                $trimmed = trim($role);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (empty($allowedRoles) || ! in_array($userRole, $allowedRoles, true)) {
            return $this->denyAccess();
        }

        return null;
    }

    /**
     * After filter — tidak diperlukan untuk RoleFilter.
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
     * Kembalikan respons HTTP 403 dengan halaman error kustom.
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
