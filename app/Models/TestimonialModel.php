<?php

namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table      = 'testimonials';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'name',
        'role',
        'content',
        'photo',
        'rating',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'      => 'required|max_length[100]',
        'content'   => 'required',
        'rating'    => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'is_active' => 'required|in_list[0,1]',
    ];
}
