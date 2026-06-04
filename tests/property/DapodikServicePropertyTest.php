<?php

declare(strict_types=1);

namespace Tests\Property;

use App\Models\StudentModel;
use App\Models\StudentAddressModel;
use App\Models\StudentFamilyModel;
use App\Models\UserModel;
use App\Services\DapodikService;
use Eris\Generator;

use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Property-based tests untuk DapodikService.
 *
 * **Validates: Requirement 23.3**
 */
class DapodikServicePropertyTest extends BasePropertyTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $namespace = 'App';

    private DapodikService $service;
    private StudentModel $studentModel;
    private StudentAddressModel $addressModel;
    private StudentFamilyModel $familyModel;
    private UserModel $userModel;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->service        = new DapodikService();
        $this->studentModel   = new StudentModel();
        $this->addressModel   = new StudentAddressModel();
        $this->familyModel    = new StudentFamilyModel();
        $this->userModel      = new UserModel();

        $this->studentModel->where('1=1')->delete();
        $this->addressModel->where('1=1')->delete();
        $this->familyModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        // Cleanup
        $this->studentModel->where('1=1')->delete();
        $this->addressModel->where('1=1')->delete();
        $this->familyModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    // =========================================================================
    // Property 6: Formula Persentase Kelengkapan Dapodik
    // =========================================================================

    /**
     * Property 6: Formula Persentase Kelengkapan Dapodik
     *
     * `getCompletionPercentage()` harus sama persis dengan `(jumlah_field_terisi / 11) × 100`
     * untuk semua nilai 0–11 field yang terisi.
     *
     * Menggunakan Eris dengan generator `choose(0, 11)` untuk jumlah field yang terisi.
     *
     * **Validates: Requirement 23.3**
     */
    public function testProperty6DapodikCompletionPercentageFormula(): void
    {
        $this->forAll(
            Generator\choose(0, 11) // Jumlah field yang terisi: 0 sampai 11
        )->then(function (int $filledCount) {
            // Buat student dengan jumlah field yang terisi sesuai nilai yang di-generate
            $studentId = $this->createStudentWithFilledFields($filledCount);

            // Hitung persentase menggunakan service
            $percentage = $this->service->getCompletionPercentage($studentId);

            // Hitung persentase yang diharapkan
            $expectedPercentage = ($filledCount / 11) * 100;

            // Verifikasi formula: persentase harus sama persis
            $this->assertEquals(
                $expectedPercentage,
                $percentage,
                "Persentase kelengkapan untuk {$filledCount} field terisi harus " .
                "({$filledCount} / 11) × 100 = {$expectedPercentage}%, " .
                "tetapi mendapat {$percentage}%"
            );

            // Cleanup
            $this->studentModel->delete($studentId);
            $this->addressModel->where('student_id', $studentId)->delete();
            $this->familyModel->where('student_id', $studentId)->delete();
        });
    }

    /**
     * Helper: Buat student dengan jumlah field yang terisi sesuai parameter.
     *
     * 11 field wajib Dapodik:
     *  1. NIK
     *  2. NISN
     *  3. Agama (religion)
     *  4. Jenis tinggal (residence_type)
     *  5. Moda transportasi (transport_mode)
     *  6. Jarak ke sekolah (distance_km)
     *  7. Pendidikan ayah (education)
     *  8. Pendidikan ibu (education)
     *  9. Pekerjaan ayah (occupation)
     * 10. Pekerjaan ibu (occupation)
     * 11. Kebutuhan khusus (special_needs)
     *
     * @param  int $filledCount Jumlah field yang ingin di-fill (0-11)
     * @return int Student ID
     */
    protected function createStudentWithFilledFields(int $filledCount): int
    {
        $fields = [
            'nik',              // 1
            'nisn',             // 2
            'religion',         // 3
            'residence_type',   // 4
            'transport_mode',   // 5
            'distance_km',      // 6
            'education_ayah',   // 7
            'education_ibu',    // 8
            'occupation_ayah',  // 9
            'occupation_ibu',   // 10
            'special_needs',    // 11
        ];

        $userId = $this->userModel->insert([
            'name'      => 'Test User',
            'email'     => 'test_' . uniqid() . '_' . rand(1, 10000) . '@example.com',
            'password'  => 'password123',
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        // Buat student dengan minimal field yang wajib
        $studentId = $this->studentModel->skipValidation(true)->insert([
            'user_id'       => $userId,
            'full_name'     => 'Test Student',
            'gender'        => 'L',
            'birth_place'   => 'Jakarta',
            'birth_date'    => '2010-05-15',
            'religion'      => null,
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => null,
            'nisn'          => null,
            'special_needs' => null,
        ]);

        // Fill fields sesuai jumlah yang di-generate
        for ($i = 0; $i < min($filledCount, 3); $i++) {
            if ($i === 0 && $filledCount >= 1) {
                // Field 1: NIK
                $this->studentModel->update($studentId, ['nik' => '3674051506100001']);
            }
            if ($i === 1 && $filledCount >= 2) {
                // Field 2: NISN
                $this->studentModel->update($studentId, ['nisn' => '0123456789']);
            }
            if ($i === 2 && $filledCount >= 3) {
                // Field 3: Agama
                $this->studentModel->update($studentId, ['religion' => 'Islam']);
            }
        }

        // Fill field 4-6 (address fields) jika diperlukan
        if ($filledCount >= 4 || $filledCount >= 5 || $filledCount >= 6) {
            $this->addressModel->skipValidation(true)->insert([
                'student_id'     => $studentId,
                'address'        => 'Jl. Test',
                'residence_type' => $filledCount >= 4 ? 'Bersama orang tua' : null,
                'transport_mode' => $filledCount >= 5 ? 'Motor' : null,
                'distance_km'    => $filledCount >= 6 ? 2.5 : null,
            ]);
        }

        // Fill field 7-10 (family fields) jika diperlukan
        if ($filledCount >= 7) {
            // Ayah: field 7 (education) dan field 9 (occupation)
            $this->familyModel->insert([
                'student_id'  => $studentId,
                'family_type' => 'ayah',
                'full_name'   => 'Father',
                'education'   => 'S1',
                'occupation'  => $filledCount >= 9 ? 'Karyawan Swasta' : null,
            ]);
        }

        if ($filledCount >= 8) {
            // Ibu: field 8 (education) dan field 10 (occupation)
            $this->familyModel->insert([
                'student_id'  => $studentId,
                'family_type' => 'ibu',
                'full_name'   => 'Mother',
                'education'   => 'SMA',
                'occupation'  => $filledCount >= 10 ? 'Ibu Rumah Tangga' : null,
            ]);
        }

        // Fill field 11 (special_needs) jika diperlukan
        if ($filledCount >= 11) {
            $this->studentModel->update($studentId, ['special_needs' => 'Tidak Ada']);
        }

        return $studentId;
    }
}
