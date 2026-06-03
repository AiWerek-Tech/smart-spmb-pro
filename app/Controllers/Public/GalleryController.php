<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\GalleryModel;
use App\Services\AcademicYearService;

class GalleryController extends BaseController
{
    private GalleryModel $galleryModel;
    private AcademicYearService $academicYearService;

    public function __construct()
    {
        $this->galleryModel = new GalleryModel();
        $this->academicYearService = new AcademicYearService();
    }

    public function index()
    {
        $type = $this->request->getGet('type');
        $builder = $this->galleryModel
            ->where('academic_year', $this->academicYearService->activeYear())
            ->where('is_active', 1);

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
