<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\JalurModel;
use App\Models\RegistrationModel;
use App\Models\StudentAddressModel;
use App\Models\StudentAchievementModel;
use App\Services\RankingService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class RankingServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $namespace = 'App';

    protected UserModel $userModel;
    protected StudentModel $studentModel;
    protected JalurModel $jalurModel;
    protected RegistrationModel $registrationModel;
    protected StudentAddressModel $addressModel;
    protected StudentAchievementModel $achievementModel;
    protected RankingService $rankingService;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->userModel        = new UserModel();
        $this->studentModel     = new StudentModel();
        $this->jalurModel       = new JalurModel();
        $this->registrationModel = new RegistrationModel();
        $this->addressModel     = new StudentAddressModel();
        $this->achievementModel = new StudentAchievementModel();
        $this->rankingService   = new RankingService();
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->registrationModel->where('1=1')->delete();
        $this->achievementModel->where('1=1')->delete();
        $this->addressModel->where('1=1')->delete();
        $this->studentModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();
        $this->jalurModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');

        parent::tearDown();
    }

    /**
     * Uji kalkulasi skor jarak, prestasi, total, dan pengurutan hasil ranking.
     */
    public function testRankingCalculationAndWeights(): void
    {
        // 1. Buat Jalur Pendaftaran
        $jalurDomisiliId = $this->jalurModel->insert([
            'name'        => 'Jalur Zonasi / Domisili',
            'description' => 'Zonasi',
            'quota'       => 10,
            'is_active'   => 1,
        ]);

        $jalurPrestasiId = $this->jalurModel->insert([
            'name'        => 'Jalur Prestasi Akademik',
            'description' => 'Prestasi',
            'quota'       => 10,
            'is_active'   => 1,
        ]);

        // 2. Buat Pendaftar A (Domisili, Jarak 1 km, 1 Prestasi tingkat Internasional Juara 1)
        // Jarak 1km => score_distance = 100 - (1 * 10) = 90
        // Prestasi Internasional Juara 1 => level score (50) + rank score (10) = 60
        // Total Domisili (80% Jarak + 20% Prestasi) => 0.80 * 90 + 0.20 * 60 = 72 + 12 = 84
        $userAId = $this->userModel->insert([
            'name' => 'User A', 'email' => 'usera@test.local', 'password' => 'Password123', 'role' => 'pendaftar', 'is_active' => 1
        ]);
        $studentAId = $this->studentModel->insert([
            'user_id' => $userAId, 'full_name' => 'Siswa A Domisili', 'gender' => 'L', 'birth_place' => 'Bandung',
            'birth_date' => '2010-01-01', 'religion' => 'Islam', 'citizenship' => 'WNI', 'family_status' => 'Anak Kandung', 'nik' => '3273010101100001'
        ]);
        $this->addressModel->insert([
            'student_id' => $studentAId, 'address' => 'Jl. A', 'distance_km' => 1.0, 'residence_type' => 'Bersama orang tua'
        ]);
        $this->achievementModel->insert([
            'student_id' => $studentAId, 'achievement_type' => 'akademik', 'competition_name' => 'Olimpiade A',
            'level' => 'internasional', 'rank' => 'juara 1', 'year' => 2025
        ]);
        $this->registrationModel->insert([
            'user_id' => $userAId, 'student_id' => $studentAId, 'jalur_id' => $jalurDomisiliId, 'registration_number' => 'REG-A001', 'academic_year' => '2026/2027', 'status' => 'verified'
        ]);

        // 3. Buat Pendaftar B (Domisili, Jarak 5 km, Tanpa Prestasi)
        // Jarak 5km => score_distance = 100 - (5 * 10) = 50
        // Tanpa Prestasi => score_achievement = 0
        // Total Domisili (80% Jarak + 20% Prestasi) => 0.80 * 50 + 0.20 * 0 = 40
        $userBId = $this->userModel->insert([
            'name' => 'User B', 'email' => 'userb@test.local', 'password' => 'Password123', 'role' => 'pendaftar', 'is_active' => 1
        ]);
        $studentBId = $this->studentModel->insert([
            'user_id' => $userBId, 'full_name' => 'Siswa B Domisili', 'gender' => 'P', 'birth_place' => 'Bandung',
            'birth_date' => '2010-02-02', 'religion' => 'Islam', 'citizenship' => 'WNI', 'family_status' => 'Anak Kandung', 'nik' => '3273010202100002'
        ]);
        $this->addressModel->insert([
            'student_id' => $studentBId, 'address' => 'Jl. B', 'distance_km' => 5.0, 'residence_type' => 'Bersama orang tua'
        ]);
        $this->registrationModel->insert([
            'user_id' => $userBId, 'student_id' => $studentBId, 'jalur_id' => $jalurDomisiliId, 'registration_number' => 'REG-B002', 'academic_year' => '2026/2027', 'status' => 'verified'
        ]);

        // 4. Buat Pendaftar C (Prestasi, Jarak 10 km, 1 Prestasi Nasional Juara 2)
        // Jarak 10km => score_distance = 100 - (10 * 10) = 0
        // Prestasi Nasional Juara 2 => level score (40) + rank score (8) = 48
        // Total Prestasi (30% Jarak + 70% Prestasi) => 0.30 * 0 + 0.70 * 48 = 33.60
        $userCId = $this->userModel->insert([
            'name' => 'User C', 'email' => 'userc@test.local', 'password' => 'Password123', 'role' => 'pendaftar', 'is_active' => 1
        ]);
        $studentCId = $this->studentModel->insert([
            'user_id' => $userCId, 'full_name' => 'Siswa C Prestasi', 'gender' => 'L', 'birth_place' => 'Bandung',
            'birth_date' => '2010-03-03', 'religion' => 'Islam', 'citizenship' => 'WNI', 'family_status' => 'Anak Kandung', 'nik' => '3273010303100003'
        ]);
        $this->addressModel->insert([
            'student_id' => $studentCId, 'address' => 'Jl. C', 'distance_km' => 10.0, 'residence_type' => 'Bersama orang tua'
        ]);
        $this->achievementModel->insert([
            'student_id' => $studentCId, 'achievement_type' => 'akademik', 'competition_name' => 'Olimpiade C',
            'level' => 'nasional', 'rank' => 'juara 2', 'year' => 2025
        ]);
        $this->registrationModel->insert([
            'user_id' => $userCId, 'student_id' => $studentCId, 'jalur_id' => $jalurPrestasiId, 'registration_number' => 'REG-C003', 'academic_year' => '2026/2027', 'status' => 'verified'
        ]);

        // 5. Jalankan Kalkulasi Ranking
        $result = $this->rankingService->calculateAll();
        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['count']);

        // 6. Verifikasi Nilai Terupdate di DB
        $updatedA = $this->studentModel->find($studentAId);
        $updatedB = $this->studentModel->find($studentBId);
        $updatedC = $this->studentModel->find($studentCId);

        $this->assertEquals(90.00, (float)$updatedA['score_distance']);
        $this->assertEquals(60, (int)$updatedA['score_achievement']);
        $this->assertEquals(84.00, (float)$updatedA['score_total']);

        $this->assertEquals(50.00, (float)$updatedB['score_distance']);
        $this->assertEquals(0, (int)$updatedB['score_achievement']);
        $this->assertEquals(40.00, (float)$updatedB['score_total']);

        $this->assertEquals(0.00, (float)$updatedC['score_distance']);
        $this->assertEquals(48, (int)$updatedC['score_achievement']);
        $this->assertEquals(33.60, (float)$updatedC['score_total']);

        // 7. Uji Pengurutan (Default sorting dari applyFilters)
        $this->registrationModel->applyFilters([]);
        $registrants = $this->registrationModel->whereNotIn('registrations.status', ['draft'])->findAll();

        $this->assertCount(3, $registrants);
        // Urutan teratas harus memiliki total_skor tertinggi (Siswa A: 84.00, lalu Siswa B: 40.00, lalu Siswa C: 33.60)
        $this->assertEquals('Siswa A Domisili', $registrants[0]['full_name']);
        $this->assertEquals('Siswa B Domisili', $registrants[1]['full_name']);
        $this->assertEquals('Siswa C Prestasi', $registrants[2]['full_name']);
    }
}
