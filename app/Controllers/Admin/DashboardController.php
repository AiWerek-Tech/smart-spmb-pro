<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Models\RegistrationModel;
use App\Models\StudentDocumentModel;
use App\Models\JalurModel;
use App\Models\SettingModel;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Services\AcademicYearService;

class DashboardController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentDocumentModel $documentModel;
    protected JalurModel $jalurModel;
    protected SettingModel $settingModel;
    protected StudentModel $studentModel;
    protected UserModel $userModel;
    protected ActivityLogModel $activityLogModel;
    protected AcademicYearService $academicYearService;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->jalurModel        = new JalurModel();
        $this->settingModel      = new SettingModel();
        $this->studentModel      = new StudentModel();
        $this->userModel         = new UserModel();
        $this->activityLogModel  = new ActivityLogModel();
        $this->academicYearService = new AcademicYearService($this->settingModel);
    }

    public function index()
    {
        $academicYear = $this->academicYearService->activeYear();
        $db = \Config\Database::connect();

        $totalRegistrants = $this->registrationModel->countSubmitted($academicYear);
        $acceptedCount = $this->registrationModel->countAccepted($academicYear);
        $completeDocs = $this->registrationModel->countCompleteRequiredDocuments($academicYear);
        $pendingRegistrations = (int) $db->table('registrations')
            ->where('academic_year', $academicYear)
            ->where('status', 'submitted')
            ->countAllResults();
        $verifiedCount = (int) $db->table('registrations')
            ->where('academic_year', $academicYear)
            ->where('status', 'verified')
            ->countAllResults();
        $rejectedCount = (int) $db->table('registrations')
            ->where('academic_year', $academicYear)
            ->where('status', 'rejected')
            ->countAllResults();
        $pendingDocs = (int) $db->table('student_documents')
            ->join('registrations', 'registrations.student_id = student_documents.student_id AND registrations.academic_year = student_documents.academic_year')
            ->where('registrations.academic_year', $academicYear)
            ->where('student_documents.status', 'pending')
            ->countAllResults();
        $rejectedDocs = (int) $db->table('student_documents')
            ->join('registrations', 'registrations.student_id = student_documents.student_id AND registrations.academic_year = student_documents.academic_year')
            ->where('registrations.academic_year', $academicYear)
            ->where('student_documents.status', 'rejected')
            ->countAllResults();
        $dapodikReady = (int) $db->table('students')
            ->join('registrations', 'registrations.student_id = students.id')
            ->where('registrations.academic_year', $academicYear)
            ->where('students.is_dapodik_ready', 1)
            ->countAllResults();

        $stats = [
            'total_registrants' => $totalRegistrants,
            'total_accepted'    => $acceptedCount,
            'complete_docs'     => $completeDocs,
            'total_users'       => $this->userModel->countAllResults(),
        ];

        $jalurStats = $this->jalurModel->getJalurWithRegistrantCount($academicYear);
        $quotaUsage = [];
        foreach ($jalurStats as $jalur) {
            $quota = max((int) ($jalur['quota'] ?? 0), 0);
            $used = max((int) ($jalur['registrant_count'] ?? 0), 0);
            $percent = $quota > 0 ? min(($used / $quota) * 100, 100) : 0;
            $quotaUsage[] = [
                'name' => (string) ($jalur['name'] ?? 'Jalur'),
                'used' => $used,
                'quota' => $quota,
                'remaining' => max($quota - $used, 0),
                'percent' => $percent,
                'status' => $percent >= 90 ? 'danger' : ($percent >= 75 ? 'warning' : 'success'),
            ];
        }

        $dailyTrendRaw = $this->registrationModel->getDailyTrend($academicYear);
        $trendLabels = [];
        $trendData   = [];
        foreach ($dailyTrendRaw as $trend) {
            $trendLabels[] = date('d M Y', strtotime($trend['date']));
            $trendData[]   = (int) $trend['total'];
        }

        $verificationQueue = $db->table('registrations')
            ->select('registrations.id, registrations.registration_number, registrations.status, registrations.submitted_at, students.full_name, students.is_dapodik_ready, students.dapodik_percentage, jalur.name AS jalur_name')
            ->join('students', 'students.id = registrations.student_id')
            ->join('jalur', 'jalur.id = registrations.jalur_id', 'left')
            ->where('registrations.academic_year', $academicYear)
            ->whereIn('registrations.status', ['submitted', 'verified'])
            ->orderBy('registrations.submitted_at', 'ASC')
            ->limit(6)
            ->get()
            ->getResultArray();

        $todayRegistrants = (int) $db->table('registrations')
            ->where('academic_year', $academicYear)
            ->where('status !=', 'draft')
            ->where('submitted_at >=', date('Y-m-d') . ' 00:00:00')
            ->where('submitted_at <=', date('Y-m-d') . ' 23:59:59')
            ->countAllResults();
        $incompleteDocs = max($totalRegistrants - $completeDocs, 0);
        $acceptedPercent = $totalRegistrants > 0 ? ($acceptedCount / $totalRegistrants) * 100 : 0;
        $dapodikPercent = $totalRegistrants > 0 ? ($dapodikReady / $totalRegistrants) * 100 : 0;

        $summaryCards = [
            [
                'label' => 'Total Pendaftar',
                'value' => $totalRegistrants,
                'icon' => 'users',
                'tone' => 'primary',
                'meta' => $todayRegistrants . ' pendaftar hari ini',
                'url' => base_url('operator/registrants'),
            ],
            [
                'label' => 'Menunggu Verifikasi',
                'value' => $pendingDocs,
                'icon' => 'file-search',
                'tone' => 'warning',
                'meta' => $pendingRegistrations . ' status submitted',
                'url' => base_url('operator/registrants?status=submitted'),
            ],
            [
                'label' => 'Perlu Perbaikan',
                'value' => $rejectedDocs + $incompleteDocs,
                'icon' => 'alert-triangle',
                'tone' => 'danger',
                'meta' => $rejectedDocs . ' dokumen ditolak',
                'url' => base_url('operator/registrants'),
            ],
            [
                'label' => 'Diterima Sementara',
                'value' => $acceptedCount,
                'icon' => 'user-check',
                'tone' => 'success',
                'meta' => number_format($acceptedPercent, 1) . '% dari pendaftar',
                'url' => base_url('admin/seleksi'),
            ],
            [
                'label' => 'Siap Dapodik',
                'value' => $dapodikReady,
                'icon' => 'database-zap',
                'tone' => 'info',
                'meta' => number_format($dapodikPercent, 1) . '% data lengkap',
                'url' => base_url('operator/dapodik'),
            ],
        ];

        $priorityTasks = [
            ['label' => 'Dokumen perlu diverifikasi', 'value' => $pendingDocs, 'icon' => 'file-check-2', 'tone' => 'warning', 'url' => base_url('operator/registrants?status=submitted')],
            ['label' => 'Pendaftar perlu koreksi data', 'value' => $rejectedDocs + $incompleteDocs, 'icon' => 'alert-circle', 'tone' => 'danger', 'url' => base_url('operator/registrants')],
            ['label' => 'Hasil seleksi menunggu keputusan', 'value' => $verifiedCount, 'icon' => 'award', 'tone' => 'primary', 'url' => base_url('admin/seleksi')],
            ['label' => 'Siswa diterima belum final Dapodik', 'value' => max($acceptedCount - $dapodikReady, 0), 'icon' => 'clipboard-list', 'tone' => 'info', 'url' => base_url('operator/dapodik')],
        ];

        $funnelSteps = [
            ['label' => 'Submitted', 'value' => $pendingRegistrations],
            ['label' => 'Verified', 'value' => $verifiedCount],
            ['label' => 'Accepted', 'value' => $acceptedCount],
            ['label' => 'Rejected', 'value' => $rejectedCount],
            ['label' => 'Dapodik Ready', 'value' => $dapodikReady],
        ];

        $activityItems = $this->activityLogModel->getRecentLogs(6);

        $quickActions = [
            ['label' => 'Tambah Pendaftar', 'icon' => 'user-plus', 'url' => base_url('auth/register')],
            ['label' => 'Verifikasi Dokumen', 'icon' => 'file-check-2', 'url' => base_url('operator/registrants?status=submitted')],
            ['label' => 'Jalankan Seleksi', 'icon' => 'award', 'url' => base_url('admin/seleksi')],
            ['label' => 'Export Dapodik', 'icon' => 'download', 'url' => base_url('operator/export/excel')],
            ['label' => 'Buat Pengumuman', 'icon' => 'megaphone', 'url' => base_url('admin/announcements/create')],
            ['label' => 'Backup Database', 'icon' => 'database', 'url' => base_url('admin/backup')],
        ];

        $data = [
            'title'             => 'Dashboard Admin',
            'stats'             => $stats,
            'summaryCards'      => $summaryCards,
            'priorityTasks'     => $priorityTasks,
            'funnelSteps'       => $funnelSteps,
            'quotaUsage'        => $quotaUsage,
            'verificationQueue' => $verificationQueue,
            'activityItems'     => $activityItems,
            'quickActions'      => $quickActions,
            'trendLabels'       => json_encode($trendLabels),
            'trendData'         => json_encode($trendData),
            'academicYear'      => $academicYear,
            'breadcrumbs'  => [
                ['title' => 'Admin', 'url' => base_url('admin/dashboard')],
                ['title' => 'Dashboard', 'url' => base_url('admin/dashboard')],
            ],
        ];

        return view('admin/dashboard', $data);
    }
}
