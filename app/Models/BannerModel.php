<?php

namespace App\Models;

use CodeIgniter\Model;

class BannerModel extends Model
{
    protected $table      = 'banners';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'title',
        'subtitle',
        'image',
        'cta_text',
        'cta_url',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title'      => 'required|max_length[255]',
        'is_active'  => 'required|in_list[0,1]',
        'sort_order' => 'required|integer',
    ];
}
