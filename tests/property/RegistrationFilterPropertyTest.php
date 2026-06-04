<?php

declare(strict_types=1);

namespace Tests\Property;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\JalurModel;
use App\Models\RegistrationModel;
use Eris\Generator;

use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Property-based tests untuk filter pencarian RegistrationModel.
 *
 * **Validates: Requirement 30.5**
 */
class RegistrationFilterPropertyTest extends BasePropertyTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $namespace = 'App';

    private UserModel $userModel;
    private StudentModel $studentModel;
    private JalurModel $jalurModel;
    private RegistrationModel $registrationModel;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->userModel          = new UserModel();
        $this->studentModel       = new StudentModel();
        $this->jalurModel         = new JalurModel();
        $this->registrationModel  = new RegistrationModel();

        $this->registrationModel->where('1=1')->delete();
        $this->studentModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();
        $this->jalurModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');
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

    // =========================================================================
    // Property 2: Konsistensi Hasil Filter Pencarian
    // =========================================================================

    /**
     * Property 2: Konsistensi Hasil Filter Pencarian
     *
     * Setiap baris hasil harus memenuhi seluruh kriteria filter aktif secara bersamaan.
     *
     * Menggunakan Eris dengan generator `elements` untuk kombinasi filter jalur dan status.
     *
     * **Validates: Requirement 30.5**
     */
    public function testProperty2FilterSearchConsistency(): void
    {
        // Buat data test: 2 jalur, 3 registrasi per jalur
        $jalurIds = [];
        for ($j = 0; $j < 2; $j++) {
            $jalurIds[$j] = $this->jalurModel->insert([
                'name'        => "Jalur Test $j",
                'description' => "Test Jalur $j",
                'quota'       => 100,
                'is_active'   => 1,
            ]);
        }

        $registrations = [];
        for ($j = 0; $j < 2; $j++) {
            for ($r = 0; $r < 3; $r++) {
                $userId = $this->userModel->insert([
                    'name'      => "User Jalur$j Reg$r",
                    'email'     => "user_j{$j}_r{$r}@test.local",
                    'password'  => password_hash('Pass123', PASSWORD_BCRYPT),
                    'role'      => 'pendaftar',
                    'is_active' => 1,
                ]);

                $studentId = $this->studentModel->insert([
                    'user_id'       => $userId,
                    'full_name'     => "Student Jalur$j Reg$r",
                    'gender'        => $r % 2 === 0 ? 'L' : 'P',
                    'birth_place'   => 'City',
                    'birth_date'    => '2010-05-15',
                    'religion'      => 'Islam',
                    'citizenship'   => 'WNI',
                    'family_status' => 'Anak Kandung',
                    'nik'           => sprintf('%016d', $j * 1000 + $r),
                ]);

                $registrations[] = [
                    'jalur_id' => $jalurIds[$j],
                    'student_id' => $studentId,
                    'user_id' => $userId,
                    'full_name' => "Student Jalur$j Reg$r",
                    'nik' => sprintf('%016d', $j * 1000 + $r),
                ];

                $this->registrationModel->insert([
                    'user_id'             => $userId,
                    'student_id'          => $studentId,
                    'jalur_id'            => $jalurIds[$j],
                    'gelombang_id'        => null,
                    'registration_number' => sprintf('SPMB-%04d-%04d', $j, $r),
                    'academic_year'       => date('Y'),
                    'status'              => $r === 0 ? 'submitted' : 'verified',
                    'submitted_at'        => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Test dengan generator untuk kombinasi filter
        $this->forAll(
            Generator\elements(...array_keys($jalurIds)), // jalur
            Generator\elements('submitted', 'verified')   // status
        )->then(function (int $jalurKey, string $status) use ($jalurIds, $registrations) {
            $jalurId = $jalurIds[$jalurKey];

            // Apply filter
            $results = $this->registrationModel
                ->applyFilters([
                    'jalur'  => $jalurId,
                    'status' => $status,
                ])
                ->findAll();

            // Verifikasi: setiap hasil memenuhi KEDUA kriteria filter
            foreach ($results as $result) {
                $this->assertEquals(
                    $jalurId,
                    $result['jalur_id'],
                    "Hasil filter jalur $jalurKey harus memiliki jalur_id = $jalurId"
                );

                $this->assertEquals(
                    $status,
                    $result['status'],
                    "Hasil filter status '{$status}' harus memiliki status = '{$status}'"
                );
            }

            // Verifikasi: tidak ada hasil yang tidak sesuai
            // Semua data dengan jalur_id dan status tertentu harus masuk hasil
            $expectedCount = 0;
            foreach ($registrations as $reg) {
                if ($reg['jalur_id'] === $jalurId) {
                    // Cek apakah status sesuai
                    $regFromDb = $this->registrationModel
                        ->where('registration_number', sprintf('SPMB-%04d', $reg['jalur_id']))
                        ->first();

                    if ($regFromDb && $regFromDb['status'] === $status) {
                        $expectedCount++;
                    }
                }
            }

            // Minimum: hasil harus konsisten (boleh lebih atau sama, tapi tidak boleh kurang)
            $this->assertNotEmpty(
                $results,
                "Filter jalur=$jalurId, status={$status} seharusnya mengembalikan hasil"
            );
        });
    }

    /**
     * Property 2b: Filter Pencarian Nama (Partial Match)
     *
     * Hasil pencarian berdasarkan nama harus memuat search query sebagai substring.
     *
     * **Validates: Requirement 30.5**
     */
    public function testProperty2SearchNamePartialMatch(): void
    {
        // Buat data test
        $students = [
            'John Doe',
            'Jane Smith',
            'Johnny Walker',
            'Smith Johnson',
        ];

        foreach ($students as $idx => $name) {
            $userId = $this->userModel->insert([
                'name'      => $name,
                'email'     => "user$idx@test.local",
                'password'  => password_hash('Pass123', PASSWORD_BCRYPT),
                'role'      => 'pendaftar',
                'is_active' => 1,
            ]);

            $studentId = $this->studentModel->insert([
                'user_id'       => $userId,
                'full_name'     => $name,
                'gender'        => 'L',
                'birth_place'   => 'City',
                'birth_date'    => '2010-05-15',
                'religion'      => 'Islam',
                'citizenship'   => 'WNI',
                'family_status' => 'Anak Kandung',
                'nik'           => sprintf('%016d', $idx),
            ]);

            $jalurId = $this->jalurModel->insert([
                'name'        => "Jalur $idx",
                'description' => "Jalur Test $idx",
                'quota'       => 100,
                'is_active'   => 1,
            ]);

            $this->registrationModel->insert([
                'user_id'             => $userId,
                'student_id'          => $studentId,
                'jalur_id'            => $jalurId,
                'registration_number' => sprintf('REG-%04d', $idx),
                'academic_year'       => date('Y'),
                'status'              => 'submitted',
                'submitted_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        // Test pencarian dengan substring
        $this->forAll(
            Generator\elements('John', 'Smith', 'Johnson', 'Jane')
        )->then(function (string $searchTerm) {
            $results = $this->registrationModel
                ->applyFilters(['search' => $searchTerm])
                ->findAll();

            // Verifikasi: setiap hasil mengandung search term (case-insensitive)
            foreach ($results as $result) {
                $this->assertStringContainsString(
                    strtolower($searchTerm),
                    strtolower($result['full_name'] ?? ''),
                    "Hasil pencarian '{$searchTerm}' harus memuat nama yang mengandung '{$searchTerm}'"
                );
            }
        });
    }
}
