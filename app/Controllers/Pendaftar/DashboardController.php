<?php

namespace App\Controllers\Pendaftar;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\StudentModel;
use App\Models\SettingModel;
use App\Models\StudentDocumentModel;
use App\Services\AcademicYearService;
use App\Services\DocumentRequirementService;
use App\Services\RegistrationGateService;
use App\Services\RegistrationService;
use App\Services\UploadDirectoryService;

class DashboardController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentModel $studentModel;
    protected SettingModel $settingModel;
    protected StudentDocumentModel $documentModel;
    protected AcademicYearService $academicYearService;
    protected DocumentRequirementService $documentRequirementService;
    protected RegistrationGateService $registrationGateService;
    protected RegistrationService $registrationService;
    protected UploadDirectoryService $uploadDirectoryService;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->studentModel      = new StudentModel();
        $this->settingModel      = new SettingModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->academicYearService = new AcademicYearService($this->settingModel);
        $this->documentRequirementService = new DocumentRequirementService();
        $this->registrationGateService = new RegistrationGateService();
        $this->registrationService = new RegistrationService();
        $this->uploadDirectoryService = new UploadDirectoryService($this->academicYearService);
    }

    /**
     * Tampilkan dashboard pendaftar.
     */
    public function index()
    {
        $userId = (int)session()->get('user_id');
        $academicYear = $this->academicYearService->activeYear();

        // Check if student profile exists; if not, create a default draft to prevent exceptions
        $student = $this->studentModel->findByUserId($userId);
        if (!$student) {
            $studentId = $this->studentModel->insert([
                'user_id'       => $userId,
                'full_name'     => session()->get('user_name') ?: 'Pendaftar Baru',
                'gender'        => 'L',
                'birth_place'   => 'Jakarta',
                'birth_date'    => '2012-01-01',
                'religion'      => 'Islam',
                'citizenship'   => 'WNI',
                'family_status' => 'Anak Kandung',
                'nik'           => '3200000000000000',
            ]);
            $student = $this->studentModel->find($studentId);
        }

        $studentId = (int)$student['id'];

        // Get registration info if already finalized
        $registration = $this->registrationModel->findByUserId($userId, $academicYear);

        $registrationDetails = [];
        if ($registration) {
            $registrationDetails = $this->registrationModel->getRegistrationWithDetails((int)$registration['id'], $academicYear);
        }

        // Gather document checklist count
        $uploadedDocs = $this->documentModel->findByStudentId($studentId, $academicYear);
        $jalurId = isset($registration['jalur_id']) ? (int) $registration['jalur_id'] : null;
        $mandatoryApprovedCount = $this->documentRequirementService->approvedRequiredCount($studentId, $academicYear, $jalurId);
        $requiredDocuments = $this->documentRequirementService->requiredTypes($academicYear, $jalurId);
        $documentLabels = $this->documentRequirementService->labels($academicYear, $jalurId);
        $draftData = $this->registrationService->getDraftData($userId);
        $draftStepCount = $this->countSavedDraftSteps($draftData);
        $registrationGate = $this->registrationGateService->status(null, $academicYear);

        $invoiceModel = new \App\Models\InvoiceModel();
        $invoiceItemModel = new \App\Models\InvoiceItemModel();
        $invoices = $invoiceModel->where('student_id', $studentId)
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('id', 'DESC')
            ->findAll();

        $paymentModel = new \App\Models\PaymentModel();
        foreach ($invoices as &$invoice) {
            $invoice['items'] = $invoiceItemModel->where('invoice_id', $invoice['id'])->findAll();
            $invoice['has_pending_payment'] = $paymentModel->where('invoice_id', $invoice['id'])
                ->where('status', 'pending')
                ->countAllResults() > 0;
        }

        $paymentMethodModel = new \App\Models\PaymentMethodModel();
        $paymentMethods = $paymentMethodModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'title'               => 'Panel Calon Siswa',
            'student'             => $student,
            'registration'        => $registrationDetails ?: $registration,
            'academicYear'        => $academicYear,
            'mandatoryApproved'   => $mandatoryApprovedCount,
            'mandatoryTotal'      => count($requiredDocuments),
            'requiredDocumentLabels' => array_map(fn ($type) => $documentLabels[$type] ?? $type, $requiredDocuments),
            'totalUploaded'       => count($uploadedDocs),
            'draftStepCount'      => $draftStepCount,
            'draftLastSavedAt'    => $this->latestDraftTimestamp($draftData),
            'draftContinueStep'   => min(8, max(1, $draftStepCount + 1)),
            'registrationGate'    => $registrationGate,
            'invoices'            => $invoices,
            'paymentMethods'      => $paymentMethods,
            'breadcrumbs'  => [
                ['title' => 'Calon Siswa', 'url' => base_url('pendaftar/dashboard')],
                ['title' => 'Dashboard', 'url' => base_url('pendaftar/dashboard')],
            ],
        ];

        return view('pendaftar/dashboard', $data);
    }

    private function countSavedDraftSteps(array $draftData): int
    {
        $count = 0;
        for ($step = 1; $step <= 8; $step++) {
            if (!empty($draftData['step_' . $step])) {
                $count++;
            }
        }

        return $count;
    }

    private function latestDraftTimestamp(array $draftData): ?string
    {
        $latest = null;
        array_walk_recursive($draftData, static function ($value, $key) use (&$latest): void {
            if (!in_array($key, ['updated_at', 'created_at'], true) || empty($value) || strtotime((string) $value) === false) {
                return;
            }

            if ($latest === null || strtotime((string) $value) > strtotime($latest)) {
                $latest = (string) $value;
            }
        });

        return $latest;
    }

    /**
     * Konfirmasi / upload bukti pembayaran.
     */
    public function confirmPayment(int $invoiceId)
    {
        $userId = (int)session()->get('user_id');
        $student = $this->studentModel->findByUserId($userId);
        if (!$student) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $invoiceModel = new \App\Models\InvoiceModel();
        $invoice = $invoiceModel->find($invoiceId);
        if (!$invoice || (int)$invoice['student_id'] !== (int)$student['id']) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Tagihan tidak ditemukan atau bukan milik Anda.');
        }

        if (in_array($invoice['status'], ['paid', 'cancelled'], true)) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Tagihan ini sudah lunas atau dibatalkan.');
        }

        $rules = [
            'payment_method_id' => 'required|integer',
            'amount'            => 'required|numeric|greater_than[0]',
            'proof_file'        => 'uploaded[proof_file]|ext_in[proof_file,jpg,jpeg,png,pdf]|max_size[proof_file,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Pastikan file bukti transfer berupa Gambar/PDF (max 2MB) dan nominal benar.');
        }

        $file = $this->request->getFile('proof_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $academicYear = $this->academicYearService->activeYear();
            $directory = $this->uploadDirectoryService->writableDirectory('payments/' . $userId, $academicYear);

            if ($file->move($directory['absolute'], $newName)) {
                $filePath = $directory['relative'] . $newName;

                $paymentModel = new \App\Models\PaymentModel();
                $paymentId = $paymentModel->insert([
                    'invoice_id'        => $invoiceId,
                    'payment_method_id' => (int)$this->request->getPost('payment_method_id'),
                    'amount'            => (float)$this->request->getPost('amount'),
                    'status'            => 'pending', // Menunggu verifikasi
                    'proof_file'        => $filePath,
                    'paid_at'           => date('Y-m-d H:i:s'),
                    'notes'             => $this->request->getPost('notes') ?: null,
                    'created_by'        => $userId,
                ]);

                if ($paymentId) {
                    $paymentLogModel = new \App\Models\PaymentLogModel();
                    $paymentLogModel->insert([
                        'invoice_id' => $invoiceId,
                        'payment_id' => $paymentId,
                        'action'     => 'payment_submitted',
                        'old_status' => $invoice['status'],
                        'new_status' => $invoice['status'],
                        'actor_id'   => $userId,
                        'notes'      => 'Pendaftar mengunggah bukti pembayaran sebesar Rp ' . number_format((float)$this->request->getPost('amount'), 0, ',', '.'),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    return redirect()->to('pendaftar/dashboard')->with('success', 'Bukti pembayaran berhasil diunggah. Silakan tunggu verifikasi oleh Bendahara.');
                }
            }
        }

        return redirect()->to('pendaftar/dashboard')->with('error', 'Gagal memproses file upload. Silakan coba lagi.');
    }
}
