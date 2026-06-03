<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JalurModel;
use App\Models\GelombangModel;
use App\Services\AcademicYearService;

class JalurController extends BaseController
{
    protected JalurModel $jalurModel;
    protected GelombangModel $gelombangModel;
    protected AcademicYearService $academicYearService;

    public function __construct()
    {
        $this->jalurModel = new JalurModel();
        $this->gelombangModel = new GelombangModel();
        $this->academicYearService = new AcademicYearService();
    }

    // -------------------------------------------------------------------------
    // JALUR MANAGEMENT
    // -------------------------------------------------------------------------

    /**
     * Tampilkan daftar jalur pendaftaran.
     */
    public function index()
    {
        $activeYear = $this->academicYearService->activeYear();
        $jalur = $this->jalurModel->getJalurWithRegistrantCount($activeYear);

        $data = [
            'title' => 'Jalur Pendaftaran',
            'jalur' => $jalur,
            'activeYear' => $activeYear,
        ];

        return view('admin/jalur/index', $data);
    }

    /**
     * Perbarui data kuota dan deskripsi jalur.
     */
    public function update(int $id)
    {
        $jalur = $this->jalurModel->find($id);
        if (!$jalur) {
            return redirect()->to('admin/jalur')->with('error', 'Jalur pendaftaran tidak ditemukan.');
        }

        $rules = [
            'quota'       => 'required|integer|greater_than[0]',
            'description' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newQuota = (int)$this->request->getPost('quota');
        $activeYear = $this->academicYearService->activeYear();

        // Validasi kuota baru tidak lebih kecil dari jumlah pendaftar saat ini (Req 11)
        if ($newQuota < $this->jalurModel->countRegistrants($id, $activeYear)) {
            $currentRegistrants = $this->jalurModel->countRegistrants($id, $activeYear);
            return redirect()->back()->withInput()->with('error', "Kuota baru ($newQuota) tidak boleh lebih kecil dari jumlah pendaftar terdaftar ($currentRegistrants).");
        }

        $updateData = [
            'quota'       => $newQuota,
            'description' => $this->request->getPost('description'),
        ];

        if (!$this->jalurModel->update($id, $updateData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jalur pendaftaran.');
        }

        return redirect()->to('admin/jalur')->with('success', 'Jalur pendaftaran berhasil diperbarui.');
    }

    /**
     * Toggle status aktif jalur.
     */
    public function toggle(int $id)
    {
        if ($this->jalurModel->toggleActive($id)) {
            return redirect()->to('admin/jalur')->with('success', 'Status jalur pendaftaran berhasil diubah.');
        }

        return redirect()->to('admin/jalur')->with('error', 'Gagal mengubah status jalur pendaftaran.');
    }

    // -------------------------------------------------------------------------
    // GELOMBANG MANAGEMENT
    // -------------------------------------------------------------------------

    /**
     * Tampilkan daftar gelombang pendaftaran.
     */
    public function gelombang()
    {
        $activeYear = $this->academicYearService->activeYear();
        $gelombang = $this->gelombangModel->getGelombangWithJalur($activeYear);
        $jalur = $this->jalurModel->findAll();

        $data = [
            'title'     => 'Kelola Gelombang Pendaftaran',
            'gelombang' => $gelombang,
            'jalur'     => $jalur,
            'activeYear' => $activeYear,
        ];

        return view('admin/jalur/gelombang', $data);
    }

    /**
     * Simpan gelombang baru.
     */
    public function gelombangStore()
    {
        $rules = [
            'jalur_id'          => 'required|integer',
            'name'              => 'required|max_length[100]',
            'open_date'         => 'required|valid_date',
            'close_date'        => 'required|valid_date',
            'announcement_date' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $openDate = $this->request->getPost('open_date');
        $closeDate = $this->request->getPost('close_date');
        $isActive = $this->request->getPost('is_active') !== null ? 1 : 0;
        $activeYear = $this->academicYearService->activeYear();

        // Validasi: close_date > open_date (Req 11)
        if (!$this->gelombangModel->validateDates($openDate, $closeDate)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal tutup pendaftaran harus lebih besar dari tanggal buka.');
        }

        // Validasi: Maksimal 3 gelombang aktif (Req 11)
        if ($isActive && $this->gelombangModel->countActiveGelombang($activeYear) >= 3) {
            return redirect()->back()->withInput()->with('error', 'Maksimal gelombang aktif yang diperbolehkan di dalam sistem adalah 3.');
        }

        $gelombangId = $this->gelombangModel->insert([
            'academic_year'      => $activeYear,
            'jalur_id'          => $this->request->getPost('jalur_id'),
            'name'              => $this->request->getPost('name'),
            'open_date'         => $openDate,
            'close_date'        => $closeDate,
            'announcement_date' => $this->request->getPost('announcement_date'),
            'is_active'          => $isActive,
        ]);

        if (!$gelombangId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan gelombang pendaftaran.');
        }

        return redirect()->to('admin/gelombang')->with('success', 'Gelombang pendaftaran baru berhasil ditambahkan.');
    }

    /**
     * Perbarui gelombang pendaftaran.
     */
    public function gelombangUpdate(int $id)
    {
        $gelombang = $this->gelombangModel->find($id);
        if (!$gelombang) {
            return redirect()->to('admin/gelombang')->with('error', 'Gelombang pendaftaran tidak ditemukan.');
        }

        $rules = [
            'jalur_id'          => 'required|integer',
            'name'              => 'required|max_length[100]',
            'open_date'         => 'required|valid_date',
            'close_date'        => 'required|valid_date',
            'announcement_date' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $openDate = $this->request->getPost('open_date');
        $closeDate = $this->request->getPost('close_date');
        $isActive = $this->request->getPost('is_active') !== null ? 1 : 0;
        $activeYear = $gelombang['academic_year'] ?? $this->academicYearService->activeYear();

        // Validasi: close_date > open_date (Req 11)
        if (!$this->gelombangModel->validateDates($openDate, $closeDate)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal tutup pendaftaran harus lebih besar dari tanggal buka.');
        }

        // Validasi: Maksimal 3 gelombang aktif (Req 11)
        if ($isActive && !$gelombang['is_active']) {
            if ($this->gelombangModel->countActiveGelombang($activeYear) >= 3) {
                return redirect()->back()->withInput()->with('error', 'Maksimal gelombang aktif yang diperbolehkan di dalam sistem adalah 3.');
            }
        }

        $updateData = [
            'jalur_id'          => $this->request->getPost('jalur_id'),
            'name'              => $this->request->getPost('name'),
            'open_date'         => $openDate,
            'close_date'        => $closeDate,
            'announcement_date' => $this->request->getPost('announcement_date'),
            'is_active'          => $isActive,
        ];

        if (!$this->gelombangModel->update($id, $updateData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui gelombang pendaftaran.');
        }

        return redirect()->to('admin/gelombang')->with('success', 'Gelombang pendaftaran berhasil diperbarui.');
    }

    /**
     * Hapus gelombang pendaftaran.
     */
    public function gelombangDelete(int $id)
    {
        $gelombang = $this->gelombangModel->find($id);
        if (!$gelombang) {
            return redirect()->to('admin/gelombang')->with('error', 'Gelombang pendaftaran tidak ditemukan.');
        }

        if ($this->gelombangModel->delete($id)) {
            return redirect()->to('admin/gelombang')->with('success', 'Gelombang pendaftaran berhasil dihapus.');
        }

        return redirect()->to('admin/gelombang')->with('error', 'Gagal menghapus gelombang pendaftaran.');
    }
}
