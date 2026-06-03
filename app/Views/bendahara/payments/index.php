<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$statusLabels = [
    'unpaid' => ['Belum Dibayar', 'warning'],
    'partial' => ['Sebagian', 'info'],
    'paid' => ['Lunas', 'success'],
    'cancelled' => ['Dibatalkan', 'secondary'],
];
?>
<div class="admin-page-shell role-page-shell">
    <div class="role-page-header">
        <div>
            <h1 class="role-page-header__title">Pembayaran SPMB</h1>
            <p class="role-page-header__subtitle">Pantau invoice, verifikasi pembayaran manual, dan ekspor rekap bendahara.</p>
        </div>
        <a href="<?= base_url('bendahara/export?' . http_build_query($filters ?? [])) ?>" class="btn btn-outline-primary d-inline-flex align-items-center">
            <i data-lucide="download" class="me-2" style="width:16px;height:16px;"></i> Ekspor CSV
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <span class="text-muted small">Total Invoice</span>
                <h3 class="mb-0"><?= number_format((int) ($summary['count'] ?? 0)) ?></h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <span class="text-muted small">Nilai Tagihan</span>
                <h3 class="mb-0">Rp <?= number_format((float) ($summary['total'] ?? 0), 0, ',', '.') ?></h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <span class="text-muted small">Sudah Dibayar</span>
                <h3 class="mb-0 text-success">Rp <?= number_format((float) ($summary['paid'] ?? 0), 0, ',', '.') ?></h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <span class="text-muted small">Belum Lunas</span>
                <h3 class="mb-0 text-warning"><?= number_format((int) (($summary['by_status']['unpaid'] ?? 0) + ($summary['by_status']['partial'] ?? 0))) ?></h3>
            </div></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET" action="<?= base_url('bendahara/invoices') ?>">
                <div class="col-md-5">
                    <label class="form-label" for="search">Cari</label>
                    <input class="form-control" id="search" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Invoice, nomor pendaftaran, atau nama">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <?php foreach ($statusLabels as $status => $meta): ?>
                            <option value="<?= esc($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= esc($meta[0]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary w-100" type="submit"><i data-lucide="search" class="me-1" style="width:16px;height:16px;"></i> Terapkan</button>
                    <a class="btn btn-outline-secondary" href="<?= base_url('bendahara/invoices') ?>" title="Reset"><i data-lucide="rotate-ccw" style="width:16px;height:16px;"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pendaftar</th>
                        <th>Jalur</th>
                        <th>Status</th>
                        <th class="text-end">Tagihan</th>
                        <th class="text-end">Sisa</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($invoices ?? []) as $invoice): ?>
                        <?php $statusMeta = $statusLabels[$invoice['status']] ?? [$invoice['status'], 'secondary']; ?>
                        <tr>
                            <td>
                                <strong><?= esc($invoice['invoice_number']) ?></strong>
                                <div class="small text-muted"><?= esc($invoice['registration_number']) ?></div>
                            </td>
                            <td><?= esc($invoice['full_name']) ?></td>
                            <td><?= esc($invoice['jalur_name'] ?? '-') ?></td>
                            <td><span class="badge bg-label-<?= esc($statusMeta[1]) ?> border"><?= esc($statusMeta[0]) ?></span></td>
                            <td class="text-end">Rp <?= number_format((float) $invoice['total_amount'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format((float) $invoice['balance_amount'], 0, ',', '.') ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('bendahara/invoices/' . $invoice['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-5">Belum ada invoice yang sesuai filter.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
