<?php

namespace App\Models;

use CodeIgniter\Model;

class DistrictModel extends Model
{
    protected $table            = 'regions_districts';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'regency_id', 'name'];

    public function getByRegencyId(string $regencyId): array
    {
        return $this->where('regency_id', $regencyId)->orderBy('name', 'ASC')->findAll();
    }
}
