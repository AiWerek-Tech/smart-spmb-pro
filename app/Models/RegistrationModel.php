<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistrationModel extends Model
{
    protected $table      = 'registrations';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',
        'student_id',
        'jalur_id',
        'gelombang_id',
        'registration_number',
        'academic_year',
        'status',
        'submitted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id'             => 'required|integer',
        'student_id'          => 'required|integer',
        'jalur_id'            => 'required|integer',
        'registration_number' => 'required|max_length[20]',
        'academic_year'       => 'required|max_length[9]',
        'status'              => 'required|in_list[draft,submitted,verified,accepted,rejected]',
    ];

    /**
     * Ambil pendaftaran berdasarkan user_id.
     */
    public function findByUserId(int $userId, ?string $academicYear = null): ?array
    {
        $query = $this->where('user_id', $userId);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->orderBy('id', 'DESC')->first();
    }

    /**
     * Ambil pendaftaran berdasarkan student_id.
     */
    public function findByStudentId(int $studentId, ?string $academicYear = null): ?array
    {
        $query = $this->where('student_id', $studentId);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->orderBy('id', 'DESC')->first();
    }

    /**
     * Ambil pendaftaran berdasarkan nomor pendaftaran.
     */
    public function findByRegistrationNumber(string $registrationNumber): ?array
    {
        return $this->where('registration_number', $registrationNumber)->first();
    }

    /**
     * Ambil pendaftaran beserta data siswa dan jalur.
     */
    public function getRegistrationWithDetails(int $registrationId, ?string $academicYear = null): ?array
    {
        $query = $this->select(
                        'registrations.*, ' .
                        'students.full_name, students.nik, students.nisn, students.birth_date, students.birth_place, ' .
                        'students.gender, students.religion, students.citizenship, students.family_status, ' .
                        'students.birth_cert_number, students.special_needs, ' .
                        'students.dapodik_percentage, students.is_dapodik_ready, students.form_override, ' .
                        'jalur.name AS jalur_name, ' .
                        'users.email AS user_email'
                    )
                    ->join('students', 'students.id = registrations.student_id')
                    ->join('jalur', 'jalur.id = registrations.jalur_id')
                    ->join('users', 'users.id = registrations.user_id')
                    ->where('registrations.id', $registrationId);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('registrations.academic_year', $academicYear);
        }

        return $query->first();
    }

    /**
     * Terapkan filter pencarian dan filter kolom pada query daftar pendaftar.
     *
     * Mendukung:
     * - Pencarian parsial (LIKE) berdasarkan nama, nomor pendaftaran, NIK
     * - Filter jalur (jalur_id)
     * - Filter status verifikasi (status)
     * - Filter status seleksi (accepted/rejected)
     *
     * @param array $filters Kunci yang didukung: 'search', 'jalur', 'status', 'seleksi'
     * @return $this
     */
    public function applyFilters(array $filters): static
    {
        // Join tabel students dan jalur untuk pencarian dan tampilan
        $this->select(
                'registrations.*, ' .
                'students.full_name, students.nik, students.nisn, students.birth_date, ' .
                'students.gender, students.dapodik_percentage, students.is_dapodik_ready, ' .
                'students.score_distance, students.score_achievement, students.score_total, ' .
                'jalur.name AS jalur_name'
             )
             ->join('students', 'students.id = registrations.student_id')
             ->join('jalur', 'jalur.id = registrations.jalur_id')
             ->orderBy('students.score_total', 'DESC');

        // Pencarian parsial: nama, nomor pendaftaran, NIK
        if (! empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $this->groupStart()
                 ->like('students.full_name', $searchTerm)
                 ->orLike('registrations.registration_number', $searchTerm)
                 ->orLike('students.nik', $searchTerm)
                 ->groupEnd();
        }

        // Filter berdasarkan jalur (jalur_id)
        if (! empty($filters['jalur'])) {
            $this->where('registrations.jalur_id', (int) $filters['jalur']);
        }

        // Filter berdasarkan status verifikasi
        if (! empty($filters['status'])) {
            $this->where('registrations.status', $filters['status']);
        }

        // Filter berdasarkan status seleksi (accepted / rejected)
        if (! empty($filters['seleksi'])) {
            $this->where('registrations.status', $filters['seleksi']);
        }

        return $this;
    }

    /**
     * Hitung total pendaftar (bukan draft).
     */
    public function countSubmitted(?string $academicYear = null): int
    {
        $query = $this->whereNotIn('status', ['draft']);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->countAllResults();
    }

    /**
     * Hitung total pendaftar yang diterima.
     */
    public function countAccepted(?string $academicYear = null): int
    {
        $query = $this->where('status', 'accepted');

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->countAllResults();
    }

    /**
     * Hitung pendaftar non-draft yang seluruh dokumen wajibnya sudah disetujui.
     */
    public function countCompleteRequiredDocuments(?string $academicYear = null): int
    {
        $query = $this->select('id, student_id, jalur_id, academic_year')
            ->whereNotIn('status', ['draft']);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        $registrations = $query->findAll();
        $documentRequirementService = new \App\Services\DocumentRequirementService();
        $documentModel = new StudentDocumentModel();
        $complete = 0;

        foreach ($registrations as $registration) {
            $year = (string) ($registration['academic_year'] ?? $academicYear ?? '');
            $jalurId = isset($registration['jalur_id']) ? (int) $registration['jalur_id'] : null;
            $requiredTypes = $documentRequirementService->requiredTypes($year, $jalurId);

            if ($requiredTypes === []) {
                continue;
            }

            $approvedCount = $documentModel
                ->where('student_id', (int) $registration['student_id'])
                ->where('academic_year', $year)
                ->where('status', 'approved')
                ->whereIn('document_type', $requiredTypes)
                ->countAllResults();

            if ($approvedCount === count($requiredTypes)) {
                $complete++;
            }
        }

        return $complete;
    }

    /**
     * Hitung pendaftar per jalur.
     */
    public function countPerJalur(?string $academicYear = null): array
    {
        $query = $this->select('jalur_id, COUNT(*) AS total')
            ->whereNotIn('status', ['draft']);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->groupBy('jalur_id')->findAll();
    }

    /**
     * Ambil tren pendaftaran harian.
     */
    public function getDailyTrend(string $academicYear): array
    {
        return $this->select('DATE(submitted_at) AS date, COUNT(*) AS total')
                    ->where('academic_year', $academicYear)
                    ->where('status !=', 'draft')
                    ->where('submitted_at IS NOT NULL')
                    ->groupBy('DATE(submitted_at)')
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }

    /**
     * Perbarui status pendaftaran.
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Tandai pendaftaran sebagai submitted.
     */
    public function markAsSubmitted(int $id): bool
    {
        return $this->update($id, [
            'status'       => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Ambil nomor urut terakhir untuk tahun ajaran tertentu (untuk generate nomor pendaftaran).
     * Menggunakan SELECT ... FOR UPDATE untuk atomisitas.
     */
    public function getLastSequenceNumber(string $academicYear): int
    {
        $db = \Config\Database::connect();

        $result = $db->query(
            'SELECT COUNT(*) AS total FROM registrations WHERE academic_year = ? FOR UPDATE',
            [$academicYear]
        )->getRowArray();

        return (int) ($result['total'] ?? 0);
    }
}
