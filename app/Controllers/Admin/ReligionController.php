<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReligionModel;
use App\Models\ReligionSubgroupModel;

class ReligionController extends BaseController
{
    protected ReligionModel $religionModel;
    protected ReligionSubgroupModel $subgroupModel;

    public function __construct()
    {
        $this->religionModel = new ReligionModel();
        $this->subgroupModel = new ReligionSubgroupModel();
    }

    /**
     * Tampilkan daftar agama dan aliran (sub-agama).
     */
    public function index()
    {
        $religions = $this->religionModel->orderBy('name', 'ASC')->findAll();
        
        foreach ($religions as &$rel) {
            $rel['subgroups'] = $this->subgroupModel->getByReligionId((int)$rel['id']);
        }

        $data = [
            'title'     => 'Kelola Sub-Agama & Aliran',
            'religions' => $religions,
            'breadcrumbs'  => [
                ['title' => 'Admin', 'url' => base_url('admin')],
                ['title' => 'Konfigurasi', 'url' => base_url('admin/settings')],
                ['title' => 'Sub-Agama', 'url' => base_url('admin/religions')],
            ],
        ];

        return view('admin/religions/index', $data);
    }

    /**
     * Simpan agama baru.
     */
    public function store()
    {
        $rules = [
            'name' => 'required|max_length[100]|is_unique[religions.name]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->religionModel->insert([
            'name' => $this->request->getPost('name'),
        ]);

        return redirect()->back()->with('success', 'Agama baru berhasil ditambahkan.');
    }

    /**
     * Update nama agama.
     */
    public function update($id)
    {
        $rules = [
            'name' => "required|max_length[100]|is_unique[religions.name,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->religionModel->update($id, [
            'name' => $this->request->getPost('name'),
        ]);

        return redirect()->back()->with('success', 'Nama agama berhasil diperbarui.');
    }

    /**
     * Hapus agama beserta sub-agama terkait.
     */
    public function delete($id)
    {
        $this->religionModel->delete($id);
        return redirect()->back()->with('success', 'Agama berhasil dihapus.');
    }

    /**
     * Tambah sub-agama/aliran baru.
     */
    public function storeSubgroup()
    {
        $rules = [
            'religion_id' => 'required|integer',
            'name'        => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $religionId = (int)$this->request->getPost('religion_id');
        $name       = $this->request->getPost('name');

        // Check uniqueness for this specific religion
        $existing = $this->subgroupModel
            ->where('religion_id', $religionId)
            ->where('name', $name)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Aliran tersebut sudah ada pada agama ini.');
        }

        $this->subgroupModel->insert([
            'religion_id' => $religionId,
            'name'        => $name,
        ]);

        return redirect()->back()->with('success', 'Aliran baru berhasil ditambahkan.');
    }

    /**
     * Hapus sub-agama/aliran.
     */
    public function deleteSubgroup($id)
    {
        $this->subgroupModel->delete($id);
        return redirect()->back()->with('success', 'Aliran/sub-agama berhasil dihapus.');
    }
}
