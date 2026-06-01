<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\FaqModel;
use App\Models\BannerModel;
use App\Models\TestimonialModel;
use App\Models\GalleryModel;
use App\Models\StatisticModel;

class ContentController extends BaseController
{
    protected SettingModel $settingModel;
    protected FaqModel $faqModel;
    protected BannerModel $bannerModel;
    protected TestimonialModel $testimonialModel;
    protected GalleryModel $galleryModel;
    protected StatisticModel $statisticModel;

    public function __construct()
    {
        $this->settingModel     = new SettingModel();
        $this->faqModel         = new FaqModel();
        $this->bannerModel      = new BannerModel();
        $this->testimonialModel = new TestimonialModel();
        $this->galleryModel     = new GalleryModel();
        $this->statisticModel   = new StatisticModel();
    }

    /**
     * Tampilkan halaman pengelolaan profil sekolah & galeri.
     */
    public function index()
    {
        $settings = $this->settingModel->getAllAsArray();

        $defaults = [
            'vision'   => '',
            'mission'  => '',
            'history'  => '',
            'tagline'  => '',
        ];

        $settings = array_merge($defaults, $settings);
        $gallery  = $this->galleryModel->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'title'    => 'Pengelolaan Profil & Galeri',
            'settings' => $settings,
            'gallery'  => $gallery,
        ];

        return view('admin/content/index', $data);
    }

    /**
     * Simpan visi, misi, sejarah, dan tagline.
     */
    public function save()
    {
        $rules = [
            'vision'  => 'required',
            'mission' => 'required',
            'history' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $contentData = [
            'vision'  => $this->request->getPost('vision'),
            'mission' => $this->request->getPost('mission'),
            'history' => $this->request->getPost('history'),
        ];

        if ($this->request->getPost('tagline') !== null) {
            $contentData['tagline'] = $this->request->getPost('tagline');
        }

        if (!$this->settingModel->setMultiple($contentData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil sekolah.');
        }

        return redirect()->to('admin/content')->with('success', 'Profil sekolah berhasil diperbarui.');
    }

    /**
     * Unggah foto galeri baru.
     */
    public function galleryUpload()
    {
        $rules = [
            'gallery_image' => 'uploaded[gallery_image]|is_image[gallery_image]|max_size[gallery_image,2048]',
            'title'         => 'permit_empty|max_length[255]',
            'category'      => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        $imageFile = $this->request->getFile('gallery_image');
        if ($imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            if ($imageFile->move(FCPATH . 'uploads/gallery/', $newName)) {
                $filePath = 'uploads/gallery/' . $newName;

                $this->galleryModel->insert([
                    'title'    => $this->request->getPost('title'),
                    'image'    => $filePath,
                    'category' => $this->request->getPost('category'),
                    'is_active'=> 1,
                    'sort_order'=> 0,
                ]);

                return redirect()->to('admin/content')->with('success', 'Foto galeri berhasil diunggah.');
            }
        }

        return redirect()->back()->with('error', 'Gagal mengunggah foto galeri.');
    }

    /**
     * Hapus foto galeri.
     */
    public function galleryDelete(int $id)
    {
        $item = $this->galleryModel->find($id);
        if (!$item) {
            return redirect()->to('admin/content')->with('error', 'Foto galeri tidak ditemukan.');
        }

        $filePath = FCPATH . $item['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($this->galleryModel->delete($id)) {
            return redirect()->to('admin/content')->with('success', 'Foto galeri berhasil dihapus.');
        }

        return redirect()->to('admin/content')->with('error', 'Gagal menghapus data galeri.');
    }

    // --- BANNER MANAGEMENT ---
    public function banners()
    {
        $data = [
            'title'   => 'Kelola Banner Hero',
            'banners' => $this->bannerModel->orderBy('sort_order', 'ASC')->findAll(),
        ];
        return view('admin/content/banners', $data);
    }

    public function bannerStore()
    {
        $rules = [
            'title'      => 'required|max_length[255]',
            'banner_img' => 'uploaded[banner_img]|is_image[banner_img]|max_size[banner_img,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $img = $this->request->getFile('banner_img');
        $newName = $img->getRandomName();
        $img->move(FCPATH . 'uploads/banners/', $newName);

        $this->bannerModel->insert([
            'title'      => $this->request->getPost('title'),
            'subtitle'   => $this->request->getPost('subtitle'),
            'image'      => 'uploads/banners/' . $newName,
            'cta_text'   => $this->request->getPost('cta_text'),
            'cta_url'    => $this->request->getPost('cta_url'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int)$this->request->getPost('sort_order'),
        ]);

        return redirect()->to('admin/banners')->with('success', 'Banner berhasil ditambahkan.');
    }

    public function bannerUpdate(int $id)
    {
        $banner = $this->bannerModel->find($id);
        if (!$banner) return redirect()->back()->with('error', 'Banner tidak ditemukan.');

        $rules = ['title' => 'required|max_length[255]'];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $updateData = [
            'title'      => $this->request->getPost('title'),
            'subtitle'   => $this->request->getPost('subtitle'),
            'cta_text'   => $this->request->getPost('cta_text'),
            'cta_url'    => $this->request->getPost('cta_url'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int)$this->request->getPost('sort_order'),
        ];

        $img = $this->request->getFile('banner_img');
        if ($img && $img->isValid()) {
            if (file_exists(FCPATH . $banner['image'])) unlink(FCPATH . $banner['image']);
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads/banners/', $newName);
            $updateData['image'] = 'uploads/banners/' . $newName;
        }

        $this->bannerModel->update($id, $updateData);
        return redirect()->to('admin/banners')->with('success', 'Banner berhasil diperbarui.');
    }

    public function bannerDelete(int $id)
    {
        $banner = $this->bannerModel->find($id);
        if ($banner) {
            if (file_exists(FCPATH . $banner['image'])) unlink(FCPATH . $banner['image']);
            $this->bannerModel->delete($id);
        }
        return redirect()->to('admin/banners')->with('success', 'Banner berhasil dihapus.');
    }

    // --- TESTIMONIAL MANAGEMENT ---
    public function testimonials()
    {
        $data = [
            'title'        => 'Kelola Testimoni',
            'testimonials' => $this->testimonialModel->findAll(),
        ];
        return view('admin/content/testimonials', $data);
    }

    public function testimonialStore()
    {
        $rules = [
            'name'    => 'required|max_length[100]',
            'content' => 'required',
        ];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $photoPath = null;
        $img = $this->request->getFile('photo');
        if ($img && $img->isValid()) {
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads/testimonials/', $newName);
            $photoPath = 'uploads/testimonials/' . $newName;
        }

        $this->testimonialModel->insert([
            'name'      => $this->request->getPost('name'),
            'role'      => $this->request->getPost('role'),
            'content'   => $this->request->getPost('content'),
            'rating'    => (int)$this->request->getPost('rating'),
            'photo'     => $photoPath,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('admin/testimonials')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function testimonialUpdate(int $id)
    {
        $testi = $this->testimonialModel->find($id);
        if (!$testi) return redirect()->back()->with('error', 'Testimoni tidak ditemukan.');

        $rules = ['name' => 'required|max_length[100]', 'content' => 'required'];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $updateData = [
            'name'      => $this->request->getPost('name'),
            'role'      => $this->request->getPost('role'),
            'content'   => $this->request->getPost('content'),
            'rating'    => (int)$this->request->getPost('rating'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $img = $this->request->getFile('photo');
        if ($img && $img->isValid()) {
            if ($testi['photo'] && file_exists(FCPATH . $testi['photo'])) unlink(FCPATH . $testi['photo']);
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads/testimonials/', $newName);
            $updateData['photo'] = 'uploads/testimonials/' . $newName;
        }

        $this->testimonialModel->update($id, $updateData);
        return redirect()->to('admin/testimonials')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function testimonialDelete(int $id)
    {
        $testi = $this->testimonialModel->find($id);
        if ($testi) {
            if ($testi['photo'] && file_exists(FCPATH . $testi['photo'])) unlink(FCPATH . $testi['photo']);
            $this->testimonialModel->delete($id);
        }
        return redirect()->to('admin/testimonials')->with('success', 'Testimoni berhasil dihapus.');
    }

    // --- STATISTICS MANAGEMENT ---
    public function statistics()
    {
        $data = [
            'title' => 'Kelola Statistik',
            'stats' => $this->statisticModel->orderBy('sort_order', 'ASC')->findAll(),
        ];
        return view('admin/content/statistics', $data);
    }

    public function statisticStore()
    {
        $rules = [
            'label' => 'required|max_length[100]',
            'value' => 'required|max_length[50]',
        ];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $this->statisticModel->insert([
            'label'      => $this->request->getPost('label'),
            'value'      => $this->request->getPost('value'),
            'icon'       => $this->request->getPost('icon'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int)$this->request->getPost('sort_order'),
        ]);

        return redirect()->to('admin/statistics')->with('success', 'Statistik berhasil ditambahkan.');
    }

    public function statisticUpdate(int $id)
    {
        $rules = [
            'label' => 'required|max_length[100]',
            'value' => 'required|max_length[50]',
        ];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $this->statisticModel->update($id, [
            'label'      => $this->request->getPost('label'),
            'value'      => $this->request->getPost('value'),
            'icon'       => $this->request->getPost('icon'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int)$this->request->getPost('sort_order'),
        ]);

        return redirect()->to('admin/statistics')->with('success', 'Statistik berhasil diperbarui.');
    }

    public function statisticDelete(int $id)
    {
        $this->statisticModel->delete($id);
        return redirect()->to('admin/statistics')->with('success', 'Statistik berhasil dihapus.');
    }

    // --- EXISTING FAQ METHODS (Keep them) ---
    public function faq()
    {
        $faqs = $this->faqModel->orderBy('sort_order', 'ASC')->findAll();
        $data = ['title' => 'Kelola FAQ', 'faqs'  => $faqs];
        return view('admin/content/faq', $data);
    }

    public function faqStore()
    {
        $rules = ['question' => 'required', 'answer' => 'required', 'sort_order' => 'required|integer'];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $this->faqModel->insert([
            'question'   => $this->request->getPost('question'),
            'answer'     => $this->request->getPost('answer'),
            'sort_order' => (int)$this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ]);
        return redirect()->to('admin/faq')->with('success', 'FAQ baru berhasil ditambahkan.');
    }

    public function faqUpdate(int $id)
    {
        $rules = ['question' => 'required', 'answer' => 'required', 'sort_order' => 'required|integer'];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $this->faqModel->update($id, [
            'question'   => $this->request->getPost('question'),
            'answer'     => $this->request->getPost('answer'),
            'sort_order' => (int)$this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ]);
        return redirect()->to('admin/faq')->with('success', 'FAQ berhasil diperbarui.');
    }

    public function faqDelete(int $id)
    {
        $this->faqModel->delete($id);
        return redirect()->to('admin/faq')->with('success', 'FAQ berhasil dihapus.');
    }
}
