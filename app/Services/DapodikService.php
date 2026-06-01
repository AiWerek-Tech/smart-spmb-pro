<?php

namespace App\Services;

use App\Models\StudentModel;
use App\Models\StudentAddressModel;
use App\Models\StudentFamilyModel;

/**
 * DapodikService
 *
 * Memvalidasi kelengkapan 11 field Dapodik wajib per pendaftar dan
 * memperbarui kolom `dapodik_percentage` serta `is_dapodik_ready`
 * pada tabel `students`.
 *
 * 11 field wajib Dapodik:
 *  1. NIK                  → students.nik
 *  2. NISN                 → students.nisn
 *  3. Agama                → students.religion
 *  4. Jenis tinggal        → student_address.residence_type
 *  5. Moda transportasi    → student_address.transport_mode
 *  6. Jarak ke sekolah     → student_address.distance_km
 *  7. Pendidikan ayah      → student_family.education (family_type = 'ayah')
 *  8. Pendidikan ibu       → student_family.education (family_type = 'ibu')
 *  9. Pekerjaan ayah       → student_family.occupation (family_type = 'ayah')
 * 10. Pekerjaan ibu        → student_family.occupation (family_type = 'ibu')
 * 11. Kebutuhan khusus     → students.special_needs
 *
 * Requirements: 17.7, 23.1, 23.2, 23.3, 23.4
 */
class DapodikService
{
    /**
     * Total jumlah field Dapodik wajib.
     */
    private const TOTAL_FIELDS = 11;

    /**
     * Nama-nama field Dapodik wajib (untuk pesan yang ramah pengguna).
     */
    private const FIELD_LABELS = [
        'nik'               => 'NIK',
        'nisn'              => 'NISN',
        'religion'          => 'Agama',
        'residence_type'    => 'Jenis Tinggal',
        'transport_mode'    => 'Moda Transportasi',
        'distance_km'       => 'Jarak ke Sekolah',
        'education_ayah'    => 'Pendidikan Ayah',
        'education_ibu'     => 'Pendidikan Ibu',
        'occupation_ayah'   => 'Pekerjaan Ayah',
        'occupation_ibu'    => 'Pekerjaan Ibu',
        'special_needs'     => 'Kebutuhan Khusus',
    ];

    protected StudentModel $studentModel;
    protected StudentAddressModel $addressModel;
    protected StudentFamilyModel $familyModel;

    public function __construct(
        ?StudentModel $studentModel = null,
        ?StudentAddressModel $addressModel = null,
        ?StudentFamilyModel $familyModel = null
    ) {
        $this->studentModel = $studentModel ?? new StudentModel();
        $this->addressModel = $addressModel ?? new StudentAddressModel();
        $this->familyModel  = $familyModel  ?? new StudentFamilyModel();
    }

    /**
     * Hitung persentase kelengkapan data Dapodik untuk seorang siswa.
     *
     * Formula: (jumlah_field_terisi / 11) × 100
     *
     * @param  int   $studentId  ID siswa pada tabel `students`
     * @return float Persentase kelengkapan (0.0 – 100.0)
     */
    public function getCompletionPercentage(int $studentId): float
    {
        $filledCount = $this->countFilledFields($studentId);

        return ($filledCount / self::TOTAL_FIELDS) * 100;
    }

    /**
     * Kembalikan daftar nama field Dapodik wajib yang belum terisi.
     *
     * @param  int   $studentId  ID siswa pada tabel `students`
     * @return array Array label field yang belum terisi (string[])
     */
    public function getMissingFields(int $studentId): array
    {
        $fieldStatus = $this->evaluateFields($studentId);
        $missing     = [];

        foreach ($fieldStatus as $key => $isFilled) {
            if (! $isFilled) {
                $missing[] = self::FIELD_LABELS[$key] ?? $key;
            }
        }

        return $missing;
    }

    /**
     * Tentukan apakah siswa sudah siap untuk Dapodik
     * (semua 11 field wajib terisi → persentase = 100%).
     *
     * @param  int  $studentId  ID siswa pada tabel `students`
     * @return bool true jika semua 11 field terisi
     */
    public function isReadyForDapodik(int $studentId): bool
    {
        return $this->countFilledFields($studentId) === self::TOTAL_FIELDS;
    }

    /**
     * Hitung dan simpan persentase serta status Dapodik ke tabel `students`.
     *
     * Dipanggil setiap kali data siswa diperbarui agar kolom
     * `dapodik_percentage` dan `is_dapodik_ready` selalu sinkron.
     *
     * @param  int  $studentId  ID siswa pada tabel `students`
     * @return bool true jika pembaruan berhasil
     */
    public function updateDapodikStatus(int $studentId): bool
    {
        $percentage = $this->getCompletionPercentage($studentId);
        $isReady    = $this->isReadyForDapodik($studentId);

        return $this->studentModel->updateDapodikStatus($studentId, $percentage, $isReady);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Evaluasi setiap field Dapodik wajib dan kembalikan status terisi/tidak.
     *
     * @param  int   $studentId
     * @return array<string, bool>  key = nama field internal, value = apakah terisi
     */
    private function evaluateFields(int $studentId): array
    {
        $student = $this->studentModel->find($studentId);
        $address = $this->addressModel->findByStudentId($studentId);
        $father  = $this->familyModel->getFather($studentId);
        $mother  = $this->familyModel->getMother($studentId);

        return [
            'nik'            => $this->isFilled($student['nik'] ?? null),
            'nisn'           => $this->isFilled($student['nisn'] ?? null),
            'religion'       => $this->isFilled($student['religion'] ?? null),
            'residence_type' => $this->isFilled($address['residence_type'] ?? null),
            'transport_mode' => $this->isFilled($address['transport_mode'] ?? null),
            'distance_km'    => $this->isFilledNumeric($address['distance_km'] ?? null),
            'education_ayah' => $this->isFilled($father['education'] ?? null),
            'education_ibu'  => $this->isFilled($mother['education'] ?? null),
            'occupation_ayah'=> $this->isFilled($father['occupation'] ?? null),
            'occupation_ibu' => $this->isFilled($mother['occupation'] ?? null),
            'special_needs'  => $this->isFilled($student['special_needs'] ?? null),
        ];
    }

    /**
     * Hitung jumlah field yang sudah terisi.
     *
     * @param  int $studentId
     * @return int Jumlah field terisi (0–11)
     */
    private function countFilledFields(int $studentId): int
    {
        return array_sum($this->evaluateFields($studentId));
    }

    /**
     * Cek apakah nilai string dianggap terisi (tidak null, tidak string kosong).
     *
     * @param  mixed $value
     * @return bool
     */
    private function isFilled(mixed $value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }

    /**
     * Cek apakah nilai numerik dianggap terisi (tidak null, bukan string kosong,
     * dan nilainya >= 0 — termasuk 0 km yang valid).
     *
     * @param  mixed $value
     * @return bool
     */
    private function isFilledNumeric(mixed $value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }
}
