<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\UserModel;
use App\Models\DocumentRequirementModel;
use App\Models\StudentModel;
use App\Models\StudentDocumentModel;
use App\Models\JalurModel;
use App\Models\GelombangModel;
use App\Models\FeeTypeModel;
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
    protected StudentDocumentModel $documentModel;
    protected DocumentRequirementModel $documentRequirementModel;
    protected JalurModel $jalurModel;
    protected GelombangModel $gelombangModel;
    protected FeeTypeModel $feeTypeModel;
    protected RegistrationModel $registrationModel;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->service            = new RegistrationService();
        $this->userModel          = new UserModel();
        $this->studentModel       = new StudentModel();
        $this->documentModel      = new StudentDocumentModel();
        $this->documentRequirementModel = new DocumentRequirementModel();
        $this->jalurModel         = new JalurModel();
        $this->gelombangModel     = new GelombangModel();
        $this->feeTypeModel       = new FeeTypeModel();
        $this->registrationModel  = new RegistrationModel();
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        // Cleanup
        foreach (['payment_logs', 'payments', 'invoice_items', 'invoices', 'payment_methods'] as $table) {
            if ($db->tableExists($table)) {
                $db->table($table)->truncate();
            }
        }

        $this->registrationModel->where('1=1')->delete();
        $this->feeTypeModel->where('1=1')->delete();
        $this->documentModel->where('1=1')->delete();
        $this->documentRequirementModel->where('1=1')->delete();
        $this->studentModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();
        $this->gelombangModel->where('1=1')->delete();
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

    protected function createGelombang(int $jalurId, string $openDate, string $closeDate): int
    {
        return $this->gelombangModel->insert([
            'academic_year'     => '2026/2027',
            'jalur_id'          => $jalurId,
            'name'              => 'Gelombang Uji',
            'open_date'         => $openDate,
            'close_date'        => $closeDate,
            'announcement_date' => date('Y-m-d', strtotime($closeDate . ' +7 days')),
            'is_active'         => 1,
        ]);
    }

    protected function createRequiredDocuments(int $userId): void
    {
        $student = $this->studentModel->findByUserId($userId);
        $this->assertNotNull($student);

        foreach (['kk', 'akta', 'foto'] as $type) {
            $this->documentModel->insert([
                'student_id'    => (int) $student['id'],
                'academic_year' => '2026/2027',
                'document_type' => $type,
                'file_name'     => $type . '.jpg',
                'file_path'     => 'uploads/documents/' . $userId . '/' . $type . '.jpg',
                'file_size'     => 1024,
                'mime_type'     => 'image/jpeg',
                'status'        => 'approved',
            ]);
        }
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
        $this->createRequiredDocuments($userId);

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

    public function testFinalizeCreatesInvoiceFromAutoInvoiceFees(): void
    {
        $userId = $this->createUserWithStudent();
        $jalurId = $this->createJalur();
        $this->createRequiredDocuments($userId);
        $this->feeTypeModel->insert([
            'code'                         => 'formulir',
            'name'                         => 'Biaya Formulir',
            'amount'                       => 125000,
            'billing_period'               => 'Satu Kali',
            'is_required'                  => 1,
            'is_active'                    => 1,
            'show_on_homepage'             => 1,
            'requires_payment_before_form' => 0,
            'auto_invoice'                 => 1,
            'icon'                         => 'wallet',
            'sort_order'                   => 100,
        ]);

        $result = $this->service->finalize($userId, $jalurId);

        $this->assertTrue($result['success'], $result['message'] ?? '');
        $registration = $this->registrationModel->where('user_id', $userId)->first();
        $invoice = \Config\Database::connect()
            ->table('invoices')
            ->where('registration_id', $registration['id'])
            ->get()
            ->getRowArray();

        $this->assertNotNull($invoice);
        $this->assertSame('unpaid', $invoice['status']);
        $this->assertSame(125000.0, (float) $invoice['total_amount']);
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
        $this->createRequiredDocuments($userId1);
        $this->service->finalize($userId1, $jalurId);

        // Coba daftar user kedua (kuota penuh)
        $userId2 = $this->createUserWithStudent();
        $this->createRequiredDocuments($userId2);
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

    public function testFinalizeFaultsWhenGelombangNotYetOpen(): void
    {
        $userId = $this->createUserWithStudent();
        $jalurId = $this->createJalur();
        $this->createRequiredDocuments($userId);
        $gelombangId = $this->createGelombang(
            $jalurId,
            date('Y-m-d', strtotime('+7 days')),
            date('Y-m-d', strtotime('+21 days'))
        );

        $result = $this->service->finalize($userId, $jalurId, $gelombangId);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('belum dibuka', $result['message']);
    }

    public function testFinalizeFaultsWhenGelombangAlreadyClosed(): void
    {
        $userId = $this->createUserWithStudent();
        $jalurId = $this->createJalur();
        $this->createRequiredDocuments($userId);
        $gelombangId = $this->createGelombang(
            $jalurId,
            date('Y-m-d', strtotime('-21 days')),
            date('Y-m-d', strtotime('-7 days'))
        );

        $result = $this->service->finalize($userId, $jalurId, $gelombangId);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('sudah ditutup', $result['message']);
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

    public function testSaveStep8UsesConfiguredRequiredDocuments(): void
    {
        $userId = $this->createUserWithStudent();
        $student = $this->studentModel->findByUserId($userId);
        $this->assertNotNull($student);

        $this->documentRequirementModel->where('academic_year', '2026/2027')->delete();
        $this->documentRequirementModel->insert([
            'academic_year'         => '2026/2027',
            'jalur_id'              => null,
            'document_type'         => 'kk',
            'label'                 => 'Kartu Keluarga',
            'is_required'           => 1,
            'allowed_extensions'    => 'jpg,jpeg,png,pdf',
            'max_size_kb'           => 2048,
            'requires_verification' => 0,
            'is_active'             => 1,
            'sort_order'            => 10,
        ]);

        $this->documentModel->insert([
            'student_id'    => (int) $student['id'],
            'academic_year' => '2026/2027',
            'document_type' => 'kk',
            'file_name'     => 'kk.jpg',
            'file_path'     => 'uploads/documents/' . $userId . '/kk.jpg',
            'file_size'     => 1024,
            'mime_type'     => 'image/jpeg',
            'status'        => 'pending',
        ]);

        $result = $this->service->saveStep($userId, 8, []);

        $this->assertTrue($result['success'], $result['message'] ?? '');
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
