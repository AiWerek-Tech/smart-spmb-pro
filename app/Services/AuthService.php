<?php

namespace App\Services;

use App\Libraries\RbacEngine;
use App\Models\UserModel;
use CodeIgniter\Database\BaseConnection;

/**
 * AuthService — Logika bisnis autentikasi pengguna.
 *
 * Menangani login, registrasi, lupa password, reset password, dan logout.
 * Sesuai Requirements 6.1 – 6.9, 25.3, 25.5.
 */
class AuthService
{
    protected UserModel $userModel;
    protected BaseConnection $db;
    protected PermissionService $permissionService;
    protected RbacEngine $rbacEngine;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db        = \Config\Database::connect();
        $this->permissionService = new PermissionService();
        $this->rbacEngine = service('rbacEngine');
    }

    // -------------------------------------------------------------------------
    // Login
    // -------------------------------------------------------------------------

    /**
     * Verifikasi kredensial dan buat sesi pengguna.
     *
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'user' => array|null, 'message' => string]
     */
    public function login(string $email, string $password): array
    {
        // Cari pengguna berdasarkan email (aktif maupun tidak)
        $user = $this->userModel->findByEmail($email);

        if ($user === null) {
            return [
                'success' => false,
                'user'    => null,
                'message' => 'Email atau kata sandi tidak valid.',
            ];
        }

        // Verifikasi password dengan bcrypt
        if (! password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'user'    => null,
                'message' => 'Email atau kata sandi tidak valid.',
            ];
        }

        // Cek status aktif akun
        if (! $user['is_active']) {
            return [
                'success' => false,
                'user'    => null,
                'message' => 'Akun Anda telah dinonaktifkan.',
            ];
        }

        // Buat sesi dan regenerasi session ID (cegah session fixation)
        $this->createSession($user);
        (new AuditLogService())->record('auth', 'login');

        return [
            'success' => true,
            'user'    => $user,
            'message' => 'Login berhasil.',
        ];
    }

    /**
     * Buat sesi pengguna dan regenerasi session ID.
     */
    protected function createSession(array $user): void
    {
        $session = session();

        // Regenerasi session ID untuk mencegah session fixation (Req 25.5)
        $session->regenerate(true);

        $baseRole = $this->permissionService->baseRoleFor((string) $user['role']);

        $session->set([
            'user_id'    => $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_role'  => $user['role'],
            'user_base_role' => $baseRole,
            'logged_in'  => true,
        ]);

        $this->rbacEngine->refreshSession((int) $user['id']);
    }

    /**
     * Tentukan URL redirect berdasarkan peran pengguna.
     *
     * @param string $role
     * @return string
     */
    public function getRedirectUrl(string $role): string
    {
        $userId = (int) session()->get('user_id');
        if ($userId > 0) {
            $permissions = $this->rbacEngine->getUserPermissions($userId);

            if (in_array('manage_system', $permissions, true) || in_array('admin.dashboard.view', $permissions, true)) {
                return base_url('admin/dashboard');
            }

            if (
                in_array('view_registrants', $permissions, true)
                || in_array('registrants.view', $permissions, true)
                || in_array('operator.dashboard.view', $permissions, true)
            ) {
                return base_url('operator/dashboard');
            }

            if (in_array('submit_registration', $permissions, true) || in_array('pendaftar.dashboard.view', $permissions, true)) {
                return base_url('pendaftar/dashboard');
            }
        }

        $baseRole = $this->permissionService->baseRoleFor($role);

        return match ($baseRole) {
            'admin'     => base_url('admin/dashboard'),
            'operator'  => base_url('operator/dashboard'),
            'pendaftar' => base_url('pendaftar/dashboard'),
            default     => base_url('auth/login'),
        };
    }

    // -------------------------------------------------------------------------
    // Registrasi
    // -------------------------------------------------------------------------

    /**
     * Daftarkan akun baru dengan peran Pendaftar.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'user_id' => int|null, 'message' => string]
     */
    public function register(string $name, string $email, string $password): array
    {
        // Validasi panjang password minimal 8 karakter
        if (strlen($password) < 8) {
            return [
                'success' => false,
                'user_id' => null,
                'message' => 'Kata sandi minimal 8 karakter.',
            ];
        }

        // Cek keunikan email (Req 6.5)
        if ($this->userModel->emailExists($email)) {
            return [
                'success' => false,
                'user_id' => null,
                'message' => 'Email sudah terdaftar. Gunakan email lain atau masuk.',
            ];
        }

        // Hash password dengan bcrypt (Req 25.3)
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userId = $this->userModel->insert([
            'name'      => $name,
            'email'     => $email,
            'password'  => $hashedPassword,
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        if (! $userId) {
            return [
                'success' => false,
                'user_id' => null,
                'message' => 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.',
            ];
        }

        // Ambil data user yang baru dibuat dan buat sesi
        $this->assignPrimaryRole((int) $userId, 'pendaftar');
        $user = $this->userModel->find($userId);
        $this->createSession($user);

        return [
            'success' => true,
            'user_id' => (int) $userId,
            'message' => 'Akun berhasil dibuat.',
        ];
    }

    // -------------------------------------------------------------------------
    // Lupa Password / Reset Password
    // -------------------------------------------------------------------------

    /**
     * Kirim token reset password ke email yang terdaftar.
     *
     * @param string $email
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendPasswordReset(string $email): array
    {
        $user = $this->userModel->findByEmail($email);

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'Email tidak ditemukan dalam sistem kami.',
            ];
        }

        // Buat token unik (64 karakter hex)
        $token     = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));

        // Hapus token lama yang belum digunakan untuk email ini
        $this->db->table('password_resets')
            ->where('email', $email)
            ->where('is_used', 0)
            ->delete();

        // Simpan token baru ke tabel password_resets
        $this->db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'is_used'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Kirim email reset password
        $resetUrl = base_url('auth/reset') . '?token=' . rawurlencode($token);
        $this->sendResetEmail($email, $user['name'], $resetUrl);

        return [
            'success' => true,
            'message' => 'Tautan pemulihan kata sandi telah dikirim ke email Anda. Tautan berlaku selama 60 menit.',
        ];
    }

    /**
     * Kirim email reset password menggunakan CI4 Email library.
     */
    protected function sendResetEmail(string $email, string $name, string $resetUrl): void
    {
        try {
            $emailService = \Config\Services::email();
            $emailService->setTo($email);
            $emailService->setSubject('Pemulihan Kata Sandi — Smart SPMB Pro');
            $emailService->setMessage(
                '<p>Halo ' . esc($name) . ',</p>'
                . '<p>Kami menerima permintaan pemulihan kata sandi untuk akun Anda.</p>'
                . '<p>Klik tautan berikut untuk mengatur ulang kata sandi Anda:</p>'
                . '<p><a href="' . $resetUrl . '">' . $resetUrl . '</a></p>'
                . '<p>Tautan ini berlaku selama <strong>60 menit</strong>.</p>'
                . '<p>Jika Anda tidak meminta pemulihan kata sandi, abaikan email ini.</p>'
                . '<p>Salam,<br>Tim Smart SPMB Pro</p>'
            );
            $emailService->send();
        } catch (\Throwable $e) {
            // Log error tapi jangan gagalkan proses (token sudah tersimpan)
            log_message('error', 'AuthService::sendResetEmail failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset password menggunakan token yang valid.
     *
     * @param string $token
     * @param string $newPassword
     * @return array ['success' => bool, 'message' => string]
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        // Validasi panjang password
        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => 'Kata sandi baru minimal 8 karakter.',
            ];
        }

        // Cari token yang valid dan belum digunakan
        $resetRecord = $this->db->table('password_resets')
            ->where('token', $token)
            ->where('is_used', 0)
            ->get()
            ->getRowArray();

        if ($resetRecord === null) {
            return [
                'success' => false,
                'message' => 'Token tidak valid atau sudah digunakan.',
            ];
        }

        // Cek apakah token sudah kedaluwarsa
        if (strtotime($resetRecord['expires_at']) < time()) {
            return [
                'success' => false,
                'message' => 'Token telah kedaluwarsa. Silakan minta tautan pemulihan baru.',
            ];
        }

        // Cari pengguna berdasarkan email dari token
        $user = $this->userModel->findByEmail($resetRecord['email']);

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
            ];
        }

        // Update password dengan hash bcrypt baru
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->userModel->update($user['id'], ['password' => $hashedPassword]);

        // Tandai token sebagai sudah digunakan
        $this->db->table('password_resets')
            ->where('token', $token)
            ->update(['is_used' => 1]);

        return [
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui. Silakan login dengan kata sandi baru Anda.',
        ];
    }

    /**
     * Validasi apakah token reset password masih valid (belum digunakan dan belum kedaluwarsa).
     *
     * @param string $token
     * @return bool
     */
    public function isValidResetToken(string $token): bool
    {
        $record = $this->db->table('password_resets')
            ->where('token', $token)
            ->where('is_used', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        return $record !== null;
    }

    // -------------------------------------------------------------------------
    // Logout
    // -------------------------------------------------------------------------

    /**
     * Hapus sesi pengguna (logout).
     */
    public function logout(): void
    {
        (new AuditLogService())->record('auth', 'logout');

        $session = session();
        $session->remove(['user_id', 'user_name', 'user_email', 'user_role', 'user_base_role', 'user_roles', 'user_permissions', 'rbac_cached_at', 'logged_in']);
        $session->destroy();
    }

    private function assignPrimaryRole(int $userId, string $roleSlug): void
    {
        if (! $this->db->tableExists('roles') || ! $this->db->tableExists('user_roles')) {
            return;
        }

        $role = $this->db->table('roles')->select('id')->where('slug', $roleSlug)->get()->getRowArray();
        if (! $role) {
            return;
        }

        $exists = $this->db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $role['id'])
            ->countAllResults();

        if ($exists === 0) {
            $this->db->table('user_roles')->insert([
                'user_id'     => $userId,
                'role_id'     => (int) $role['id'],
                'assigned_by' => null,
                'assigned_at' => date('Y-m-d H:i:s'),
                'expires_at'  => null,
            ]);
        }
    }
}
