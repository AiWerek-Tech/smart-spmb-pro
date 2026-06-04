<?php

namespace Tests\Unit;

use App\Controllers\Auth\AuthController;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class AuthControllerTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate            = false;
    protected $refresh            = false;
    protected $namespace          = 'App';
    protected $seeders            = ['SampleSeeder'];

    protected $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new UserModel();
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');
        $this->userModel->where('1=1')->delete();
        $db->table('password_resets')->where('1=1')->delete();
        $db->query('SET FOREIGN_KEY_CHECKS=1;');

        parent::tearDown();
        // Cleanup sessions if any
        session()->destroy();
    }

    /**
     * Test loginView() — Jika sudah login, redirect ke dashboard; jika belum, tampilkan form login.
     *
     * Requirements: 6.1
     */
    public function testLoginViewShowsFormWhenNotLoggedIn()
    {
        // Pastikan tidak ada user login
        if (session()->has('user_id')) {
            session()->destroy();
        }

        $result = $this->withSession([])
            ->get('/auth/login');

        $result->assertStatus(200);
        $result->assertSee('Login');
        $result->assertSee('email');
        $result->assertSee('password');
    }

    /**
     * Test loginView() — Redirect ke dashboard jika sudah login.
     *
     * Requirements: 6.1
     */
    public function testLoginViewRedirectsIfAlreadyLoggedIn()
    {
        $sessionData = [
            'user_id'   => 1,
            'user_name' => 'Test User',
            'user_role' => 'pendaftar',
            'logged_in' => true,
        ];

        $result = $this->withSession($sessionData)
            ->get('/auth/login');

        $result->assertStatus(302);
        $result->assertRedirect('/pendaftar/dashboard');
    }

    /**
     * Test login() — Login dengan kredensial valid.
     *
     * Requirements: 6.1 (E1 - Valid login), 6.2
     */
    public function testLoginWithValidCredentials()
    {
        // Buat user test dengan password hash
        $userData = [
            'name'      => 'Test User',
            'email'     => 'test@example.com',
            'password'  => password_hash('password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ];
        $this->userModel->insert($userData);

        $result = $this->post('/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        // Redirect ke dashboard
        $result->assertStatus(302);
        $result->assertRedirect('/pendaftar/dashboard');
    }

    /**
     * Test login() — Login dengan kredensial invalid.
     *
     * Requirements: 6.2 (E2 - Invalid credentials)
     */
    public function testLoginWithInvalidCredentials()
    {
        // Buat user test
        $userData = [
            'name'      => 'Test User',
            'email'     => 'test@example.com',
            'password'  => password_hash('password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ];
        $this->userModel->insert($userData);

        // Login dengan password salah
        $result = $this->post('/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // Redirect kembali ke form dengan error
        $result->assertStatus(302);
        $result->assertSessionHas('error');
    }

    /**
     * Test login() — Login dengan akun nonaktif.
     *
     * Requirements: 6.3 (E3 - Inactive account)
     */
    public function testLoginWithInactiveAccount()
    {
        // Buat user test dengan is_active = 0
        $userData = [
            'name'      => 'Test User',
            'email'     => 'inactive@example.com',
            'password'  => password_hash('password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 0,
        ];
        $this->userModel->insert($userData);

        // Login
        $result = $this->post('/auth/login', [
            'email'    => 'inactive@example.com',
            'password' => 'password123',
        ]);

        // Redirect kembali dengan error
        $result->assertStatus(302);
        $result->assertSessionHas('error');
        $result->assertSessionMissing('user_id');
    }

    /**
     * Test registerView() — Tampilkan form register jika belum login.
     *
     * Requirements: 6.4
     */
    public function testRegisterViewShowsFormWhenNotLoggedIn()
    {
        $result = $this->withSession([])
            ->get('/auth/register');

        $result->assertStatus(200);
        $result->assertSee('Daftar Akun');
        $result->assertSee('name');
        $result->assertSee('email');
        $result->assertSee('password');
        $result->assertSee('password_confirm');
    }

    public function testRegisterViewShowsEmailPurposeHelperText(): void
    {
        $result = $this->withSession([])
            ->get('/auth/register');

        $result->assertStatus(200);
        $result->assertSee('Email digunakan untuk menerima informasi pendaftaran');
        $result->assertSee('dapat menggunakan email orang tua/wali/keluarga');
    }

    public function testRegisterWithoutEmailShowsClearFieldValidation(): void
    {
        $result = $this->post('/auth/register', [
            'name'             => 'New User',
            'email'            => '',
            'password'         => 'password123',
            'password_confirm' => 'password123',
            'terms'            => 'on',
        ]);

        $result->assertStatus(302);
        $result->assertSessionHas('errors');

        $errors = session()->getFlashdata('errors');
        $this->assertSame('Email wajib diisi.', $errors['email'] ?? null);
    }

    /**
     * Test register() — Registrasi dengan data valid.
     *
     * Requirements: 6.4, 6.5
     */
    public function testRegisterWithValidData()
    {
        $result = $this->post('/auth/register', [
            'name'             => 'New User',
            'email'            => 'newuser@example.com',
            'password'         => 'password123',
            'password_confirm' => 'password123',
            'terms'            => 'on',
        ]);

        // Redirect ke dashboard
        $result->assertStatus(302);
        $result->assertRedirect('/pendaftar/dashboard');

        // Verifikasi user dibuat di database
        $user = $this->userModel->findByEmail('newuser@example.com');
        $this->assertNotNull($user);
        $this->assertEquals('New User', $user['name']);
        $this->assertEquals('pendaftar', $user['role']);
        $this->assertTrue(password_verify('password123', $user['password']));
    }

    /**
     * Test register() — Registrasi dengan email duplikat.
     *
     * Requirements: 6.4, 6.5
     */
    public function testRegisterWithDuplicateEmail()
    {
        // Buat user pertama
        $userData = [
            'name'      => 'Existing User',
            'email'     => 'existing@example.com',
            'password'  => password_hash('password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ];
        $this->userModel->insert($userData);

        // Coba registrasi dengan email yang sama
        $result = $this->post('/auth/register', [
            'name'             => 'Another User',
            'email'            => 'existing@example.com',
            'password'         => 'password123',
            'password_confirm' => 'password123',
            'terms'            => 'on',
        ]);

        // Redirect kembali dengan error
        $result->assertStatus(302);
        $result->assertSessionHas('errors');
    }

    /**
     * Test register() — Password kurang dari 8 karakter.
     *
     * Requirements: 6.4, 6.5
     */
    public function testRegisterWithShortPassword()
    {
        $result = $this->post('/auth/register', [
            'name'             => 'New User',
            'email'            => 'newuser@example.com',
            'password'         => 'pass123',
            'password_confirm' => 'pass123',
            'terms'            => 'on',
        ]);

        // Validation error
        $result->assertStatus(302);
        $result->assertSessionHas('errors');
    }

    /**
     * Test register() — Password tidak cocok dengan konfirmasi.
     *
     * Requirements: 6.4, 6.5
     */
    public function testRegisterWithMismatchedPasswords()
    {
        $result = $this->post('/auth/register', [
            'name'             => 'New User',
            'email'            => 'newuser@example.com',
            'password'         => 'password123',
            'password_confirm' => 'password456',
            'terms'            => 'on',
        ]);

        // Validation error
        $result->assertStatus(302);
        $result->assertSessionHas('errors');
    }

    /**
     * Test forgotView() — Tampilkan form lupa password.
     *
     * Requirements: Forgot password form
     */
    public function testForgotViewShowsForm()
    {
        $result = $this->withSession([])
            ->get('/auth/forgot');

        $result->assertStatus(200);
        $result->assertSee('Lupa Password');
        $result->assertSee('email');
    }

    /**
     * Test sendReset() — Kirim email reset dengan email terdaftar.
     *
     * Requirements: Reset password email
     */
    public function testSendResetWithValidEmail()
    {
        // Buat user
        $userData = [
            'name'      => 'Test User',
            'email'     => 'test@example.com',
            'password'  => password_hash('password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ];
        $this->userModel->insert($userData);

        // Kirim reset request
        $result = $this->post('/auth/send-reset', [
            'email' => 'test@example.com',
        ]);

        // Redirect dengan success message
        $result->assertStatus(302);
        $result->assertSessionHas('success');
    }

    public function testResetViewWithValidToken()
    {
        $db = \Config\Database::connect();
        
        $email = 'test@example.com';
        $dummyToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));
        
        // Insert dummy token
        $db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $dummyToken,
            'expires_at' => $expiresAt,
            'is_used'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $result = $this->get('/auth/reset?token=' . $dummyToken);

        $result->assertStatus(200);
        $result->assertSee('Reset Password');
    }

    /**
     * Test logout() — Logout dan hapus sesi.
     *
     * Requirements: 6.9 (Logout)
     */
    public function testLogout()
    {
        // Login terlebih dahulu
        $sessionData = [
            'user_id'   => 1,
            'user_name' => 'Test User',
            'user_role' => 'pendaftar',
            'logged_in' => true,
        ];

        $result = $this->withSession($sessionData)
            ->get('/auth/logout');

        // Redirect ke homepage
        $result->assertStatus(302);
        $result->assertRedirect('/');
        // Session harus dihapus
        $result->assertSessionMissing('user_id');
        $result->assertSessionMissing('logged_in');
    }
}
