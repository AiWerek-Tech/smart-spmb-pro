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
}
