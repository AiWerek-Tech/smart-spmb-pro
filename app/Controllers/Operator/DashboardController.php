<?php

namespace App\Controllers\Operator;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\StudentDocumentModel;
use App\Models\StudentModel;
use App\Models\SettingModel;
use App\Services\AcademicYearService;

class DashboardController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentDocumentModel $documentModel;
    protected StudentModel $studentModel;
    protected SettingModel $settingModel;
    protected AcademicYearService $academicYearService;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->studentModel      = new StudentModel();
        $this->settingModel      = new SettingModel();
        $this->academicYearService = new AcademicYearService($this->settingModel);
    }

    /**
     * Tampilkan dashboard Operator.
     */
    public function index()
    {
        $academicYear = $this->academicYearService->activeYear();
        $db = \Config\Database::connect();
        $pendingDocs = (int) $db->table('student_documents')
            ->join('registrations', 'registrations.student_id = student_documents.student_id AND registrations.academic_year = student_documents.academic_year')
            ->where('registrations.academic_year', $academicYear)
            ->where('student_documents.status', 'pending')
            ->countAllResults();
        $dapodikReady = (int) $db->table('students')
            ->join('registrations', 'registrations.student_id = students.id')
            ->where('registrations.academic_year', $academicYear)
            ->where('students.is_dapodik_ready', 1)
            ->countAllResults();

        // Operator Dashboard statistics
        $stats = [
            'total_submitted'    => $this->registrationModel->countSubmitted($academicYear),
            'pending_verif_docs' => $pendingDocs,
            'total_verified'     => $this->registrationModel->where('academic_year', $academicYear)->where('status', 'verified')->countAllResults(),
            'dapodik_ready'      => $dapodikReady,
        ];

        // Fetch recent registrations (e.g. 5 latest)
        $this->registrationModel->select('registrations.*, students.full_name, students.nik, jalur.name AS jalur_name')
            ->join('students', 'students.id = registrations.student_id')
            ->join('jalur', 'jalur.id = registrations.jalur_id')
            ->where('registrations.academic_year', $academicYear)
            ->whereNotIn('registrations.status', ['draft'])
            ->orderBy('registrations.submitted_at', 'DESC');
        
        $recentRegistrants = $this->registrationModel->findAll(5);

        $data = [
            'title'             => 'Dashboard Operator',
            'stats'             => $stats,
            'recentRegistrants' => $recentRegistrants,
            'academicYear'      => $academicYear,
            'breadcrumbs'  => [
                ['title' => 'Operator', 'url' => base_url('operator/dashboard')],
                ['title' => 'Dashboard', 'url' => base_url('operator/dashboard')],
            ],
        ];

        return view('operator/dashboard', $data);
    }
}
