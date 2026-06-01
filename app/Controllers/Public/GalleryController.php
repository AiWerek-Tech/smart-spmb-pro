<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\GalleryModel;

class GalleryController extends BaseController
{
    private GalleryModel $galleryModel;

    public function __construct()
    {
        $this->galleryModel = new GalleryModel();
    }

    public function index()
    {
        $type = $this->request->getGet('type');
        $builder = $this->galleryModel->where('is_active', 1);

        if (in_array($type, ['photo', 'video'], true)) {
            $builder->where('media_type', $type);
        }

        return view('public/gallery', [
            'title' => 'Galeri Sekolah',
            'activeType' => $type ?: 'all',
            'items' => $builder->orderBy('sort_order', 'ASC')->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }
}
