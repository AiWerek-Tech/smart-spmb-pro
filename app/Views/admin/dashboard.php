<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
if (!function_exists('sp_admin_time_ago')) {
    function sp_admin_time_ago(?string $datetime): string
    {
        if (empty($datetime)) {
            return 'baru saja';
        }

        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        if ($diff->d >= 7) {
            return floor($diff->d / 7) . ' minggu lalu';
        }

        if ($diff->d > 0) {
            return $diff->d . ' hari lalu';
        }

        if ($diff->h > 0) {
            return $diff->h . ' jam lalu';
        }

        if ($diff->i > 0) {
            return $diff->i . ' menit lalu';
        }

        return 'baru saja';
    }
}

$trendLabelList = json_decode($trendLabels ?? '[]', true) ?: [];
$trendDataList = json_decode($trendData ?? '[]', true) ?: [];
$maxFunnel = max(array_map(static fn ($item) => (int) ($item['value'] ?? 0), $funnelSteps ?? []) ?: [0]);
?>

<section class="admin-command-center" aria-labelledby="admin-dashboard-title">
    <header class="admin-hero-panel">
        <div class="admin-hero-panel__content">
            <p class="admin-hero-panel__eyebrow">Tahun Ajaran <?= esc($academicYear) ?></p>
            <h1 id="admin-dashboard-title">Dashboard SPMB</h1>
            <p>Command center untuk memantau pendaftar, verifikasi dokumen, seleksi, kuota, dan kesiapan Dapodik.</p>
        </div>
        <div class="admin-hero-panel__actions" aria-label="Aksi cepat dashboard">
            <a href="<?= base_url('operator/registrants?status=submitted') ?>" class="btn btn-primary">
                <i data-lucide="file-check-2" class="me-2"></i>Verifikasi
            </a>
            <a href="<?= base_url('admin/seleksi') ?>" class="btn btn-outline-primary">
                <i data-lucide="award" class="me-2"></i>Seleksi
            </a>
        </div>
    </header>

    <div class="admin-summary-grid" aria-label="Ringkasan eksekutif">
        <?php foreach (($summaryCards ?? []) as $card): ?>
            <?= view('components/stat_card', $card) ?>
        <?php endforeach; ?>
    </div>

    <div class="admin-dashboard-grid">
        <section class="admin-panel admin-panel--priority" aria-labelledby="priority-task-title">
            <div class="admin-panel__header">
                <div>
                    <p class="admin-panel__kicker">Prioritas hari ini</p>
                    <h2 id="priority-task-title">Tugas Mendesak</h2>
                </div>
                <a href="<?= base_url('operator/registrants') ?>" class="admin-panel__link">Lihat antrian</a>
            </div>

            <?php if (empty($priorityTasks)): ?>
                <?= view('components/empty_state', ['icon' => 'check-circle-2', 'title' => 'Tidak ada tugas mendesak', 'text' => 'Semua proses utama sedang terkendali.']) ?>
            <?php else: ?>
                <div class="admin-task-list">
                    <?php foreach ($priorityTasks as $task): ?>
                        <a href="<?= esc($task['url'] ?? '#') ?>" class="admin-task-item admin-task-item--<?= esc($task['tone'] ?? 'primary') ?>">
                            <span class="admin-task-item__icon" aria-hidden="true"><i data-lucide="<?= esc($task['icon'] ?? 'circle') ?>"></i></span>
                            <span class="admin-task-item__copy">
                                <strong><?= number_format((int) ($task['value'] ?? 0), 0, ',', '.') ?></strong>
                                <span><?= esc($task['label'] ?? '') ?></span>
                            </span>
                            <i data-lucide="chevron-right" class="admin-task-item__arrow" aria-hidden="true"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="admin-panel" aria-labelledby="funnel-title">
            <div class="admin-panel__header">
                <div>
                    <p class="admin-panel__kicker">Admission funnel</p>
                    <h2 id="funnel-title">Pergerakan Pendaftar</h2>
                </div>
            </div>

            <div class="admin-funnel" role="list">
                <?php foreach (($funnelSteps ?? []) as $step): ?>
                    <?php
                        $stepValue = (int) ($step['value'] ?? 0);
                        $stepPercent = $maxFunnel > 0 ? max(($stepValue / $maxFunnel) * 100, 8) : 8;
                    ?>
                    <div class="admin-funnel__step" role="listitem">
                        <div class="admin-funnel__meta">
                            <span><?= esc($step['label'] ?? '') ?></span>
                            <strong><?= number_format($stepValue, 0, ',', '.') ?></strong>
                        </div>
                        <div class="admin-funnel__bar" aria-hidden="true">
                            <span style="width: <?= esc((string) $stepPercent) ?>%"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="admin-dashboard-grid admin-dashboard-grid--wide">
        <section class="admin-panel" aria-labelledby="quota-title">
            <div class="admin-panel__header">
                <div>
                    <p class="admin-panel__kicker">Kuota jalur</p>
                    <h2 id="quota-title">Kapasitas Penerimaan</h2>
                </div>
                <a href="<?= base_url('admin/jalur') ?>" class="admin-panel__link">Kelola jalur</a>
            </div>

            <?php if (empty($quotaUsage)): ?>
                <?= view('components/empty_state', ['icon' => 'git-fork', 'title' => 'Jalur belum tersedia', 'text' => 'Tambahkan jalur pendaftaran agar kuota bisa dipantau.']) ?>
            <?php else: ?>
                <div class="admin-quota-list">
                    <?php foreach ($quotaUsage as $quota): ?>
                        <div class="admin-quota-item admin-quota-item--<?= esc($quota['status'] ?? 'success') ?>">
                            <div class="admin-quota-item__top">
                                <strong><?= esc($quota['name'] ?? 'Jalur') ?></strong>
                                <span><?= number_format((int) ($quota['used'] ?? 0), 0, ',', '.') ?> / <?= number_format((int) ($quota['quota'] ?? 0), 0, ',', '.') ?></span>
                            </div>
                            <div class="admin-quota-item__bar" aria-hidden="true">
                                <span style="width: <?= esc((string) ($quota['percent'] ?? 0)) ?>%"></span>
                            </div>
                            <p><?= number_format((int) ($quota['remaining'] ?? 0), 0, ',', '.') ?> kursi tersisa</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="admin-panel" aria-labelledby="trend-title">
            <div class="admin-panel__header">
                <div>
                    <p class="admin-panel__kicker">30 hari terakhir</p>
                    <h2 id="trend-title">Tren Pendaftaran</h2>
                </div>
            </div>

            <?php if (empty($trendLabelList)): ?>
                <?= view('components/empty_state', ['icon' => 'trending-up', 'title' => 'Belum ada tren', 'text' => 'Data tren muncul setelah pendaftar melakukan submit.']) ?>
            <?php else: ?>
                <div class="admin-chart-frame">
                    <div id="registrationTrendChart"></div>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="admin-dashboard-grid admin-dashboard-grid--queue">
        <section class="admin-panel" aria-labelledby="queue-title">
            <div class="admin-panel__header">
                <div>
                    <p class="admin-panel__kicker">Antrian kerja</p>
                    <h2 id="queue-title">Pendaftar Perlu Aksi</h2>
                </div>
                <a href="<?= base_url('operator/registrants') ?>" class="admin-panel__link">Semua pendaftar</a>
            </div>

            <?php if (empty($verificationQueue)): ?>
                <?= view('components/empty_state', ['icon' => 'file-check-2', 'title' => 'Antrian kosong', 'text' => 'Belum ada pendaftar yang menunggu tindakan.']) ?>
            <?php else: ?>
                <div class="admin-queue-list">
                    <?php foreach ($verificationQueue as $item): ?>
                        <article class="admin-queue-card">
                            <div class="admin-queue-card__main">
                                <strong><?= esc($item['full_name'] ?? 'Pendaftar') ?></strong>
                                <span><?= esc($item['registration_number'] ?? '-') ?> &middot; <?= esc($item['jalur_name'] ?? 'Jalur') ?></span>
                            </div>
                            <div class="admin-queue-card__status">
                                <?= view('components/status_badge', ['status' => $item['status'] ?? 'submitted']) ?>
                                <?php if (!empty($item['is_dapodik_ready'])): ?>
                                    <?= view('components/status_badge', ['status' => 'dapodik_ready']) ?>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url('operator/documents/' . (int) ($item['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-primary">
                                Tinjau
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside class="admin-panel" aria-labelledby="quick-actions-title">
            <div class="admin-panel__header">
                <div>
                    <p class="admin-panel__kicker">Shortcut</p>
                    <h2 id="quick-actions-title">Quick Actions</h2>
                </div>
            </div>

            <div class="admin-action-grid">
                <?php foreach (($quickActions ?? []) as $action): ?>
                    <a href="<?= esc($action['url'] ?? '#') ?>" class="admin-action">
                        <span aria-hidden="true"><i data-lucide="<?= esc($action['icon'] ?? 'circle') ?>"></i></span>
                        <strong><?= esc($action['label'] ?? '') ?></strong>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>
    </div>

    <section class="admin-panel" aria-labelledby="activity-title">
        <div class="admin-panel__header">
            <div>
                <p class="admin-panel__kicker">Audit ringan</p>
                <h2 id="activity-title">Aktivitas Terbaru</h2>
            </div>
        </div>

        <?php if (empty($activityItems)): ?>
            <?= view('components/empty_state', ['icon' => 'activity', 'title' => 'Belum ada aktivitas', 'text' => 'Aktivitas admin akan tampil di sini.']) ?>
        <?php else: ?>
            <div class="admin-activity-list">
                <?php foreach ($activityItems as $activity): ?>
                    <article class="admin-activity-item">
                        <span class="admin-activity-item__icon" aria-hidden="true"><i data-lucide="activity"></i></span>
                        <div>
                            <strong><?= esc($activity['action'] ?? 'Aktivitas sistem') ?></strong>
                            <p><?= esc($activity['user_name'] ?? 'Sistem') ?> &middot; <?= sp_admin_time_ago($activity['created_at'] ?? null) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!empty($trendLabelList)): ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartEl = document.querySelector("#registrationTrendChart");
        if (!chartEl || typeof ApexCharts === 'undefined') {
            return;
        }

        const chartPrimary = (window.SpTheme && SpTheme.getThemePrimary()) || getComputedStyle(document.documentElement).getPropertyValue('--sp-primary').trim();
        const isDark = document.body.classList.contains('dark-mode');

        const chart = new ApexCharts(chartEl, {
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                sparkline: { enabled: false }
            },
            series: [{
                name: 'Pendaftar',
                data: <?= json_encode(array_map('intval', $trendDataList)) ?>
            }],
            xaxis: {
                categories: <?= json_encode($trendLabelList) ?>,
                labels: { style: { colors: 'var(--sp-text-muted)' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: 'var(--sp-text-muted)' } }
            },
            stroke: { curve: 'smooth', width: 3, colors: [chartPrimary] },
            colors: [chartPrimary],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.28, opacityTo: 0.02, stops: [0, 90, 100] }
            },
            grid: { borderColor: 'rgba(148, 163, 184, 0.18)', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        });

        chart.render();
        window.registrationTrendChart = chart;
    });
</script>
<?php endif; ?>
<?= $this->endSection() ?>
