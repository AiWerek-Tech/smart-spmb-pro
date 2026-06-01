<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
// PHP helpers for dynamic calculations
$totalRegistrants = (int) ($stats['total_registrants'] ?? 0);
$acceptedCount = min((int) ($stats['total_accepted'] ?? 0), $totalRegistrants);
$completeDocs = min((int) ($stats['complete_docs'] ?? 0), $totalRegistrants);
$belumLengkap = max($totalRegistrants - $completeDocs, 0);
$percentAccepted = $totalRegistrants > 0 ? ($acceptedCount / $totalRegistrants) * 100 : 0;
$percentCompleteDocs = $totalRegistrants > 0 ? ($completeDocs / $totalRegistrants) * 100 : 0;
$percentBelumLengkap = $totalRegistrants > 0 ? ($belumLengkap / $totalRegistrants) * 100 : 0;

$activityLogModel = new \App\Models\ActivityLogModel();
$recentLogs = $activityLogModel->getRecentLogs(5);

$announcementModel = new \App\Models\AnnouncementModel();
$recentAnnouncements = $announcementModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

if (!function_exists('time_elapsed_string_local')) {
    function time_elapsed_string_local($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'tahun',
            'm' => 'bulan',
            'w' => 'minggu',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'menit',
            's' => 'detik',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
    }
}
?>

<div class="row animate-fade-in">
    <!-- Header — Modern Clean Page Header -->
    <div class="col-12 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="mb-1 fw-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">Dashboard</h3>
            <p class="text-muted mb-0">Ringkasan data Penerimaan Murid Baru (SPMB) Tahun Ajaran <strong><?= esc($academicYear) ?></strong></p>
            <div class="d-flex align-items-center gap-2 mt-2">
                <label class="text-muted small fw-semibold">Tahun Ajaran</label>
                <select class="form-select form-select-sm" style="width: 140px; border-radius: 8px;" onchange="window.location.href='?year=' + this.value">
                    <option value="2026/2027" <?= $academicYear === '2026/2027' ? 'selected' : '' ?>>2026/2027</option>
                    <option value="2025/2026" <?= $academicYear === '2025/2026' ? 'selected' : '' ?>>2025/2026</option>
                </select>
            </div>
        </div>
        <div>
            <a href="<?= base_url('operator/export/excel') ?>" class="btn btn-primary d-inline-flex align-items-center">
                <i data-lucide="download" class="me-2" style="width: 16px; height: 16px;"></i> Ekspor Data
            </a>
        </div>
    </div>

    <!-- Statistics Cards — Premium SaaS Cards -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-primary h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="stat-label">Total Pendaftar</span>
                        <div class="stat-icon stat-icon-primary">
                            <i data-lucide="users"></i>
                        </div>
                    </div>
                    <h3 class="stat-value mb-1"><?= number_format($totalRegistrants, 0, ',', '.') ?></h3>
                </div>
                <div class="mt-2">
                    <span class="text-muted small">Pendaftar aktif pada tahun ajaran ini</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-success h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="stat-label">Diterima</span>
                        <div class="stat-icon stat-icon-success">
                            <i data-lucide="user-check"></i>
                        </div>
                    </div>
                    <h3 class="stat-value mb-1" style="color: var(--sp-success);"><?= number_format($acceptedCount, 0, ',', '.') ?></h3>
                </div>
                <div class="mt-2">
                    <span class="fw-semibold text-dark small">
                        <?= number_format($percentAccepted, 1) ?>%
                    </span>
                    <span class="text-muted small ms-1">dari total pendaftar</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-info h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="stat-label">Berkas Lengkap</span>
                        <div class="stat-icon stat-icon-info">
                            <i data-lucide="file-check"></i>
                        </div>
                    </div>
                    <h3 class="stat-value mb-1" style="color: var(--sp-info);"><?= number_format($completeDocs, 0, ',', '.') ?></h3>
                </div>
                <div class="mt-2">
                    <span class="fw-semibold text-dark small">
                        <?= number_format($percentCompleteDocs, 1) ?>%
                    </span>
                    <span class="text-muted small ms-1">dokumen wajib disetujui</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-warning h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="stat-label">Belum Lengkap</span>
                        <div class="stat-icon stat-icon-warning">
                            <i data-lucide="alert-circle"></i>
                        </div>
                    </div>
                    <h3 class="stat-value mb-1" style="color: var(--sp-warning);"><?= number_format($belumLengkap, 0, ',', '.') ?></h3>
                </div>
                <div class="mt-2">
                    <span class="fw-semibold text-dark small">
                        <?= number_format($percentBelumLengkap, 1) ?>%
                    </span>
                    <span class="text-muted small ms-1">dari total pendaftar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Registration (ApexCharts) -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title m-0">Grafik Pendaftaran <span class="text-muted fw-normal" style="font-size: 0.85rem;">(30 Hari Terakhir)</span></h5>
                </div>
                <span class="badge bg-label-secondary">30 hari terakhir</span>
            </div>
            <div class="card-body">
                <?php if (empty(json_decode($trendLabels))): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-lucide="trending-up"></i>
                        </div>
                        <p class="empty-state-title">Belum Ada Data</p>
                        <p class="empty-state-text">Belum ada data pendaftaran yang disubmit untuk tahun ajaran ini.</p>
                    </div>
                <?php else: ?>
                    <div style="height: 300px; position: relative;">
                        <div id="registrationTrendChart"></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Jalur & Kuota Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">Kuota Jalur Pendaftaran</h5>
                <a href="<?= base_url('admin/jalur') ?>" class="text-primary small fw-semibold">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (empty($jalurStats)): ?>
                    <div class="empty-state py-4">
                        <div class="empty-state-icon" style="width: 50px; height: 50px;">
                            <i data-lucide="git-fork"></i>
                        </div>
                        <p class="empty-state-title small mt-2">Tidak Ada Jalur</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jalurStats as $j): ?>
                        <?php 
                            $quota = (int) $j['quota'];
                            $count = (int) $j['registrant_count'];
                            $percent = $quota > 0 ? min(($count / $quota) * 100, 100) : 0;
                            $barClass = 'bg-primary';
                            if ($percent >= 90) {
                                $barClass = 'bg-danger';
                            } elseif ($percent >= 70) {
                                $barClass = 'bg-warning';
                            }
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark small"><?= esc($j['name']) ?></span>
                                <span class="text-muted small"><?= $count ?> / <?= $quota ?></span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $percent ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Row 2: Recent Logs & Announcements -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentLogs)): ?>
                    <p class="text-muted small text-center my-3">Belum ada aktivitas tercatat.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($recentLogs as $log): ?>
                            <div class="d-flex align-items-start mb-3 pb-2 border-bottom border-light">
                                <div class="badge-center bg-light text-primary me-3 flex-shrink-0" style="width: 32px; height: 32px; border-radius: 50%;">
                                    <i data-lucide="activity" style="width: 14px; height: 14px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-dark small fw-semibold mb-1"><?= esc($log['action']) ?></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size: 0.75rem;"><?= esc($log['user_name'] ?? 'Sistem') ?> (<?= esc($log['user_role'] ?? 'system') ?>)</span>
                                        <span class="text-muted" style="font-size: 0.7rem;"><?= time_elapsed_string_local($log['created_at']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">Pengumuman Terbaru</h5>
                <a href="<?= base_url('admin/announcements') ?>" class="text-primary small fw-semibold">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentAnnouncements)): ?>
                    <p class="text-muted small text-center my-3">Tidak ada pengumuman.</p>
                <?php else: ?>
                    <div class="announcement-list">
                        <?php foreach ($recentAnnouncements as $ann): ?>
                            <div class="d-flex align-items-start mb-3 pb-2 border-bottom border-light">
                                <div class="badge-center bg-light text-primary me-3 flex-shrink-0" style="width: 32px; height: 32px; border-radius: 50%;">
                                    <i data-lucide="megaphone" style="width: 14px; height: 14px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-dark small fw-semibold mb-1"><?= esc($ann['title']) ?></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size: 0.75rem;">Status: <span class="badge bg-light text-dark border py-0 px-2"><?= esc($ann['status']) ?></span></span>
                                        <span class="text-muted" style="font-size: 0.7rem;"><?= date('d M Y', strtotime($ann['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!empty(json_decode($trendLabels))): ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartEl = document.querySelector("#registrationTrendChart");
        if (!chartEl || typeof ApexCharts === 'undefined') {
            return;
        }

        const chartPrimary = (window.SpTheme && SpTheme.getThemePrimary()) || getComputedStyle(document.documentElement).getPropertyValue('--sp-primary').trim();

        // Initialize ApexCharts Line Graph
        const options = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [chartPrimary]
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.02,
                    stops: [0, 90, 100]
                }
            },
            series: [{
                name: 'Pendaftar Masuk',
                data: <?= $trendData ?>
            }],
            xaxis: {
                categories: <?= $trendLabels ?>,
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px'
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px'
                    }
                }
            },
            grid: {
                borderColor: 'rgba(100, 116, 139, 0.06)',
                strokeDashArray: 4
            },
            colors: [chartPrimary],
            dataLabels: { enabled: false },
            tooltip: {
                theme: (window.SpTheme && SpTheme.isDarkMode()) ? 'dark' : (localStorage.getItem('theme') === 'dark' ? 'dark' : 'light'),
                x: { format: 'dd MMM yyyy' }
            }
        };
        
        const chart = new ApexCharts(chartEl, options);
        chart.render();
        window.registrationTrendChart = chart;

        document.addEventListener('theme-color-change', function() {
            const color = window.SpTheme ? SpTheme.getThemePrimary() : chartPrimary;
            chart.updateOptions({
                colors: [color],
                stroke: { colors: [color] },
            });
        });

        document.addEventListener('dark-mode-change', function() {
            chart.updateOptions({
                tooltip: { theme: (window.SpTheme && SpTheme.isDarkMode()) ? 'dark' : 'light' },
            });
        });
    });
</script>
<?php endif; ?>
<?= $this->endSection() ?>
