<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\JalurModel;
use App\Models\RegistrationModel;
use App\Models\StudentAddressModel;
use App\Models\StudentFamilyModel;
use App\Models\StudentPeriodicModel;
use App\Models\StudentDocumentModel;
use App\Services\ExportService;
use App\Services\RegistrationService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Unit tests untuk ExportService.
 *
 * **Validates: Requirements 20.1-20.5, 21.1-21.5, 22.1-22.4**
 */
class ExportServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $namespace = 'App';

    protected ExportService $exportService;
    protected RegistrationService $registrationService;
    protected UserModel $userModel;
    protected StudentModel $studentModel;
    protected JalurModel $jalurModel;
    protected RegistrationModel $registrationModel;
    protected StudentAddressModel $addressModel;
    protected StudentFamilyModel $familyModel;
    protected StudentPeriodicModel $periodicModel;
    protected StudentDocumentModel $documentModel;

    protected array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->exportService       = new ExportService();
        $this->registrationService = new RegistrationService();
        $this->userModel           = new UserModel();
        $this->studentModel        = new StudentModel();
        $this->jalurModel          = new JalurModel();
        $this->registrationModel   = new RegistrationModel();
        $this->addressModel        = new StudentAddressModel();
        $this->familyModel         = new StudentFamilyModel();
        $this->periodicModel       = new StudentPeriodicModel();
        $this->documentModel       = new StudentDocumentModel();

        $this->createdFiles        = [];
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        // Cleanup DB records
        $this->registrationModel->where('1=1')->delete();
        $this->documentModel->where('1=1')->delete();
        $this->periodicModel->where('1=1')->delete();
        $this->familyModel->where('1=1')->delete();
        $this->addressModel->where('1=1')->delete();
        $this->studentModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();
        $this->jalurModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');

        // Cleanup physical files generated during test
        foreach ($this->createdFiles as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        parent::tearDown();
    }

    /**
     * Helper: Buat user + student + full profile + registration record.
     */
    protected function createFullStudentProfile(): int
    {
        $userId = $this->userModel->insert([
            'name'      => 'Export Test User',
            'email'     => 'exportuser' . uniqid() . '@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        $studentId = $this->studentModel->insert([
            'user_id'       => $userId,
            'full_name'     => 'Budiman Export',
            'gender'        => 'L',
            'birth_place'   => 'Bandung',
            'birth_date'    => '2010-08-20',
            'religion'      => 'Islam',
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => '3273012008100002',
            'nisn'          => '0102030405',
        ]);

        // Address
        $this->addressModel->insert([
            'student_id'     => $studentId,
            'street_address' => 'Jl. Merdeka No. 10',
            'rt'             => 3,
            'rw'             => 4,
            'village'        => 'Babakan',
            'subdistrict'    => 'Sumur Bandung',
            'district'       => 'Kota Bandung',
            'province'       => 'Jawa Barat',
            'postal_code'    => '40111',
            'residence_type' => 'Bersama orang tua',
            'distance_km'    => 1.5,
            'transport_mode' => 'Sepeda',
        ]);

        // Families
        $this->familyModel->insert([
            'student_id'   => $studentId,
            'family_type'  => 'ayah',
            'full_name'    => 'Suherman',
            'nik'          => '3273011505700001',
            'birth_place'  => 'Bandung',
            'birth_date'   => '1970-05-15',
            'education'    => 'SMA/Sederajat',
            'occupation'   => 'Wiraswasta',
            'income'       => '2-5 juta',
            'phone_number' => '08122334455',
        ]);

        $this->familyModel->insert([
            'student_id'   => $studentId,
            'family_type'  => 'ibu',
            'full_name'    => 'Siti Aminah',
            'nik'          => '3273012512750002',
            'birth_place'  => 'Garut',
            'birth_date'   => '1975-12-25',
            'education'    => 'Diploma',
            'occupation'   => 'Ibu Rumah Tangga',
            'income'       => 'Tidak bekerja',
            'phone_number' => '08988776655',
        ]);

        // Periodic
        $this->periodicModel->insert([
            'student_id'        => $studentId,
            'height_cm'         => 160,
            'weight_kg'         => 50,
            'has_kip'           => 0,
            'has_kks'           => 0,
            'special_condition' => 'Tidak Ada Kondisi Khusus',
        ]);

        foreach (['kk', 'akta', 'foto'] as $type) {
            $this->documentModel->insert([
                'student_id'    => $studentId,
                'academic_year' => '2026/2027',
                'document_type' => $type,
                'file_name'     => $type . '.jpg',
                'file_path'     => 'uploads/documents/' . $userId . '/' . $type . '.jpg',
                'file_size'     => 1024,
                'mime_type'     => 'image/jpeg',
                'status'        => 'approved',
            ]);
        }

        // Jalur
        $jalurId = $this->jalurModel->insert([
            'name'        => 'Jalur Prestasi',
            'description' => 'Jalur ekspor test',
            'quota'       => 50,
            'is_active'   => 1,
        ]);

        // Finalize
        $this->registrationService->finalize($userId, $jalurId);

        return $studentId;
    }

    // =========================================================================
    // Test Excel Export
    // =========================================================================

    /**
     * Test exportToExcel — ekspor file Excel berhasil.
     *
     * **Validates: Requirements 20.1, 20.2, 20.3**
     */
    public function testExportToExcelGeneratesFileSuccessfully(): void
    {
        $this->createFullStudentProfile();

        $result = $this->exportService->exportToExcel();

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['file_path']);
        $this->assertNotNull($result['filename']);
        $this->assertFileExists($result['file_path']);

        $this->createdFiles[] = $result['file_path'];
    }

    // =========================================================================
    // Test PDF F-PD Export
    // =========================================================================

    /**
     * Test exportToPdfFpd — ekspor Formulir F-PD (Dapodik) berhasil.
     *
     * **Validates: Requirements 21.1, 21.2, 21.3**
     */
    public function testExportToPdfFpdGeneratesFileSuccessfully(): void
    {
        $studentId = $this->createFullStudentProfile();

        $result = $this->exportService->exportToPdfFpd($studentId);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['file_path']);
        $this->assertNotNull($result['filename']);
        $this->assertFileExists($result['file_path']);

        $this->createdFiles[] = $result['file_path'];
    }

    // =========================================================================
    // Test PDF Kartu Peserta Export
    // =========================================================================

    /**
     * Test exportToPdfKartu — ekspor Kartu Peserta berhasil.
     *
     * **Validates: Requirements 22.1, 22.2, 22.3**
     */
    public function testExportToPdfKartuGeneratesFileSuccessfully(): void
    {
        $studentId = $this->createFullStudentProfile();

        $result = $this->exportService->exportToPdfKartu($studentId);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['file_path']);
        $this->assertNotNull($result['filename']);
        $this->assertFileExists($result['file_path']);

        $this->createdFiles[] = $result['file_path'];
    }

    public function testExportToPdfSklGeneratesFileSuccessfully(): void
    {
        $studentId = $this->createFullStudentProfile();

        // Update status pendaftaran menjadi accepted agar lulus syarat SKL
        $this->registrationModel->where('student_id', $studentId)->update(null, ['status' => 'accepted']);

        $result = $this->exportService->exportToPdfSkl($studentId);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['file_path']);
        $this->assertNotNull($result['filename']);
        $this->assertFileExists($result['file_path']);

        $this->createdFiles[] = $result['file_path'];
    }
}
