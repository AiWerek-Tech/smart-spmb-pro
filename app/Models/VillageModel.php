<?php

namespace App\Models;

use CodeIgniter\Model;

class VillageModel extends Model
{
    protected $table            = 'regions_villages';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'district_id', 'name'];

    public function getByDistrictId(string $districtId): array
    {
        return $this->where('district_id', $districtId)->orderBy('name', 'ASC')->findAll();
    }
}
