<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table      = 'gallery';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'academic_year',
        'title',
        'description',
        'image',
        'category',
        'media_type',
        'video_url',
        'video_provider',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'academic_year' => 'permit_empty|max_length[9]',
        'image'      => 'required|max_length[255]',
        'is_active'  => 'required|in_list[0,1]',
        'sort_order' => 'required|integer',
    ];
}
