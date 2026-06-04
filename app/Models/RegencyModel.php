<?php

namespace App\Models;

use CodeIgniter\Model;

class RegencyModel extends Model
{
    protected $table            = 'regions_regencies';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'province_id', 'name'];

    public function getByProvinceId(string $provinceId): array
    {
        return $this->where('province_id', $provinceId)->orderBy('name', 'ASC')->findAll();
    }
}
