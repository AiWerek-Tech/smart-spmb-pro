<?php

namespace App\Models;

use CodeIgniter\Model;

class ReligionSubgroupModel extends Model
{
    protected $table            = 'religion_subgroups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['religion_id', 'name'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'religion_id' => 'required|integer',
        'name'        => 'required|max_length[100]',
    ];

    /**
     * Get subgroups by religion ID.
     */
    public function getByReligionId(int $religionId): array
    {
        return $this->where('religion_id', $religionId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
