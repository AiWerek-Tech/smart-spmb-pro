<?php

declare(strict_types=1);

namespace Tests\Property;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\JalurModel;
use App\Models\RegistrationModel;
use App\Services\RegistrationService;
use Eris\Generator;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Property-based tests untuk RegistrationService.
 *
 * **Validates: Requirement 29.3**
 */
class RegistrationServicePropertyTest extends BasePropertyTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $namespace = 'App';

    private RegistrationService $service;
    private UserModel $userModel;
    private StudentModel $studentModel;
    private JalurModel $jalurModel;
    private RegistrationModel $registrationModel;

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
    // Property 1: Keunikan Nomor Pendaftaran
    // =========================================================================

    /**
     * Property 1: Keunikan Nomor Pendaftaran
     *
     * Tidak ada dua nomor pendaftaran yang identik dalam satu tahun ajaran.
     *
     * Menggunakan Eris dengan generator `choose(2, 50)` untuk jumlah pendaftar.
     *
     * **Validates: Requirement 29.3**
     */
    public function testProperty1RegistrationNumberUniqueness(): void
    {
        $this->forAll(
            Generator\choose(2, 50) // Jumlah pendaftar: 2 sampai 50
        )->then(function (int $registrantCount) {
            $this->service->resetSequences();
            $academicYear = date('Y');
            $regNumbers   = [];

            // Generate nomor pendaftaran sejumlah registrantCount
            for ($i = 0; $i < $registrantCount; $i++) {
                $regNumber = $this->service->generateRegistrationNumber($academicYear);
                $regNumbers[] = $regNumber;
            }

            // Verifikasi: tidak ada duplikat
            $uniqueNumbers = array_unique($regNumbers);

            $this->assertEquals(
                count($regNumbers),
                count($uniqueNumbers),
                "Seharusnya ada {$registrantCount} nomor pendaftaran unik, " .
                "tetapi hanya " . count($uniqueNumbers) . " yang unik. Duplikat: " .
                implode(', ', array_diff_key($regNumbers, $uniqueNumbers))
            );

            // Verifikasi: semua nomor memiliki format benar (SPMB-YYYY-NNNN)
            foreach ($regNumbers as $regNumber) {
                $this->assertMatchesRegularExpression(
                    '/^SPMB-' . $academicYear . '-\d{4}$/',
                    $regNumber,
                    "Nomor pendaftaran '{$regNumber}' tidak sesuai format SPMB-{$academicYear}-NNNN"
                );
            }

            // Verifikasi: urutan increment
            $sequences = [];
            foreach ($regNumbers as $regNumber) {
                preg_match('/SPMB-\d+-(\d+)$/', $regNumber, $m);
                $sequences[] = intval($m[1]);
            }

            // Harus dalam urutan increment 1, 2, 3, ...
            $expectedSequences = range(1, $registrantCount);
            sort($sequences);

            $this->assertEquals(
                $expectedSequences,
                $sequences,
                "Nomor pendaftaran harus memiliki urutan increment sequential"
            );
        });
    }

    /**
     * Property 1b: Format Nomor Pendaftaran Konsisten
     *
     * Setiap nomor pendaftaran yang di-generate harus memiliki format SPMB-[TAHUN]-[NOMOR_URUT_4_DIGIT].
     *
     * **Validates: Requirement 29.3**
     */
    public function testProperty1RegistrationNumberFormatConsistency(): void
    {
        $this->forAll(
            Generator\choose(1, 100) // Jumlah nomor yang di-generate
        )->then(function (int $count) {
            $this->service->resetSequences();
            $academicYear = date('Y');

            for ($i = 0; $i < $count; $i++) {
                $regNumber = $this->service->generateRegistrationNumber($academicYear);

                // Verifikasi format
                $this->assertMatchesRegularExpression(
                    '/^SPMB-\d{4}-\d{4}$/',
                    $regNumber,
                    "Format nomor pendaftaran '{$regNumber}' tidak sesuai SPMB-YYYY-NNNN"
                );

                // Verifikasi tahun akurat
                $this->assertStringContainsString(
                    "SPMB-{$academicYear}",
                    $regNumber,
                    "Tahun dalam nomor pendaftaran '{$regNumber}' tidak sesuai tahun akademik {$academicYear}"
                );

                // Verifikasi urutan adalah 4 digit (0000-9999)
                preg_match('/SPMB-\d+-(\d{4})$/', $regNumber, $match);
                $sequence = intval($match[1]);

                $this->assertGreaterThanOrEqual(
                    0,
                    $sequence,
                    "Urutan nomor pendaftaran harus >= 0"
                );
                $this->assertLessThanOrEqual(
                    9999,
                    $sequence,
                    "Urutan nomor pendaftaran harus <= 9999 (4 digit)"
                );
            }
        });
    }
}
