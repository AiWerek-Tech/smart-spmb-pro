<?php

namespace App\Services;

use App\Models\StudentModel;
use App\Models\StudentAddressModel;
use App\Models\StudentAchievementModel;
use App\Models\RegistrationModel;
use App\Models\JalurModel;
use App\Services\AcademicYearService;

/**
 * RankingService — Layanan kalkulasi ranking otomatis bagi pendaftar.
 *
 * Menghitung komponen skor:
 * - score_distance (0 - 100 berdasarkan distance_km)
 * - score_achievement (total poin prestasi)
 * - score_total (bobot terkalibrasi berdasarkan jalur pendaftaran)
 */
class RankingService
{
    protected StudentModel $studentModel;
    protected StudentAddressModel $addressModel;
    protected StudentAchievementModel $achievementModel;
    protected RegistrationModel $registrationModel;
    protected JalurModel $jalurModel;
    protected AcademicYearService $academicYearService;

    public function __construct()
    {
        $this->studentModel     = new StudentModel();
        $this->addressModel     = new StudentAddressModel();
        $this->achievementModel = new StudentAchievementModel();
        $this->registrationModel = new RegistrationModel();
        $this->jalurModel       = new JalurModel();
        $this->academicYearService = new AcademicYearService();
    }

    /**
     * Jalankan kalkulasi ranking untuk semua pendaftar non-draft.
     *
     * @return array Status dan jumlah pendaftar yang dikalkulasi.
     */
    public function calculateAll(): array
    {
        $db = \Config\Database::connect();
        $db->transStart();
        $academicYear = $this->academicYearService->activeYear();

        // Ambil pendaftaran tahun aktif yang statusnya bukan 'draft' beserta info jalur
        $registrations = $this->registrationModel
            ->select('registrations.student_id, registrations.jalur_id, jalur.name as jalur_name')
            ->join('jalur', 'jalur.id = registrations.jalur_id')
            ->where('registrations.academic_year', $academicYear)
            ->whereNotIn('registrations.status', ['draft'])
            ->findAll();

        $count = 0;

        foreach ($registrations as $reg) {
            $studentId = (int) $reg['student_id'];
            $jalurName = $reg['jalur_name'];

            // 1. Hitung score_distance
            $address = $this->addressModel->findByStudentId($studentId);
            $scoreDistance = 0.00;
            if ($address !== null && isset($address['distance_km'])) {
                $distanceKm = (float) $address['distance_km'];
                $scoreDistance = max(0.00, 100.00 - ($distanceKm * 10.00));
            }

            // 2. Hitung score_achievement
            $achievements = $this->achievementModel->findByStudentId($studentId);
            $scoreAchievement = 0;
            foreach ($achievements as $ach) {
                $levelScore = 0;
                $level = strtolower($ach['level'] ?? '');
                switch ($level) {
                    case 'kecamatan':
                        $levelScore = 10;
                        break;
                    case 'kabupaten':
                        $levelScore = 20;
                        break;
                    case 'provinsi':
                        $levelScore = 30;
                        break;
                    case 'nasional':
                        $levelScore = 40;
                        break;
                    case 'internasional':
                        $levelScore = 50;
                        break;
                }

                $rankScore = 0;
                $rank = strtolower($ach['rank'] ?? '');
                switch ($rank) {
                    case 'harapan':
                        $rankScore = 4;
                        break;
                    case 'juara 3':
                        $rankScore = 6;
                        break;
                    case 'juara 2':
                        $rankScore = 8;
                        break;
                    case 'juara 1':
                        $rankScore = 10;
                        break;
                }

                $scoreAchievement += ($levelScore + $rankScore);
            }

            // 3. Hitung score_total berdasarkan bobot jalur
            // Domisili = 80/20, Prestasi = 30/70, others = 50/50
            $normalizedJalur = strtolower($jalurName);
            if (strpos($normalizedJalur, 'domisili') !== false || strpos($normalizedJalur, 'zonasi') !== false) {
                $scoreTotal = ($scoreDistance * 0.80) + ($scoreAchievement * 0.20);
            } elseif (strpos($normalizedJalur, 'prestasi') !== false) {
                $scoreTotal = ($scoreDistance * 0.30) + ($scoreAchievement * 0.70);
            } else {
                $scoreTotal = ($scoreDistance * 0.50) + ($scoreAchievement * 0.50);
            }

            // 4. Update data student
            $this->studentModel->update($studentId, [
                'score_distance'    => $scoreDistance,
                'score_achievement' => $scoreAchievement,
                'score_total'       => $scoreTotal,
            ]);

            $count++;
        }

        $db->transComplete();

        return [
            'success'       => $db->transStatus(),
            'count'         => $count,
            'academic_year' => $academicYear,
        ];
    }
}
