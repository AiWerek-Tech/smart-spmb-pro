<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\RegistrationModel;
use App\Models\JalurModel;

class AnnouncementController extends BaseController
{
    protected AnnouncementModel $announcementModel;
    protected RegistrationModel $registrationModel;
    protected JalurModel $jalurModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->registrationModel = new RegistrationModel();
        $this->jalurModel        = new JalurModel();
    }

    // -------------------------------------------------------------------------
    // ANNOUNCEMENT CRUD
    // -------------------------------------------------------------------------

    /**
     * Tampilkan daftar pengumuman.
     */
    public function index()
    {
        $announcements = $this->announcementModel->getAllOrdered();

        $data = [
            'title'         => 'Kelola Pengumenan',
            'announcements' => $announcements,
        ];

        return view('admin/announcements/index', $data);
    }

    /**
     * Tampilkan formulir tambah pengumuman baru.
     */
    public function create()
    {
        return view('admin/announcements/create', [
            'title' => 'Tambah Pengumuman Baru',
        ]);
    }

    /**
     * Simpan pengumuman baru ke database.
     */
    public function store()
    {
        $rules = [
            'title'   => 'required|max_length[200]',
            'content' => 'required',
            'status'  => 'required|in_list[draft,published]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = $this->request->getPost('status');
        $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;

        $announcementId = $this->announcementModel->insert([
            'title'        => $this->request->getPost('title'),
            'content'      => $this->request->getPost('content'),
            'status'       => $status,
            'published_at' => $publishedAt,
            'created_by'   => (int)session()->get('user_id'),
        ]);

        if (!$announcementId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengumuman.');
        }

        return redirect()->to('admin/announcements')->with('success', 'Pengumuman baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit pengumuman.
     */
    public function edit(int $id)
    {
        $announcement = $this->announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to('admin/announcements')->with('error', 'Pengumuman tidak ditemukan.');
        }

        return view('admin/announcements/edit', [
            'title'        => 'Edit Pengumuman',
            'announcement' => $announcement,
        ]);
    }

    /**
     * Perbarui pengumuman di database.
     */
    public function update(int $id)
    {
        $announcement = $this->announcementModel->find($id);

        if (!$announcement) {
            return redirect()->to('admin/announcements')->with('error', 'Pengumuman tidak ditemukan.');
        }

        $rules = [
            'title'   => 'required|max_length[200]',
            'content' => 'required',
            'status'  => 'required|in_list[draft,published]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = $this->request->getPost('status');
        
        $updateData = [
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'status'  => $status,
        ];

        // Jika diubah ke terbit, set tanggal terbit jika sebelumnya kosong
        if ($status === 'published') {
            if (empty($announcement['published_at'])) {
                $updateData['published_at'] = date('Y-m-d H:i:s');
            }
        } else {
            $updateData['published_at'] = null;
        }

        if (!$this->announcementModel->update($id, $updateData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengumuman.');
        }

        return redirect()->to('admin/announcements')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Ubah status terbit pengumuman (publish/unpublish).
     */
    public function publish(int $id)
    {
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            return redirect()->to('admin/announcements')->with('error', 'Pengumuman tidak ditemukan.');
        }

        if ($announcement['status'] === 'published') {
            $this->announcementModel->unpublish($id);
            $msg = 'Pengumuman berhasil ditarik menjadi draft kembali.';
        } else {
            $this->announcementModel->publish($id);
            $msg = 'Pengumuman berhasil diterbitkan.';
        }

        return redirect()->to('admin/announcements')->with('success', $msg);
    }

    /**
     * Hapus pengumuman.
     */
    public function delete(int $id)
    {
        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            return redirect()->to('admin/announcements')->with('error', 'Pengumuman tidak ditemukan.');
        }

        if ($this->announcementModel->delete($id)) {
            return redirect()->to('admin/announcements')->with('success', 'Pengumuman berhasil dihapus.');
        }

        return redirect()->to('admin/announcements')->with('error', 'Gagal menghapus pengumuman.');
    }

    // -------------------------------------------------------------------------
    // SELECTION RESULTS (SELEKSI)
    // -------------------------------------------------------------------------

    /**
     * Tampilkan daftar pendaftar untuk proses seleksi.
     */
    public function seleksi()
    {
        $jalurId = $this->request->getGet('jalur');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $filters = [
            'jalur'  => !empty($jalurId) ? (int)$jalurId : null,
            'status' => !empty($status) ? $status : null,
            'search' => !empty($search) ? $search : null,
        ];

        // Saring pendaftar yang sudah submit (bukan draft)
        $this->registrationModel->applyFilters($filters);
        $registrants = $this->registrationModel->whereNotIn('registrations.status', ['draft'])->findAll();
        
        $jalur = $this->jalurModel->findAll();

        $data = [
            'title'       => 'Hasil Seleksi Pendaftaran',
            'registrants' => $registrants,
            'jalur'       => $jalur,
            'jalurId'     => $jalurId,
            'status'      => $status,
            'search'      => $search,
        ];

        return view('admin/announcements/seleksi', $data);
    }

    /**
     * Ubah status seleksi pendaftar (terima/tolak).
     */
    public function seleksiUpdate(int $id)
    {
        $registration = $this->registrationModel->find($id);
        if (!$registration) {
            return redirect()->to('admin/seleksi')->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $rules = [
            'status' => 'required|in_list[accepted,rejected,verified,submitted]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Status seleksi tidak valid.');
        }

        $status = $this->request->getPost('status');

        // Jika pendaftar diterima, pastikan kuota jalur pendaftaran masih mencukupi
        if ($status === 'accepted') {
            if (!$this->jalurModel->hasAvailableQuota((int)$registration['jalur_id'])) {
                $jalur = $this->jalurModel->find($registration['jalur_id']);
                return redirect()->back()->with('error', 'Gagal menerima peserta! Kuota jalur ' . esc($jalur['name']) . ' sudah penuh (' . esc($jalur['quota']) . ' peserta).');
            }
        }

        if (!$this->registrationModel->updateStatus($id, $status)) {
            return redirect()->back()->with('error', 'Gagal memperbarui status seleksi.');
        }

        // Kirim notifikasi WhatsApp kelulusan seleksi
        try {
            $studentModel = new \App\Models\StudentModel();
            $student = $studentModel->find($registration['student_id']);
            if ($student) {
                $contactModel = new \App\Models\StudentContactModel();
                $contact = $contactModel->findByStudentId((int)$student['id']);
                if ($contact && !empty($contact['phone'])) {
                    $whatsappService = new \App\Services\WhatsappService();
                    if ($status === 'accepted') {
                        $message = sprintf(
                            "Selamat %s! Anda dinyatakan LULUS SELEKSI penerimaan murid baru. Silakan unduh Surat Keterangan Lulus (SKL) Anda di Dashboard Pendaftar.",
                            $student['full_name']
                        );
                    } elseif ($status === 'rejected') {
                        $message = sprintf(
                            "Mohon maaf %s, Anda dinyatakan belum lulus seleksi penerimaan murid baru karena keterbatasan kuota. Tetap semangat!",
                            $student['full_name']
                        );
                    } else {
                        $message = sprintf(
                            "Halo %s, status seleksi pendaftaran Anda diperbarui menjadi: %s.",
                            $student['full_name'],
                            strtoupper($status)
                        );
                    }
                    $whatsappService->sendNotification($contact['phone'], $message);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send WhatsApp notification on selection update: ' . $e->getMessage());
        }

        return redirect()->to('admin/seleksi')->with('success', 'Status hasil seleksi pendaftar berhasil diperbarui.');
    }

    /**
     * Jalankan proses kalkulasi ranking otomatis untuk semua pendaftar.
     */
    public function calculateRanking()
    {
        try {
            $rankingService = new \App\Services\RankingService();
            $result = $rankingService->calculateAll();

            if ($result['success']) {
                return redirect()->to('admin/seleksi')->with('success', 'Kalkulasi ranking berhasil diselesaikan untuk ' . $result['count'] . ' pendaftar.');
            }

            return redirect()->to('admin/seleksi')->with('error', 'Gagal memproses kalkulasi ranking.');
        } catch (\Throwable $e) {
            log_message('error', 'Failed to calculate rankings: ' . $e->getMessage());
            return redirect()->to('admin/seleksi')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
