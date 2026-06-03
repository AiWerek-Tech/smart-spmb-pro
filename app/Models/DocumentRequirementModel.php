<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentRequirementModel extends Model
{
    protected $table      = 'document_requirements';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'academic_year',
        'jalur_id',
        'document_type',
        'label',
        'description',
        'is_required',
        'allowed_extensions',
        'max_size_kb',
        'requires_verification',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'academic_year'       => 'required|max_length[9]',
        'document_type'       => 'required|alpha_dash|max_length[60]',
        'label'               => 'required|max_length[150]',
        'allowed_extensions'  => 'required|max_length[120]',
        'max_size_kb'         => 'required|integer|greater_than[0]',
    ];

    public function activeForYear(string $academicYear, ?int $jalurId = null): array
    {
        $query = $this->where('academic_year', $academicYear)->where('is_active', 1);

        if ($jalurId !== null) {
            $query->groupStart()
                ->where('jalur_id', null)
                ->orWhere('jalur_id', $jalurId)
                ->groupEnd();
        } else {
            $query->where('jalur_id', null);
        }

        return $query->orderBy('sort_order', 'ASC')->findAll();
    }

    public function scopedList(string $academicYear, ?int $jalurId = null): array
    {
        $query = $this->where('academic_year', $academicYear);

        if ($jalurId !== null) {
            $query->where('jalur_id', $jalurId);
        }

        return $query->orderBy('jalur_id', 'ASC')->orderBy('sort_order', 'ASC')->findAll();
    }
}
