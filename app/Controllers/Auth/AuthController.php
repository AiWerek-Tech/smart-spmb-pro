<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Services\AuthService;

/**
 * AuthController — Menangani tampilan dan proses autentikasi pengguna.
 *
 * Methods:
 * - loginView() — Tampilkan form login
 * - login() — Proses login (POST)
 * - registerView() — Tampilkan form register
 * - register() — Proses register (POST)
 * - forgotView() — Tampilkan form lupa password
 * - sendReset() — Kirim email reset (POST)
 * - resetView($token) — Tampilkan form reset password
 * - resetPassword() — Proses reset password (POST)
 * - logout() — Logout dan redirect
 *
 * Requirements: 6.1–6.9, 25.3, 25.5
 */
class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Tampilkan halaman login.
     * GET: /auth/login
     */
    public function loginView()
    {
        // Jika sudah login, redirect ke dashboard sesuai peran
        if (session()->has('user_id')) {
            return redirect()->to($this->getRedirectUrl(session()->get('user_role')));
        }

        return view('auth/login', [
            'title'  => 'Login',
            'errors' => session()->getFlashdata('errors'),
        ]);
    }

    /**
     * Proses login.
     * POST: /auth/login
     *
     * Requirements: 6.1 (E1), 6.2 (E2), 6.3 (E3)
     */
    public function login()
    {
        // Validasi CSRF
        if (! $this->validate([
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[1]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Panggil AuthService untuk login
        $result = $this->authService->login($email, $password);

        if (! $result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        // Login berhasil, redirect sesuai peran (Req 6.6, 6.7, 6.8)
        $redirectUrl = $this->authService->getRedirectUrl($result['user']['role']);
        return redirect()->to($redirectUrl);
    }

    /**
     * Tampilkan halaman register.
     * GET: /auth/register
     */
    public function registerView()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->has('user_id')) {
            return redirect()->to($this->getRedirectUrl(session()->get('user_role')));
        }

        return view('auth/register', [
            'title'  => 'Daftar Akun',
            'errors' => session()->getFlashdata('errors'),
        ]);
    }

    /**
     * Proses registrasi akun baru.
     * POST: /auth/register
     *
     * Requirements: 6.4, 6.5
     */
    public function register()
    {
        $settingModel = new \App\Models\SettingModel();
        $isEmailRequired = (int)$settingModel->getValue('registration_email_required', '1') === 1;

        $emailRule = 'permit_empty';
        if ($isEmailRequired) {
            $emailRule = 'required|valid_email|is_unique[users.email]';
        } else {
            $emailPost = $this->request->getPost('email');
            if (!empty($emailPost)) {
                $emailRule = 'valid_email|is_unique[users.email]';
            }
        }

        // Validasi input
        if (! $this->validate([
            'name'                 => 'required|min_length[3]|max_length[100]',
            'email'                => $emailRule,
            'password'             => 'required|min_length[8]|max_length[255]',
            'password_confirm'     => 'required|matches[password]',
        ], [
            'email' => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'   => 'Email sudah terdaftar. Gunakan email lain atau masuk.',
            ],
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $name     = $this->request->getPost('name');
        $email    = (string) $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Panggil AuthService untuk registrasi
        $result = $this->authService->register($name, $email, $password);

        if (! $result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        // Registrasi berhasil, redirect ke dashboard Pendaftar
        return redirect()->to(base_url('pendaftar/dashboard'))
            ->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    /**
     * Tampilkan halaman lupa password.
     * GET: /auth/forgot
     */
    public function forgotView()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->has('user_id')) {
            return redirect()->to($this->getRedirectUrl(session()->get('user_role')));
        }

        return view('auth/forgot_password', [
            'title'  => 'Lupa Password',
            'errors' => session()->getFlashdata('errors'),
        ]);
    }

    /**
     * Kirim email reset password.
     * POST: /auth/forgot
     *
     * Requirements: Lupa password (email verification)
     */
    public function sendReset()
    {
        // Validasi email
        if (! $this->validate([
            'email' => 'required|valid_email',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');

        // Kirim email reset
        $result = $this->authService->sendPasswordReset($email);

        if (! $result['success']) {
            // Jangan beri tahu apakah email ada atau tidak (security)
            return redirect()->back()
                ->with('info', 'Jika email terdaftar, Anda akan menerima link reset password dalam beberapa menit.');
        }

        return redirect()->back()
            ->with('success', 'Link reset password telah dikirim ke email Anda.');
    }

    /**
     * Tampilkan halaman reset password.
     * GET: /auth/reset?token={token}
     *
     * Requirements: Reset password form
     */
    public function resetView($token = null)
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->has('user_id')) {
            return redirect()->to($this->getRedirectUrl(session()->get('user_role')));
        }

        $token = $this->request->getGet('token') ?? $token;

        // Validasi token dari query parameter atau argumen
        if (empty($token)) {
            return redirect()->to(base_url('auth/forgot'))
                ->with('error', 'Token reset tidak valid.');
        }

        // Cek validitas token
        if (! $this->authService->isValidResetToken($token)) {
            return redirect()->to(base_url('auth/forgot'))
                ->with('error', 'Token reset tidak valid atau telah kadaluarsa.');
        }

        return view('auth/reset_password', [
            'title'  => 'Reset Password',
            'token'  => $token,
            'errors' => session()->getFlashdata('errors'),
        ]);
    }

    /**
     * Proses reset password.
     * POST: /auth/reset
     *
     * Requirements: 6.5 (password reset)
     */
    public function resetPassword()
    {
        $token = $this->request->getPost('token');

        // Validasi form
        if (! $this->validate([
            'token'                => 'required',
            'password'             => 'required|min_length[8]|max_length[255]',
            'password_confirm'     => 'required|matches[password]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $newPassword = $this->request->getPost('password');

        // Panggil AuthService untuk reset password
        $result = $this->authService->resetPassword($token, $newPassword);

        if (! $result['success']) {
            return redirect()->to(base_url('auth/forgot'))
                ->with('error', $result['message']);
        }

        // Reset berhasil, redirect ke login
        return redirect()->to(base_url('auth/login'))
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Logout pengguna.
     * GET: /auth/logout
     *
     * Requirements: 6.9 (Logout)
     */
    public function logout()
    {
        $this->authService->logout();

        return redirect()->to(base_url('/'))
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Tentukan URL redirect berdasarkan peran.
     * Helper method yang memanggil AuthService::getRedirectUrl()
     *
     * @param string $role
     * @return string
     */
    protected function getRedirectUrl(string $role): string
    {
        return $this->authService->getRedirectUrl($role);
    }
}
