<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentDocumentModel extends Model
{
    protected $table      = 'student_documents';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'student_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /** Tipe dokumen wajib */
    public const REQUIRED_TYPES = ['kk', 'akta', 'foto'];

    /** Tipe dokumen opsional */
    public const OPTIONAL_TYPES = ['raport', 'sertifikat', 'kip_kks'];

    protected $validationRules = [
        'student_id'    => 'required|integer',
        'document_type' => 'required|in_list[kk,akta,foto,raport,sertifikat,kip_kks]',
        'file_name'     => 'required|max_length[255]',
        'file_path'     => 'required|max_length[500]',
        'file_size'     => 'required|integer',
        'mime_type'     => 'required|max_length[100]',
        'status'        => 'required|in_list[pending,approved,rejected]',
    ];

    /**
     * Ambil semua dokumen berdasarkan student_id.
     */
    public function findByStudentId(int $studentId): array
    {
        return $this->where('student_id', $studentId)
                    ->orderBy('document_type', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil dokumen berdasarkan student_id dan tipe dokumen.
     */
    public function findByStudentAndType(int $studentId, string $documentType): ?array
    {
        return $this->where('student_id', $studentId)
                    ->where('document_type', $documentType)
                    ->first();
    }

    /**
     * Cek apakah semua dokumen wajib sudah diunggah.
     */
    public function hasAllRequiredDocuments(int $studentId): bool
    {
        $uploadedTypes = $this->select('document_type')
                              ->where('student_id', $studentId)
                              ->findAll();

        $uploadedTypeList = array_column($uploadedTypes, 'document_type');

        foreach (self::REQUIRED_TYPES as $requiredType) {
            if (! in_array($requiredType, $uploadedTypeList, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ambil daftar dokumen wajib yang belum diunggah.
     */
    public function getMissingRequiredDocuments(int $studentId): array
    {
        $uploadedTypes = $this->select('document_type')
                              ->where('student_id', $studentId)
                              ->findAll();

        $uploadedTypeList = array_column($uploadedTypes, 'document_type');

        return array_values(array_diff(self::REQUIRED_TYPES, $uploadedTypeList));
    }

    /**
     * Verifikasi dokumen (setujui atau tolak).
     */
    public function verifyDocument(int $docId, string $status, ?string $rejectionReason, int $verifiedBy): bool
    {
        $data = [
            'status'      => $status,
            'verified_by' => $verifiedBy,
            'verified_at' => date('Y-m-d H:i:s'),
        ];

        if ($status === 'rejected' && $rejectionReason !== null) {
            $data['rejection_reason'] = $rejectionReason;
        } else {
            $data['rejection_reason'] = null;
        }

        return $this->update($docId, $data);
    }

    /**
     * Hitung jumlah dokumen yang sudah diverifikasi (approved).
     */
    public function countVerified(): int
    {
        return $this->where('status', 'approved')->countAllResults();
    }
}
