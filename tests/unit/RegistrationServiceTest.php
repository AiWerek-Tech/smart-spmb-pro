<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\JalurModel;
use App\Models\RegistrationModel;
use App\Services\RegistrationService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Unit tests untuk RegistrationService.
 *
 * **Validates: Requirements 8.7, 14.3, 14.4, 14.7, 29.3, 29.4**
 */
class RegistrationServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $namespace = 'App';

    protected RegistrationService $service;
    protected UserModel $userModel;
    protected StudentModel $studentModel;
    protected JalurModel $jalurModel;
    protected RegistrationModel $registrationModel;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->service            = new RegistrationService();
        $this->userModel          = new UserModel();
        $this->studentModel       = new StudentModel();
        $this->jalurModel         = new JalurModel();
        $this->registrationModel  = new RegistrationModel();
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        // Cleanup
        $this->registrationModel->where('1=1')->delete();
        $this->studentModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();
        $this->jalurModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    /**
     * Helper: Buat user + student untuk testing.
     */
    protected function createUserWithStudent(): int
    {
        $userId = $this->userModel->insert([
            'name'      => 'Test User',
            'email'     => 'testuser' . uniqid() . '@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        $this->studentModel->insert([
            'user_id'       => $userId,
            'full_name'     => 'Test Student',
            'gender'        => 'L',
            'birth_place'   => 'Jakarta',
            'birth_date'    => '2010-05-15',
            'religion'      => 'Islam',
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => '3674051506100001',
        ]);

        return $userId;
    }

    /**
     * Helper: Buat jalur untuk testing.
     */
    protected function createJalur(): int
    {
        return $this->jalurModel->insert([
            'name'        => 'Test Jalur',
            'description' => 'Jalur untuk testing',
            'quota'       => 100,
            'is_active'   => 1,
        ]);
    }

    // =========================================================================
    // Test Generate Registration Number
    // =========================================================================

    /**
     * Test generateRegistrationNumber — format benar.
     *
     * **Validates: Requirement 29.3**
     */
    public function testGenerateRegistrationNumberFormatCorrect(): void
    {
        $academicYear = date('Y');
        $regNumber    = $this->service->generateRegistrationNumber($academicYear);

        // Format: SPMB-[TAHUN]-[NOMOR_URUT_4_DIGIT]
        $this->assertMatchesRegularExpression(
            '/^SPMB-' . $academicYear . '-\d{4}$/',
            $regNumber
        );
    }

    /**
     * Test generateRegistrationNumber — nomor unik setiap kali dipanggil.
     *
     * **Validates: Requirement 29.3**
     */
    public function testGenerateRegistrationNumberUnique(): void
    {
        $academicYear  = date('Y');
        $regNumber1    = $this->service->generateRegistrationNumber($academicYear);
        $regNumber2    = $this->service->generateRegistrationNumber($academicYear);

        $this->assertNotEquals($regNumber1, $regNumber2);

        // Verifikasi urutan increment
        preg_match('/SPMB-\d+-(\d+)$/', $regNumber1, $matches1);
        preg_match('/SPMB-\d+-(\d+)$/', $regNumber2, $matches2);

        $seq1 = intval($matches1[1]);
        $seq2 = intval($matches2[1]);

        $this->assertEquals($seq2, $seq1 + 1);
    }

    // =========================================================================
    // Test Finalize Registration
    // =========================================================================

    /**
     * Test finalize — membuat registration record dengan nomor unik.
     *
     * **Validates: Requirement 29.4**
     */
    public function testFinalizeCreatesRegistrationRecord(): void
    {
        $userId   = $this->createUserWithStudent();
        $jalurId  = $this->createJalur();

        $result = $this->service->finalize($userId, $jalurId);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['registration_number']);
        $this->assertStringContainsString('SPMB-', $result['registration_number']);

        // Verify di database
        $registration = $this->registrationModel->where('user_id', $userId)->first();
        $this->assertNotNull($registration);
        $this->assertEquals('submitted', $registration['status']);
        $this->assertEquals($jalurId, $registration['jalur_id']);
    }

    /**
     * Test finalize — kuota penuh ditolak.
     */
    public function testFinalizeFaultsWhenQuotaFull(): void
    {
        // Buat jalur dengan quota 1
        $jalurId = $this->jalurModel->insert([
            'name'        => 'Full Jalur',
            'description' => 'Jalur penuh',
            'quota'       => 1,
            'is_active'   => 1,
        ]);

        // Daftar user pertama
        $userId1 = $this->createUserWithStudent();
        $this->service->finalize($userId1, $jalurId);

        // Coba daftar user kedua (kuota penuh)
        $userId2 = $this->createUserWithStudent();
        $result  = $this->service->finalize($userId2, $jalurId);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('penuh', $result['message']);
    }

    /**
     * Test finalize — jalur nonaktif ditolak.
     */
    public function testFinalizeFaultsWhenJalurInactive(): void
    {
        // Buat jalur nonaktif
        $jalurId = $this->jalurModel->insert([
            'name'        => 'Inactive Jalur',
            'description' => 'Jalur nonaktif',
            'quota'       => 100,
            'is_active'   => 0,
        ]);

        $userId = $this->createUserWithStudent();
        $result = $this->service->finalize($userId, $jalurId);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('tidak aktif', $result['message']);
    }

    // =========================================================================
    // Test Save Step
    // =========================================================================

    /**
     * Test saveStep1 — validasi NIK 16 digit.
     *
     * **Validates: Requirements 8.7, 9.4, 9.5**
     */
    public function testSaveStep1ValidatesNik(): void
    {
        $userId = $this->createUserWithStudent();

        $data = [
            'full_name'  => 'Student Name',
            'gender'     => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-15',
            'religion'   => 'Islam',
            'citizenship' => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'        => 'NOTNIKFORMAT', // Invalid NIK
            'nisn'       => null,
        ];

        $result = $this->service->saveStep($userId, 1, $data);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('nik', $result['errors']);
    }

    /**
     * Test saveStep1 — validasi NISN 10 digit.
     *
     * **Validates: Requirements 9.6, 9.7**
     */
    public function testSaveStep1ValidatesNisn(): void
    {
        $userId = $this->createUserWithStudent();

        $data = [
            'full_name'  => 'Student Name',
            'gender'     => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-15',
            'religion'   => 'Islam',
            'citizenship' => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'        => '3674051506100001',
            'nisn'       => '12345', // Invalid NISN (should be 10 digits)
        ];

        $result = $this->service->saveStep($userId, 1, $data);

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('nisn', $result['errors']);
    }

    /**
     * Test saveStep — data tersimpan sementara.
     *
     * **Validates: Requirement 8.6**
     */
    public function testSaveStepPersistsDraftData(): void
    {
        $userId = $this->createUserWithStudent();

        $data = [
            'full_name'  => 'Student Name',
            'gender'     => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-15',
            'religion'   => 'Islam',
            'citizenship' => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'        => '3674051506100001',
            'nisn'       => '0123456789',
        ];

        $result = $this->service->saveStep($userId, 1, $data);

        $this->assertTrue($result['success']);

        // Verify data tersimpan
        $draftData = $this->service->getDraftData($userId);
        $this->assertNotEmpty($draftData['step_1']);
        $this->assertEquals('Student Name', $draftData['step_1']['full_name']);
    }

    // =========================================================================
    // Test Get Draft Data
    // =========================================================================

    /**
     * Test getDraftData — mengambil data dari semua step.
     */
    public function testGetDraftDataReturnsStructuredData(): void
    {
        $userId = $this->createUserWithStudent();

        $draftData = $this->service->getDraftData($userId);

        $this->assertArrayHasKey('step_1', $draftData);
        $this->assertArrayHasKey('step_2', $draftData);
        $this->assertArrayHasKey('step_3', $draftData);
        $this->assertArrayHasKey('step_4', $draftData);
        $this->assertArrayHasKey('step_5', $draftData);
        $this->assertArrayHasKey('step_6', $draftData);
        $this->assertArrayHasKey('step_7', $draftData);
        $this->assertArrayHasKey('step_8', $draftData);
    }

    /**
     * Test getDraftData — empty jika user tidak ditemukan.
     */
    public function testGetDraftDataReturnsEmptyWhenUserNotFound(): void
    {
        $draftData = $this->service->getDraftData(99999);

        $this->assertEmpty($draftData);
    }
}
