<?php

namespace App\Controllers\Operator;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\StudentDocumentModel;
use App\Models\StudentModel;
use App\Models\SettingModel;

class DashboardController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentDocumentModel $documentModel;
    protected StudentModel $studentModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->studentModel      = new StudentModel();
        $this->settingModel      = new SettingModel();
    }

    /**
     * Tampilkan dashboard Operator.
     */
    public function index()
    {
        $academicYear = $this->settingModel->getValue('academic_year', '2026/2027');

        // Operator Dashboard statistics
        $stats = [
            'total_submitted'    => $this->registrationModel->countSubmitted(),
            'pending_verif_docs' => $this->documentModel->where('status', 'pending')->countAllResults(),
            'total_verified'     => $this->registrationModel->where('status', 'verified')->countAllResults(),
            'dapodik_ready'      => $this->studentModel->where('is_dapodik_ready', 1)->countAllResults(),
        ];

        // Fetch recent registrations (e.g. 5 latest)
        $this->registrationModel->select('registrations.*, students.full_name, students.nik, jalur.name AS jalur_name')
            ->join('students', 'students.id = registrations.student_id')
            ->join('jalur', 'jalur.id = registrations.jalur_id')
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
