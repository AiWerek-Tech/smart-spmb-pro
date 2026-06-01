<?php

namespace App\Models;

use CodeIgniter\Model;

class StatisticModel extends Model
{
    protected $table      = 'statistics';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'label',
        'value',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'label'      => 'required|max_length[100]',
        'value'      => 'required|max_length[50]',
        'is_active'  => 'required|in_list[0,1]',
        'sort_order' => 'required|integer',
    ];
}
