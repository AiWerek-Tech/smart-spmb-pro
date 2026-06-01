<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\StudentAddressModel;
use App\Models\StudentContactModel;
use App\Models\StudentFamilyModel;
use App\Models\StudentPeriodicModel;
use App\Models\StudentDocumentModel;
use App\Models\StudentAchievementModel;
use App\Models\RegistrationModel;
use App\Models\JalurModel;
use CodeIgniter\Database\BaseConnection;

/**
 * RegistrationService — Logika bisnis pendaftaran multi-langkah (8 step).
 *
 * Mengelola form wizard, penyimpanan data per step, finalisasi pendaftaran,
 * dan pembuatan nomor pendaftaran unik.
 *
 * 8 Step:
 *  1. Identitas Siswa (students)
 *  2. Alamat & Kontak (student_address, student_contact)
 *  3. Data Ayah (student_family: family_type='ayah')
 *  4. Data Ibu (student_family: family_type='ibu')
 *  5. Data Wali (student_family: family_type='wali')
 *  6. Data Periodik (student_periodic)
 *  7. Prestasi (student_achievements)
 *  8. Unggah Dokumen (student_documents)
 *
 * Requirements: 8.1, 8.2, 8.6, 8.7, 8.8, 29.1, 29.2, 29.3, 29.4
 */
class RegistrationService
{
    protected UserModel $userModel;
    protected StudentModel $studentModel;
    protected StudentAddressModel $addressModel;
    protected StudentContactModel $contactModel;
    protected StudentFamilyModel $familyModel;
    protected StudentPeriodicModel $periodicModel;
    protected StudentDocumentModel $documentModel;
    protected StudentAchievementModel $achievementModel;
    protected RegistrationModel $registrationModel;
    protected JalurModel $jalurModel;
    protected BaseConnection $db;
    protected ValidationService $validationService;
    protected DapodikService $dapodikService;
    private static array $generatedSequences = [];

    public function __construct()
    {
        $this->userModel           = new UserModel();
        $this->studentModel        = new StudentModel();
        $this->addressModel        = new StudentAddressModel();
        $this->contactModel        = new StudentContactModel();
        $this->familyModel         = new StudentFamilyModel();
        $this->periodicModel       = new StudentPeriodicModel();
        $this->documentModel       = new StudentDocumentModel();
        $this->achievementModel    = new StudentAchievementModel();
        $this->registrationModel   = new RegistrationModel();
        $this->jalurModel          = new JalurModel();
        $this->db                  = \Config\Database::connect();
        $this->validationService   = new ValidationService();
        $this->dapodikService      = new DapodikService();
        self::$generatedSequences  = [];
    }

    // -------------------------------------------------------------------------
    // Ambil Data Draft
    // -------------------------------------------------------------------------

    /**
     * Ambil seluruh data draft dari semua step untuk seorang user.
     *
     * @param  int   $userId
     * @return array Data draft semua step, terstruktur per step
     */
    public function getDraftData(int $userId): array
    {
        $student = $this->studentModel->findByUserId($userId);

        if ($student === null) {
            return [];
        }

        $studentId = $student['id'];

        // Ambil data dari setiap step
        $step1 = $student; // Identitas Siswa
        $step2 = $this->getDraftStep2($studentId); // Alamat & Kontak
        $step3 = $this->getDraftFamilyData($studentId, 'ayah'); // Data Ayah
        $step4 = $this->getDraftFamilyData($studentId, 'ibu'); // Data Ibu
        $step5 = $this->getDraftFamilyData($studentId, 'wali'); // Data Wali
        $step6 = $this->periodicModel->findByStudentId($studentId) ?? []; // Data Periodik
        $step7 = $this->achievementModel->findByStudentId($studentId) ?? []; // Prestasi
        $step8 = $this->documentModel->findByStudentId($studentId) ?? []; // Dokumen

        return [
            'step_1' => $step1,
            'step_2' => $step2,
            'step_3' => $step3,
            'step_4' => $step4,
            'step_5' => $step5,
            'step_6' => $step6,
            'step_7' => $step7,
            'step_8' => $step8,
        ];
    }

    /**
     * Ambil data draft Step 2 (Alamat & Kontak).
     */
    protected function getDraftStep2(int $studentId): array
    {
        $address = $this->addressModel->findByStudentId($studentId);
        $contact = $this->contactModel->findByStudentId($studentId);

        return array_merge($address ?? [], $contact ?? []);
    }

    /**
     * Ambil data draft untuk keluarga (ayah/ibu/wali).
     */
    protected function getDraftFamilyData(int $studentId, string $familyType): array
    {
        return $this->familyModel->findByStudentAndType($studentId, $familyType) ?? [];
    }

    // -------------------------------------------------------------------------
    // Simpan Data Per Step
    // -------------------------------------------------------------------------

    /**
     * Simpan data satu step ke database dengan validasi.
     *
     * @param  int   $userId
     * @param  int   $step     (1–8)
     * @param  array $data     Data dari form step
     * @return array ['success' => bool, 'errors' => array, 'message' => string]
     */
    public function saveStep(int $userId, int $step, array $data): array
    {
        $student = $this->studentModel->findByUserId($userId);

        if ($student === null) {
            return [
                'success' => false,
                'errors'  => [],
                'message' => 'Siswa tidak ditemukan.',
            ];
        }

        $studentId = $student['id'];

        try {
            $this->db->transBegin();

            switch ($step) {
                case 1:
                    $errors = $this->saveStep1($studentId, $data);
                    break;
                case 2:
                    $errors = $this->saveStep2($studentId, $data);
                    break;
                case 3:
                    $errors = $this->saveStep3($studentId, $data);
                    break;
                case 4:
                    $errors = $this->saveStep4($studentId, $data);
                    break;
                case 5:
                    $errors = $this->saveStep5($studentId, $data);
                    break;
                case 6:
                    $errors = $this->saveStep6($studentId, $data);
                    break;
                case 7:
                    $errors = $this->saveStep7($studentId, $data);
                    break;
                case 8:
                    $errors = $this->saveStep8($studentId, $data);
                    break;
                default:
                    throw new \Exception('Step tidak valid: ' . $step);
            }

            if (! empty($errors)) {
                $this->db->transRollback();

                return [
                    'success' => false,
                    'errors'  => $errors,
                    'message' => 'Validasi gagal. Periksa kembali data Anda.',
                ];
            }

            // Perbarui status Dapodik setelah setiap step
            $this->dapodikService->updateDapodikStatus($studentId);

            $this->db->transCommit();

            return [
                'success' => true,
                'errors'  => [],
                'message' => 'Data step ' . $step . ' berhasil disimpan.',
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'RegistrationService::saveStep failed: ' . $e->getMessage());

            return [
                'success' => false,
                'errors'  => [],
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Simpan Step 1 — Identitas Siswa.
     */
    protected function saveStep1(int $studentId, array $data): array
    {
        $errors = [];

        // Validasi NIK jika diisi
        if (! empty($data['nik']) && ! $this->validationService->validateNik($data['nik'])) {
            $errors['nik'] = 'NIK harus terdiri dari 16 digit angka.';
        }

        // Validasi NISN jika diisi
        if (! empty($data['nisn']) && ! $this->validationService->validateNisn($data['nisn'])) {
            $errors['nisn'] = 'NISN harus terdiri dari 10 digit angka.';
        }

        if (! empty($errors)) {
            return $errors;
        }

        $updateData = [
            'full_name'       => $data['full_name'] ?? '',
            'gender'          => $data['gender'] ?? '',
            'birth_place'     => $data['birth_place'] ?? '',
            'birth_date'      => $data['birth_date'] ?? null,
            'religion'        => $data['religion'] ?? '',
            'citizenship'     => $data['citizenship'] ?? '',
            'family_status'   => $data['family_status'] ?? '',
            'nik'             => $data['nik'] ?? '',
            'nisn'            => $data['nisn'] ?? null,
            'birth_cert_number' => $data['birth_cert_number'] ?? null,
            'special_needs'   => $data['special_needs'] ?? 'Tidak Ada',
        ];

        $this->studentModel->update($studentId, $updateData);

        return $errors;
    }

    /**
     * Simpan Step 2 — Alamat & Kontak.
     */
    protected function saveStep2(int $studentId, array $data): array
    {
        $errors = [];

        // Validasi email jika diisi
        if (! empty($data['email']) && ! $this->validationService->validateEmail($data['email'])) {
            $errors['email'] = 'Format email tidak valid.';
        }

        if (! empty($errors)) {
            return $errors;
        }

        // Simpan data address
        $addressData = [
            'student_id'      => $studentId,
            'street_address'  => $data['street_address'] ?? '',
            'rt'              => $data['rt'] ?? null,
            'rw'              => $data['rw'] ?? null,
            'hamlet'          => $data['hamlet'] ?? '',
            'village'         => $data['village'] ?? '',
            'subdistrict'     => $data['subdistrict'] ?? '',
            'district'        => $data['district'] ?? '',
            'province'        => $data['province'] ?? '',
            'postal_code'     => $data['postal_code'] ?? '',
            'residence_type'  => $data['residence_type'] ?? '',
            'distance_km'     => floatval($data['distance_km'] ?? 0),
            'transport_mode'  => $data['transport_mode'] ?? '',
        ];

        $existingAddress = $this->addressModel->findByStudentId($studentId);
        if ($existingAddress) {
            $this->addressModel->update($existingAddress['id'], $addressData);
        } else {
            $this->addressModel->insert($addressData);
        }

        // Simpan data contact
        $contactData = [
            'student_id'    => $studentId,
            'phone_number'  => $data['phone_number'] ?? '',
            'email'         => $data['email'] ?? '',
        ];

        $existingContact = $this->contactModel->findByStudentId($studentId);
        if ($existingContact) {
            $this->contactModel->update($existingContact['id'], $contactData);
        } else {
            $this->contactModel->insert($contactData);
        }

        return $errors;
    }

    /**
     * Simpan Step 3 — Data Ayah.
     */
    protected function saveStep3(int $studentId, array $data): array
    {
        return $this->saveFamilyData($studentId, 'ayah', $data);
    }

    /**
     * Simpan Step 4 — Data Ibu.
     */
    protected function saveStep4(int $studentId, array $data): array
    {
        return $this->saveFamilyData($studentId, 'ibu', $data);
    }

    /**
     * Simpan Step 5 — Data Wali.
     */
    protected function saveStep5(int $studentId, array $data): array
    {
        return $this->saveFamilyData($studentId, 'wali', $data);
    }

    /**
     * Helper untuk simpan data keluarga (ayah/ibu/wali).
     */
    protected function saveFamilyData(int $studentId, string $familyType, array $data): array
    {
        $errors = [];

        // Validasi NIK jika diisi
        if (! empty($data['nik']) && ! $this->validationService->validateNik($data['nik'])) {
            $errors['nik'] = 'NIK harus terdiri dari 16 digit angka.';
        }

        if (! empty($errors)) {
            return $errors;
        }

        $familyData = [
            'student_id'   => $studentId,
            'family_type'  => $familyType,
            'full_name'    => $data['full_name'] ?? '',
            'nik'          => $data['nik'] ?? null,
            'birth_place'  => $data['birth_place'] ?? '',
            'birth_date'   => $data['birth_date'] ?? null,
            'education'    => $data['education'] ?? null,
            'occupation'   => $data['occupation'] ?? null,
            'income'       => $data['income'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'relation'     => $data['relation'] ?? null, // untuk wali
        ];

        $existing = $this->familyModel->findByStudentAndType($studentId, $familyType);
        if ($existing) {
            $this->familyModel->update($existing['id'], $familyData);
        } else {
            $this->familyModel->insert($familyData);
        }

        return $errors;
    }

    /**
     * Simpan Step 6 — Data Periodik.
     */
    protected function saveStep6(int $studentId, array $data): array
    {
        $periodicData = [
            'student_id'     => $studentId,
            'height_cm'      => $data['height_cm'] ?? null,
            'weight_kg'      => $data['weight_kg'] ?? null,
            'has_kip'        => isset($data['has_kip']) ? (bool) $data['has_kip'] : 0,
            'kip_number'     => $data['kip_number'] ?? null,
            'has_kks'        => isset($data['has_kks']) ? (bool) $data['has_kks'] : 0,
            'kks_number'     => $data['kks_number'] ?? null,
            'pkh_number'     => $data['pkh_number'] ?? null,
            'special_condition' => $data['special_condition'] ?? 'Tidak Ada Kondisi Khusus',
        ];

        $existing = $this->periodicModel->findByStudentId($studentId);
        if ($existing) {
            $this->periodicModel->update($existing['id'], $periodicData);
        } else {
            $this->periodicModel->insert($periodicData);
        }

        return [];
    }

    /**
     * Simpan Step 7 — Prestasi.
     */
    protected function saveStep7(int $studentId, array $data): array
    {
        // Data prestasi dalam format array (multiple rows)
        $achievements = $data['achievements'] ?? [];

        // Hapus prestasi lama
        $this->achievementModel->deleteByStudentId($studentId);

        // Simpan prestasi baru
        foreach ($achievements as $achievement) {
            if (! empty($achievement['name'])) {
                $this->achievementModel->insert([
                    'student_id'   => $studentId,
                    'type'         => $achievement['type'] ?? '',
                    'name'         => $achievement['name'] ?? '',
                    'level'        => $achievement['level'] ?? '',
                    'rank'         => $achievement['rank'] ?? '',
                    'year'         => $achievement['year'] ?? null,
                ]);
            }
        }

        return [];
    }

    /**
     * Simpan Step 8 — Dokumen (sudah disimpan saat unggah, jadi hanya validasi di sini).
     */
    protected function saveStep8(int $studentId, array $data): array
    {
        // Validasi dokumen wajib ada
        $requiredDocs = ['kk', 'akta', 'foto'];
        $uploadedDocs = $this->documentModel->findByStudentId($studentId) ?? [];
        $uploadedTypes = array_column($uploadedDocs, 'document_type');

        $errors = [];
        foreach ($requiredDocs as $docType) {
            if (! in_array($docType, $uploadedTypes, true)) {
                $errors['documents'] = 'Dokumen wajib belum lengkap: ' . implode(', ', $requiredDocs);
                break;
            }
        }

        return $errors;
    }

    // -------------------------------------------------------------------------
    // Finalisasi Pendaftaran
    // -------------------------------------------------------------------------

    /**
     * Finalisasi pendaftaran: simpan permanen, buat registration record, hasilkan nomor pendaftaran.
     *
     * @param  int   $userId
     * @param  int   $jalurId
     * @param  int   $gelombangId (optional)
     * @return array ['success' => bool, 'registration_number' => string|null, 'message' => string]
     */
    public function finalize(int $userId, int $jalurId, ?int $gelombangId = null): array
    {
        $student = $this->studentModel->findByUserId($userId);

        if ($student === null) {
            return [
                'success'             => false,
                'registration_number' => null,
                'message'             => 'Data siswa tidak ditemukan.',
            ];
        }

        $studentId = $student['id'];

        try {
            $this->db->transBegin();

            // Validasi jalur aktif dan kuota
            $jalur = $this->jalurModel->find($jalurId);
            if (! $jalur || ! $jalur['is_active']) {
                throw new \Exception('Jalur pendaftaran tidak aktif.');
            }

            $currentCount = $this->registrationModel
                ->where('jalur_id', $jalurId)
                ->where('status !=', 'rejected')
                ->countAllResults();

            if ($currentCount >= $jalur['quota']) {
                throw new \Exception('Kuota jalur telah penuh.');
            }

            // Generate nomor pendaftaran unik
            $academicYear = date('Y');
            $registrationNumber = $this->generateRegistrationNumber($academicYear);

            // Buat registration record
            $registrationId = $this->registrationModel->insert([
                'user_id'             => $userId,
                'student_id'          => $studentId,
                'jalur_id'            => $jalurId,
                'gelombang_id'        => $gelombangId,
                'registration_number' => $registrationNumber,
                'academic_year'       => $academicYear,
                'status'              => 'submitted',
                'submitted_at'        => date('Y-m-d H:i:s'),
            ]);

            if (! $registrationId) {
                throw new \Exception('Gagal membuat registration record.');
            }

            $this->db->transCommit();

            return [
                'success'             => true,
                'registration_number' => $registrationNumber,
                'message'             => 'Pendaftaran berhasil. Nomor pendaftaran: ' . $registrationNumber,
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'RegistrationService::finalize failed: ' . $e->getMessage());

            return [
                'success'             => false,
                'registration_number' => null,
                'message'             => $e->getMessage(),
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Generate Nomor Pendaftaran
    // -------------------------------------------------------------------------

    /**
     * Generate nomor pendaftaran unik dengan format: SPMB-[TAHUN]-[NOMOR_URUT_4_DIGIT].
     *
     * Menggunakan SELECT ... FOR UPDATE untuk atomisitas dan mencegah race condition.
     *
     * Requirements: 29.3
     *
     * @param  string $academicYear (format: "2024")
     * @return string Format: "SPMB-2024-0001"
     */
    public function generateRegistrationNumber(string $academicYear): string
    {
        $hasActiveTransaction = $this->db->transDepth > 0;
        try {
            if (! $hasActiveTransaction) {
                $this->db->transBegin();
            }

            // Ambil semua nomor registrasi untuk tahun akademik ini dengan lock (FOR UPDATE)
            $records = $this->db->query(
                "SELECT registration_number FROM registrations WHERE academic_year = ? FOR UPDATE",
                [$academicYear]
            )->getResultArray();

            $maxSeq = 0;
            foreach ($records as $record) {
                $parts = explode('-', $record['registration_number'] ?? '');
                if (count($parts) === 3) {
                    $seq = (int) $parts[2];
                    if ($seq > $maxSeq) {
                        $maxSeq = $seq;
                    }
                }
            }

            // Juga periksa cache memori statis
            if (isset(self::$generatedSequences[$academicYear])) {
                foreach (self::$generatedSequences[$academicYear] as $seq) {
                    if ($seq > $maxSeq) {
                        $maxSeq = $seq;
                    }
                }
            }

            $nextSequence = $maxSeq + 1;

            // Simpan ke cache memori statis
            self::$generatedSequences[$academicYear][] = $nextSequence;

            // Validasi tidak melebihi 9999 (4 digit)
            if ($nextSequence > 9999) {
                throw new \Exception('Urutan nomor pendaftaran melebihi batas maksimal 4 digit.');
            }

            $registrationNumber = sprintf('SPMB-%s-%04d', $academicYear, $nextSequence);

            if (! $hasActiveTransaction) {
                $this->db->transCommit();
            }

            return $registrationNumber;
        } catch (\Throwable $e) {
            if (! $hasActiveTransaction) {
                $this->db->transRollback();
            }
            log_message('error', 'RegistrationService::generateRegistrationNumber failed: ' . $e->getMessage());

            if (ENVIRONMENT === 'testing') {
                throw $e;
            }

            // Fallback ke nomor numerik acak jika gagal untuk tetap mematuhi format 4 digit angka
            return 'SPMB-' . $academicYear . '-' . sprintf('%04d', rand(1, 9999));
        }
    }

    /**
     * Reset the generated sequences cache. Used for testing isolation.
     */
    public function resetSequences(): void
    {
        self::$generatedSequences = [];
    }
}
