<?php

namespace App\Controllers\Pendaftar;

use App\Controllers\BaseController;
use App\Services\RegistrationService;
use App\Services\ValidationService;
use App\Models\JalurModel;

/**
 * RegistrationController — Form wizard pendaftaran 8 langkah.
 *
 * Methods:
 * - wizard() — Tampilkan halaman wizard
 * - step($step) — GET untuk tampilkan form step tertentu
 * - saveStep($step) — POST untuk simpan step tertentu
 * - submit() — Finalisasi pendaftaran
 *
 * Requirements: 8.1–8.8, 9.1–9.7, 10.1–10.6, 11.1–11.6, 12.1–12.5, 13.1–13.5, 14.1–14.7
 */
class RegistrationController extends BaseController
{
    protected RegistrationService $registrationService;
    protected ValidationService $validationService;
    protected JalurModel $jalurModel;

    public function __construct()
    {
        $this->registrationService = new RegistrationService();
        $this->validationService   = new ValidationService();
        $this->jalurModel          = new JalurModel();
    }

    /**
     * Tampilkan halaman wizard pendaftaran.
     * GET: /pendaftar/daftar
     */
    public function wizard()
    {
        return redirect()->to(base_url('/pendaftar/daftar/step/1'));
    }

    /**
     * Tampilkan form step tertentu.
     * GET: /pendaftar/daftar/step/{n}
     *
     * Requirements: 8.1, 8.2, 8.6, 8.7, 8.8
     */
    public function step($stepNumber = 1)
    {
        $userId = session()->get('user_id');
        $step   = (int)$stepNumber;

        // Validasi step number (1-8)
        if ($step < 1 || $step > 8) {
            return redirect()->to(base_url('pendaftar/daftar'))->with('error', 'Step tidak valid');
        }

        // Ambil draft data sebelumnya
        $draftData = $this->registrationService->getDraftData($userId);
        $stepData  = $draftData["step_{$step}"] ?? [];

        // Ambil jalur untuk dropdown (Req 8.3)
        $jalurs = $this->jalurModel->where('is_active', 1)->findAll();

        // Tambahkan data jalur ke view
        $viewData = [
            'title'        => "Pendaftaran - Langkah {$step} dari 8",
            'step'         => $step,
            'stepData'     => $stepData,
            'jalurs'       => $jalurs,
            'errors'       => [],
            'dapodikValues' => $this->getDapodikDropdownData(),
        ];

        return view("pendaftar/registration/step_{$step}", $viewData);
    }

    /**
     * Simpan data step tertentu.
     * POST: /pendaftar/daftar/save-step/{n}
     *
     * Requirements: 8.1–14.7 (validasi spesifik per step)
     */
    public function saveStep($stepNumber = 1)
    {
        $userId = session()->get('user_id');
        $step   = (int)$stepNumber;

        // Validasi step number
        if ($step < 1 || $step > 8) {
            return $this->response->setJSON(['success' => false, 'message' => 'Step tidak valid'])->setStatusCode(400);
        }

        // Ambil data dari POST
        $data = $this->request->getPost();

        // Validasi dan simpan via RegistrationService
        $result = $this->registrationService->saveStep($userId, $step, $data);

        // Return JSON response
        if (!$result['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message'],
                'errors'  => $result['errors'] ?? [],
            ])->setStatusCode(422);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $result['message'] ?? "Step {$step} berhasil disimpan",
        ]);
    }

    /**
     * Finalisasi pendaftaran.
     * POST: /pendaftar/daftar/submit
     *
     * Requirements: 8.7, 8.8, 29.3, 29.4
     */
    public function submit()
    {
        $userId    = session()->get('user_id');
        $jalurId   = $this->request->getPost('jalur_id');
        $gelombangId = $this->request->getPost('gelombang_id') ?? null;

        // Validasi jalur
        if (empty($jalurId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jalur pendaftaran harus dipilih',
            ])->setStatusCode(422);
        }

        // Validasi bahwa semua 8 step sudah terisi
        $draftData = $this->registrationService->getDraftData($userId);
        $incompleteSteps = [];

        for ($i = 1; $i <= 8; $i++) {
            if (empty($draftData["step_{$i}"])) {
                $incompleteSteps[] = $i;
            }
        }

        if (!empty($incompleteSteps)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semua langkah harus diselesaikan terlebih dahulu',
                'incompleteSteps' => $incompleteSteps,
            ])->setStatusCode(422);
        }

        // Finalisasi pendaftaran (Req 29.3, 29.4)
        $result = $this->registrationService->finalize($userId, $jalurId, $gelombangId);

        if (!$result['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message'] ?? 'Pendaftaran gagal diproses',
            ])->setStatusCode(400);
        }

        // Kirim notifikasi WhatsApp ke calon siswa
        try {
            $studentModel = new \App\Models\StudentModel();
            $student = $studentModel->findByUserId($userId);
            if ($student) {
                $contactModel = new \App\Models\StudentContactModel();
                $contact = $contactModel->findByStudentId((int)$student['id']);
                if ($contact && !empty($contact['phone'])) {
                    $whatsappService = new \App\Services\WhatsappService();
                    $regNumber = $result['registration_number'] ?? '';
                    $message = sprintf(
                        "Halo %s, pendaftaran Anda berhasil dikirim dengan Nomor Pendaftaran: %s. Silakan tunggu proses verifikasi berkas oleh panitia.",
                        $student['full_name'],
                        $regNumber
                    );
                    $whatsappService->sendNotification($contact['phone'], $message);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send WhatsApp notification on finalize: ' . $e->getMessage());
        }

        // Return nomor pendaftaran untuk ditampilkan
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pendaftaran berhasil disimpan',
            'registrationNumber' => $result['registration_number'] ?? null,
        ]);
    }

    /**
     * Helper: Ambil data dropdown dari Dapodik (agama, jenis tinggal, dll).
     */
    protected function getDapodikDropdownData(): array
    {
        return [
            'agama' => ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu'],
            'jenis_tinggal' => ['Bersama orang tua', 'Kos', 'Asrama', 'Panti asuhan', 'Lainnya'],
            'moda_transportasi' => ['Jalan kaki', 'Sepeda', 'Sepeda motor', 'Mobil pribadi', 'Angkutan umum', 'Perahu'],
            'pendidikan' => ['Tidak tamat SD', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'Diploma', 'Sarjana', 'Magister', 'Doktor'],
            'pekerjaan' => ['Tidak bekerja', 'Petani', 'Pedagang', 'Pegawai Negeri', 'Pegawai Swasta', 'Profesional', 'Lainnya'],
            'penghasilan' => ['< 1 juta', '1-2 juta', '2-5 juta', '5-10 juta', '> 10 juta'],
            'tingkat_prestasi' => ['Lokal', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'],
            'peringkat_prestasi' => ['Juara 1', 'Juara 2', 'Juara 3', 'Peserta'],
            'kondisi_khusus' => ['Tidak Ada', 'Tunanetra', 'Tunarungu', 'Tuna wicara', 'Tunagrahita', 'Tunadaksa', 'Autisme', 'ADHD'],
        ];
    }
}
