<?php

namespace App\Models;

use CodeIgniter\Model;

class ReligionModel extends Model
{
    protected $table            = 'religions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'name' => 'required|max_length[100]|is_unique[religions.name,id,{id}]',
    ];
}
