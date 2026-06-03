<?php

namespace App\Models;

use CodeIgniter\Model;

class AcademicYearModel extends Model
{
    protected $table      = 'academic_years';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'year',
        'label',
        'starts_at',
        'ends_at',
        'is_active',
        'is_archived',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'year'        => 'required|max_length[9]',
        'label'       => 'permit_empty|max_length[120]',
        'starts_at'   => 'permit_empty|valid_date[Y-m-d]',
        'ends_at'     => 'permit_empty|valid_date[Y-m-d]',
        'is_active'   => 'required|in_list[0,1]',
        'is_archived' => 'required|in_list[0,1]',
    ];

    public function active(): ?array
    {
        return $this->where('is_active', 1)->first();
    }

    public function ordered(): array
    {
        return $this->orderBy('year', 'DESC')->findAll();
    }
}
