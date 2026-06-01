<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\StudentModel;
use App\Models\StudentAddressModel;
use App\Models\StudentFamilyModel;
use App\Services\DapodikService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Unit tests untuk DapodikService.
 *
 * **Validates: Requirements 23.1, 23.2, 23.3, 23.4**
 */
class DapodikServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $namespace = 'App';

    protected DapodikService $service;
    protected StudentModel $studentModel;
    protected StudentAddressModel $addressModel;
    protected StudentFamilyModel $familyModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->service        = new DapodikService();
        $this->studentModel   = new StudentModel();
        $this->addressModel   = new StudentAddressModel();
        $this->familyModel    = new StudentFamilyModel();
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        // Cleanup database
        $this->studentModel->where('1=1')->delete();
        $this->addressModel->where('1=1')->delete();
        $this->familyModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    /**
     * Helper: Buat data siswa lengkap untuk testing.
     */
    protected function createCompleteStudent(): int
    {
        // Buat student
        $studentId = $this->studentModel->insert([
            'user_id'          => 1,
            'full_name'        => 'John Doe',
            'gender'           => 'L',
            'birth_place'      => 'Jakarta',
            'birth_date'       => '2010-05-15',
            'religion'         => 'Islam',
            'citizenship'      => 'WNI',
            'family_status'    => 'Anak Kandung',
            'nik'              => '3674051506100001',
            'nisn'             => '0123456789',
            'special_needs'    => 'Tidak Ada',
        ]);

        // Buat address
        $this->addressModel->insert([
            'student_id'      => $studentId,
            'address'         => 'Jl. Merdeka 123',
            'residence_type'  => 'Bersama orang tua',
            'distance_km'     => 2.5,
            'transport_mode'  => 'Motor',
        ]);

        // Buat father
        $this->familyModel->insert([
            'student_id'  => $studentId,
            'family_type' => 'ayah',
            'full_name'   => 'Budi Santoso',
            'education'   => 'S1',
            'occupation'  => 'Karyawan Swasta',
        ]);

        // Buat mother
        $this->familyModel->insert([
            'student_id'  => $studentId,
            'family_type' => 'ibu',
            'full_name'   => 'Siti Nurhaliza',
            'education'   => 'SMA',
            'occupation'  => 'Ibu Rumah Tangga',
        ]);

        return $studentId;
    }

    // =========================================================================
    // Test Completion Percentage
    // =========================================================================

    /**
     * Test persentase kelengkapan dengan semua field terisi = 100%.
     *
     * **Validates: Requirement 23.3**
     */
    public function testCompletionPercentageWhenAllFieldsFilled(): void
    {
        $studentId = $this->createCompleteStudent();

        $percentage = $this->service->getCompletionPercentage($studentId);

        $this->assertEquals(100.0, $percentage);
    }

    /**
     * Test persentase kelengkapan dengan beberapa field kosong.
     */
    public function testCompletionPercentageWithPartialFields(): void
    {
        // Student dengan beberapa field saja
        $studentId = $this->studentModel->skipValidation(true)->insert([
            'user_id'       => 2,
            'full_name'     => 'Jane Doe',
            'gender'        => 'P',
            'birth_place'   => 'Bandung',
            'birth_date'    => '2010-06-20',
            'religion'      => 'Kristen',
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => '3674051506100002',
            // NISN kosong
            // special_needs = null
        ]);

        $percentage = $this->service->getCompletionPercentage($studentId);

        // Seharusnya persentase < 100%
        $this->assertLessThan(100.0, $percentage);
        $this->assertGreaterThan(0, $percentage);
    }

    /**
     * Test persentase kelengkapan dengan minimal field = 0%.
     */
    public function testCompletionPercentageWhenNoFieldsFilled(): void
    {
        // Student dengan data minimal
        $studentId = $this->studentModel->skipValidation(true)->insert([
            'user_id'       => 3,
            'full_name'     => 'Empty Student',
            'gender'        => 'L',
            'birth_place'   => 'Unknown',
            'birth_date'    => '2010-05-15',
            'religion'      => null,
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => null,
            'nisn'          => null,
            'special_needs' => null,
        ]);

        $percentage = $this->service->getCompletionPercentage($studentId);

        // Hanya beberapa field wajib yang terisi (user_id, full_name, gender, citizenship)
        $this->assertLessThan(50.0, $percentage);
    }

    // =========================================================================
    // Test Missing Fields
    // =========================================================================

    /**
     * Test getMissingFields — mengembalikan array field yang belum terisi.
     *
     * **Validates: Requirement 23.2**
     */
    public function testGetMissingFieldsReturnsMissingFields(): void
    {
        $studentId = $this->studentModel->skipValidation(true)->insert([
            'user_id'       => 4,
            'full_name'     => 'Test Student',
            'gender'        => 'L',
            'birth_place'   => 'City',
            'birth_date'    => '2010-05-15',
            'religion'      => null,
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => null,
            'nisn'          => null,
            'special_needs' => null,
        ]);

        $missingFields = $this->service->getMissingFields($studentId);

        // Harus berisi field-field yang belum terisi
        $this->assertNotEmpty($missingFields);
        $this->assertContains('NIK', $missingFields);
        $this->assertContains('NISN', $missingFields);
        $this->assertContains('Agama', $missingFields);
    }

    /**
     * Test getMissingFields — kosong jika semua field terisi.
     */
    public function testGetMissingFieldsEmptyWhenAllFilled(): void
    {
        $studentId = $this->createCompleteStudent();

        $missingFields = $this->service->getMissingFields($studentId);

        $this->assertEmpty($missingFields);
    }

    // =========================================================================
    // Test Ready for Dapodik
    // =========================================================================

    /**
     * Test isReadyForDapodik — true jika 100% terisi.
     *
     * **Validates: Requirement 23.4**
     */
    public function testIsReadyForDapodikReturnsTrueWhenComplete(): void
    {
        $studentId = $this->createCompleteStudent();

        $isReady = $this->service->isReadyForDapodik($studentId);

        $this->assertTrue($isReady);
    }

    /**
     * Test isReadyForDapodik — false jika belum 100% terisi.
     */
    public function testIsReadyForDapodikReturnsFalseWhenIncomplete(): void
    {
        $studentId = $this->studentModel->skipValidation(true)->insert([
            'user_id'       => 5,
            'full_name'     => 'Incomplete Student',
            'gender'        => 'P',
            'birth_place'   => 'City',
            'birth_date'    => '2010-07-01',
            'religion'      => 'Islam',
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => null, // Belum diisi
            'nisn'          => null,
            'special_needs' => null,
        ]);

        $isReady = $this->service->isReadyForDapodik($studentId);

        $this->assertFalse($isReady);
    }

    // =========================================================================
    // Test Update Dapodik Status
    // =========================================================================

    /**
     * Test updateDapodikStatus — memperbarui kolom di database.
     *
     * **Validates: Requirements 23.1, 23.3, 23.4**
     */
    public function testUpdateDapodikStatusSavesToDatabase(): void
    {
        $studentId = $this->createCompleteStudent();

        // Call update
        $success = $this->service->updateDapodikStatus($studentId);

        $this->assertTrue($success);

        // Verify data di database
        $student = $this->studentModel->find($studentId);
        $this->assertEquals(100.0, $student['dapodik_percentage']);
        $this->assertTrue((bool) $student['is_dapodik_ready']);
    }

    /**
     * Test updateDapodikStatus dengan data parsial.
     */
    public function testUpdateDapodikStatusWithPartialData(): void
    {
        $studentId = $this->studentModel->skipValidation(true)->insert([
            'user_id'       => 6,
            'full_name'     => 'Partial Student',
            'gender'        => 'L',
            'birth_place'   => 'City',
            'birth_date'    => '2010-08-01',
            'religion'      => 'Islam',
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => '1234567890123456',
            'nisn'          => null, // Kosong
            'special_needs' => 'Tidak Ada',
        ]);

        // Call update
        $this->service->updateDapodikStatus($studentId);

        // Verify percentage < 100
        $student = $this->studentModel->find($studentId);
        $this->assertLessThan(100.0, $student['dapodik_percentage']);
        $this->assertFalse((bool) $student['is_dapodik_ready']);
    }
}
