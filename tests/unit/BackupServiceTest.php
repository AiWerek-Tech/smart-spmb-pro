<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\UserModel;
use App\Services\BackupService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Unit & Integration tests untuk Backup & Restore Database.
 *
 * **Validates: Requirements 13.1, 13.2, 15.4**
 */
class BackupServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = false;
    protected $namespace   = 'App';
    protected $userModel;
    protected BackupService $backupService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel     = new UserModel();
        $this->backupService = new BackupService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        session()->destroy();
    }

    /**
     * Test generateBackup() menghasilkan dump SQL yang tidak kosong dan valid.
     */
    public function testGenerateBackupReturnsValidSqlString()
    {
        // Masukkan setidaknya satu record agar dump memiliki data INSERT
        $this->userModel->insert([
            'name'      => 'Backup Test User',
            'email'     => 'backup_test@example.local',
            'password'  => password_hash('password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        $sql = $this->backupService->generateBackup();

        $this->assertNotEmpty($sql);
        $this->assertStringContainsString('CREATE TABLE', $sql);
        $this->assertStringContainsString('users', $sql);
        $this->assertStringContainsString('backup_test@example.local', $sql);
    }

    /**
     * Test restoreBackup() memulihkan database ke keadaan sebelumnya.
     */
    public function testRestoreBackupRestoresDatabaseState()
    {
        // 1. Bersihkan tabel user dan insert satu user awal
        $this->userModel->where('1=1')->delete();
        $this->userModel->insert([
            'name'      => 'Original User',
            'email'     => 'original@example.local',
            'password'  => password_hash('password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        // 2. Generate backup pada kondisi ini
        $backupSql = $this->backupService->generateBackup();

        // 3. Ubah database (ganti nama user dan insert user baru)
        $this->userModel->where('email', 'original@example.local')->update(null, [
            'name' => 'Modified User',
        ]);
        
        $this->userModel->insert([
            'name'      => 'New User Added After Backup',
            'email'     => 'new_after@example.local',
            'password'  => password_hash('password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        // Verifikasi perubahan terjadi di DB
        $modifiedUser = $this->userModel->where('email', 'original@example.local')->first();
        $this->assertEquals('Modified User', $modifiedUser['name']);
        
        $newCount = $this->userModel->where('email', 'new_after@example.local')->countAllResults();
        $this->assertEquals(1, $newCount);

        // 4. Jalankan Pemulihan (Restore)
        $result = $this->backupService->restoreBackup($backupSql);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('berhasil', $result['message']);

        // 5. Verifikasi database dikembalikan persis ke keadaan sebelum backup
        $restoredOriginal = $this->userModel->where('email', 'original@example.local')->first();
        $this->assertNotNull($restoredOriginal);
        $this->assertEquals('Original User', $restoredOriginal['name']); // Nama dikembalikan ke awal

        $restoredNewCount = $this->userModel->where('email', 'new_after@example.local')->countAllResults();
        $this->assertEquals(0, $restoredNewCount); // User baru yang dibuat pasca-backup terhapus
    }

    /**
     * Test index page Backup & Restore — Hanya Admin yang boleh akses.
     */
    public function testIndexAccessAllowedForAdminOnly()
    {
        // 1. Coba sebagai pendaftar -> Harus terblokir (403 Forbidden)
        $pendaftarSession = [
            'user_id'   => 99,
            'user_name' => 'Siswa Test',
            'user_role' => 'pendaftar',
            'logged_in' => true,
        ];
        $result = $this->withSession($pendaftarSession)->get('admin/backup');
        
        $result->assertStatus(403);

        // 2. Coba sebagai admin -> Akses diizinkan (200)
        $adminSession = [
            'user_id'   => 1,
            'user_name' => 'Admin Test',
            'user_role' => 'admin',
            'logged_in' => true,
        ];
        $result = $this->withSession($adminSession)->get('admin/backup');
        
        $result->assertStatus(200);
        $result->assertSee('Backup &amp; Restore Database');
    }

    /**
     * Test create backup download — Mengunduh file SQL dengan benar.
     */
    public function testCreateBackupDownloadsSqlFile()
    {
        $adminSession = [
            'user_id'   => 1,
            'user_name' => 'Admin Test',
            'user_role' => 'admin',
            'logged_in' => true,
        ];

        // Jalankan POST request karena route backup/create di Routes.php terdaftar sebagai POST
        $result = $this->withSession($adminSession)->post('admin/backup/create');

        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'application/sql');
        
        $contentDisposition = $result->response()->getHeaderLine('Content-Disposition');
        $this->assertStringContainsString('attachment; filename="backup_spmb_', $contentDisposition);
        $this->assertStringContainsString('.sql"', $contentDisposition);
        
        $this->assertNotEmpty($result->getBody());
    }

    /**
     * Test restore backup dengan parameter tidak lengkap (tidak ada konfirmasi) — Menolak restore.
     */
    public function testRestoreWithoutConfirmationFails()
    {
        $adminSession = [
            'user_id'   => 1,
            'user_name' => 'Admin Test',
            'user_role' => 'admin',
            'logged_in' => true,
        ];

        // Post tanpa check/confirm=1
        $result = $this->withSession($adminSession)->post('admin/backup/restore', [
            'confirm' => '0',
        ]);

        $result->assertStatus(302);
        $result->assertSessionHas('error', 'Anda harus memberikan konfirmasi sebelum memulihkan database.');
    }
}
