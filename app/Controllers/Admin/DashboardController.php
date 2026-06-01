<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\StudentDocumentModel;
use App\Models\JalurModel;
use App\Models\SettingModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentDocumentModel $documentModel;
    protected JalurModel $jalurModel;
    protected SettingModel $settingModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->jalurModel        = new JalurModel();
        $this->settingModel      = new SettingModel();
        $this->userModel         = new UserModel();
    }

    public function index()
    {
        $academicYear = $this->settingModel->getValue('academic_year', '2026/2027');

        // Fetch overall stats
        $stats = [
            'total_registrants' => $this->registrationModel->countSubmitted(),
            'total_accepted'    => $this->registrationModel->countAccepted(),
            'complete_docs'     => $this->registrationModel->countCompleteRequiredDocuments(),
            'total_users'       => $this->userModel->countAllResults(),
        ];

        // Fetch registrants per jalur
        $jalurStats = $this->jalurModel->getJalurWithRegistrantCount();

        // Fetch daily trend of registrations for the current academic year
        $dailyTrendRaw = $this->registrationModel->getDailyTrend($academicYear);

        // Format daily trend for Chart.js
        $trendLabels = [];
        $trendData   = [];
        foreach ($dailyTrendRaw as $trend) {
            $trendLabels[] = date('d M Y', strtotime($trend['date']));
            $trendData[]   = (int) $trend['total'];
        }

        $data = [
            'title'        => 'Dashboard Admin',
            'stats'        => $stats,
            'jalurStats'   => $jalurStats,
            'trendLabels'  => json_encode($trendLabels),
            'trendData'    => json_encode($trendData),
            'academicYear' => $academicYear,
            'breadcrumbs'  => [
                ['title' => 'Admin', 'url' => base_url('admin/dashboard')],
                ['title' => 'Dashboard', 'url' => base_url('admin/dashboard')],
            ],
        ];

        return view('admin/dashboard', $data);
    }
}
