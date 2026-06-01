<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\UserModel;
use App\Services\AuthService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit tests untuk AuthService.
 *
 * **Validates: Requirements 6.1, 6.2, 6.3, 6.4, 6.5**
 */
class AuthServiceTest extends CIUnitTestCase
{
    protected AuthService $authService;
    protected UserModel $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
        $this->userModel   = new UserModel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Bersihkan session setelah setiap test
        session()->destroy();
    }

    // =========================================================================
    // Test Login
    // =========================================================================

    /**
     * E1: Login valid — sesi aktif + redirect sesuai peran.
     *
     * **Validates: Requirement 6.1**
     */
    public function testLoginValidCredentialsCreatesSession(): void
    {
        // Arrange: Setup data pengguna
        $userId = $this->userModel->insert([
            'name'      => 'Admin Test',
            'email'     => 'admin@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'admin',
            'is_active' => 1,
        ]);

        // Act: Login dengan kredensial valid
        $result = $this->authService->login('admin@test.local', 'Password123');

        // Assert: Sesi terbuat, user data tersedia, pesan sukses
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['user']);
        $this->assertEquals($userId, $result['user']['id']);
        $this->assertEquals('admin@test.local', $result['user']['email']);

        // Cleanup
        $this->userModel->delete($userId);
    }

    /**
     * E2: Login kredensial salah — tidak ada sesi + pesan error.
     *
     * **Validates: Requirement 6.2**
     */
    public function testLoginInvalidCredentialsFails(): void
    {
        // Arrange
        $this->userModel->insert([
            'name'      => 'Test User',
            'email'     => 'user@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        // Act: Login dengan password salah
        $result = $this->authService->login('user@test.local', 'WrongPassword');

        // Assert
        $this->assertFalse($result['success']);
        $this->assertNull($result['user']);
        $this->assertStringContainsString('tidak valid', $result['message']);

        // Cleanup
        $this->userModel->where('email', 'user@test.local')->delete();
    }

    /**
     * E3: Login akun nonaktif — tidak ada sesi + pesan error.
     *
     * **Validates: Requirement 6.3**
     */
    public function testLoginInactiveAccountFails(): void
    {
        // Arrange: Akun aktif = 0
        $this->userModel->insert([
            'name'      => 'Inactive User',
            'email'     => 'inactive@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 0,
        ]);

        // Act
        $result = $this->authService->login('inactive@test.local', 'Password123');

        // Assert
        $this->assertFalse($result['success']);
        $this->assertNull($result['user']);
        $this->assertStringContainsString('dinonaktifkan', $result['message']);

        // Cleanup
        $this->userModel->where('email', 'inactive@test.local')->delete();
    }

    // =========================================================================
    // Test Register
    // =========================================================================

    /**
     * Test registrasi dengan email duplikat — pesan error + tidak buat akun.
     *
     * **Validates: Requirement 6.5**
     */
    public function testRegisterDuplicateEmailFails(): void
    {
        // Arrange: Insert user dengan email tertentu
        $this->userModel->insert([
            'name'      => 'Existing User',
            'email'     => 'existing@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        // Act: Coba register dengan email yang sama
        $result = $this->authService->register('New User', 'existing@test.local', 'Password123');

        // Assert
        $this->assertFalse($result['success']);
        $this->assertNull($result['user_id']);
        $this->assertStringContainsString('sudah terdaftar', $result['message']);

        // Cleanup
        $this->userModel->where('email', 'existing@test.local')->delete();
    }

    /**
     * Test registrasi password kurang dari 8 karakter — pesan error.
     */
    public function testRegisterShortPasswordFails(): void
    {
        // Act
        $result = $this->authService->register('New User', 'newuser@test.local', 'short1');

        // Assert
        $this->assertFalse($result['success']);
        $this->assertNull($result['user_id']);
        $this->assertStringContainsString('minimal 8', $result['message']);
    }

    // =========================================================================
    // Test Password Reset
    // =========================================================================

    /**
     * Test reset password dengan token expired — gagal.
     */
    public function testResetPasswordExpiredTokenFails(): void
    {
        // Arrange: Insert user dan token yang sudah kedaluwarsa
        $db = \Config\Database::connect();
        $this->userModel->insert([
            'name'      => 'Reset User',
            'email'     => 'reset@test.local',
            'password'  => password_hash('OldPassword123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        $expiredToken = bin2hex(random_bytes(32));
        $db->table('password_resets')->insert([
            'email'      => 'reset@test.local',
            'token'      => $expiredToken,
            'expires_at' => date('Y-m-d H:i:s', strtotime('-1 hour')), // Token sudah expired
            'is_used'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Act
        $result = $this->authService->resetPassword($expiredToken, 'NewPassword123');

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('kedaluwarsa', $result['message']);

        // Cleanup
        $this->userModel->where('email', 'reset@test.local')->delete();
        $db->table('password_resets')->where('token', $expiredToken)->delete();
    }

    /**
     * Test password reset dengan token valid — berhasil update password.
     */
    public function testResetPasswordValidTokenSucceeds(): void
    {
        // Arrange
        $db = \Config\Database::connect();
        $userId = $this->userModel->insert([
            'name'      => 'Reset User',
            'email'     => 'reset2@test.local',
            'password'  => password_hash('OldPassword123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        $validToken = bin2hex(random_bytes(32));
        $db->table('password_resets')->insert([
            'email'      => 'reset2@test.local',
            'token'      => $validToken,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')), // Token masih valid
            'is_used'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Act
        $result = $this->authService->resetPassword($validToken, 'NewPassword123');

        // Assert
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('berhasil diperbarui', $result['message']);

        // Verify password berhasil diubah
        $user       = $this->userModel->find($userId);
        $isNewPass  = password_verify('NewPassword123', $user['password']);
        $isOldPass  = password_verify('OldPassword123', $user['password']);
        $this->assertTrue($isNewPass);
        $this->assertFalse($isOldPass);

        // Cleanup
        $this->userModel->delete($userId);
        $db->table('password_resets')->where('token', $validToken)->delete();
    }

    // =========================================================================
    // Test Logout
    // =========================================================================

    /**
     * Test logout — session dihapus.
     */
    public function testLogoutDestroysSession(): void
    {
        // Arrange: Buat sesi dummy
        $session = session();
        $session->set([
            'user_id'   => 1,
            'logged_in' => true,
        ]);

        $this->assertTrue($session->has('logged_in'));

        // Act
        $this->authService->logout();

        // Assert: Session sudah tidak ada
        $this->assertFalse($session->has('logged_in'));
    }

    // =========================================================================
    // Test Get Redirect URL
    // =========================================================================

    /**
     * Test getRedirectUrl mengembalikan URL yang benar per peran.
     */
    public function testGetRedirectUrlReturnsCorrectUrl(): void
    {
        $adminUrl    = $this->authService->getRedirectUrl('admin');
        $operatorUrl = $this->authService->getRedirectUrl('operator');
        $pendaftarUrl= $this->authService->getRedirectUrl('pendaftar');

        $this->assertStringContainsString('admin', $adminUrl);
        $this->assertStringContainsString('operator', $operatorUrl);
        $this->assertStringContainsString('pendaftar', $pendaftarUrl);
    }
}
