<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\RegistrationModel;

/**
 * AnnouncementController — Halaman pengumuman publik dan cek hasil seleksi.
 *
 * Methods:
 * - index() — Tampilkan halaman pengumuman
 * - checkResult() — Cek hasil seleksi berdasarkan nama atau nomor pendaftaran
 *
 * Requirements: 4.1, 4.2, 4.3, 4.4, 4.5
 */
class AnnouncementController extends BaseController
{
    protected AnnouncementModel $announcementModel;
    protected RegistrationModel $registrationModel;

    public function __construct()
    {
        $this->announcementModel  = new AnnouncementModel();
        $this->registrationModel  = new RegistrationModel();
    }

    /**
     * Tampilkan halaman pengumuman publik.
     * GET: /pengumuman
     *
     * Requirements: 4.1, 4.2
     */
    public function index()
    {
        // Ambil pengumuman yang sudah dipublikasi, diurutkan tanggal terbaru (Req 4.2)
        $announcements = $this->announcementModel
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->paginate(10);

        $pager = $this->announcementModel->pager;

        return view('public/announcements', [
            'title'         => 'Pengumuman',
            'announcements' => $announcements,
            'pager'         => $pager,
            'search'        => '',
        ]);
    }

    public function results()
    {
        return view('public/check_result', [
            'title'   => 'Hasil Seleksi',
            'result'  => null,
            'message' => '',
            'search'  => '',
        ]);
    }

    /**
     * Cek hasil seleksi berdasarkan nama atau nomor pendaftaran.
     * POST: /pengumuman/cek-hasil
     *
     * Requirements: 4.3, 4.4, 4.5
     */
    public function checkResult()
    {
        $search = $this->request->getPost('search');
        $result = null;
        $message = '';

        if (!empty($search)) {
            // Cari berdasarkan nomor pendaftaran atau nama (Req 4.3)
            $registration = $this->registrationModel
                ->select('registrations.*, students.full_name, students.nik, students.nisn, registrations.status AS selection_status, jalur.name AS jalur_name')
                ->join('students', 'students.id = registrations.student_id')
                ->join('jalur', 'jalur.id = registrations.jalur_id')
                ->groupStart()
                    ->where('registrations.registration_number', $search)
                    ->orLike('students.full_name', $search, 'both')
                ->groupEnd()
                ->first();

            if ($registration) {
                // Map fields for view compatibility
                $registration['jalur_id'] = $registration['jalur_name'];
                
                // Cek apakah pengumuman hasil sudah diterbitkan
                $isResultPublished = $this->announcementModel
                    ->where('status', 'published')
                    ->first();

                if (!$isResultPublished) {
                    // Pengumuman belum tersedia (Req 4.5)
                    $message = 'Pengumuman belum tersedia';
                } else {
                    // Tampilkan status seleksi (Req 4.4, 4.5)
                    $result = $registration;
                }
            } else {
                // Data tidak ditemukan (Req 4.5)
                $message = 'Data tidak ditemukan';
            }
        }

        return view('public/check_result', [
            'title'   => 'Cek Hasil Seleksi',
            'result'  => $result,
            'message' => $message,
            'search'  => $search,
        ]);
    }
}
