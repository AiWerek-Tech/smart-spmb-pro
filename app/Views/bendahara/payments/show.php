<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$statusLabels = [
    'unpaid' => ['Belum Dibayar', 'warning'],
    'partial' => ['Sebagian', 'info'],
    'paid' => ['Lunas', 'success'],
    'cancelled' => ['Dibatalkan', 'secondary'],
];
$statusMeta = $statusLabels[$invoice['status']] ?? [$invoice['status'], 'secondary'];
$canReceivePayment = in_array($invoice['status'], ['unpaid', 'partial'], true);
$canCancel = !in_array($invoice['status'], ['paid', 'cancelled'], true);
?>
<div class="admin-page-shell role-page-shell">
    <div class="role-page-header">
        <div>
            <h1 class="role-page-header__title"><?= esc($invoice['invoice_number']) ?></h1>
            <p class="role-page-header__subtitle"><?= esc($invoice['full_name']) ?> · <?= esc($invoice['registration_number']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('bendahara/invoices/' . $invoice['id'] . '/slip') ?>" class="btn btn-outline-primary" target="_blank">
                <i data-lucide="printer" class="me-2" style="width:16px;height:16px;"></i> Slip
            </a>
            <a href="<?= base_url('bendahara/invoices') ?>" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="text-muted small">Status Invoice</span>
                            <h4 class="mb-0"><span class="badge bg-label-<?= esc($statusMeta[1]) ?> border"><?= esc($statusMeta[0]) ?></span></h4>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small">Tahun Pelajaran</span>
                            <div class="fw-semibold"><?= esc($invoice['academic_year']) ?></div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <span class="text-muted small">Total Tagihan</span>
                            <h4>Rp <?= number_format((float) $invoice['total_amount'], 0, ',', '.') ?></h4>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small">Sudah Dibayar</span>
                            <h4 class="text-success">Rp <?= number_format((float) $invoice['paid_amount'], 0, ',', '.') ?></h4>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted small">Sisa</span>
                            <h4 class="text-warning">Rp <?= number_format((float) $invoice['balance_amount'], 0, ',', '.') ?></h4>
                        </div>
                    </div>
                    <hr>
                    <div class="row g-2 small">
                        <div class="col-md-6"><span class="text-muted">NIK:</span> <?= esc($invoice['nik'] ?? '-') ?></div>
                        <div class="col-md-6"><span class="text-muted">Email:</span> <?= esc($invoice['user_email'] ?? '-') ?></div>
                        <div class="col-md-6"><span class="text-muted">Jalur:</span> <?= esc($invoice['jalur_name'] ?? '-') ?></div>
                        <div class="col-md-6"><span class="text-muted">Diterbitkan:</span> <?= esc($invoice['issued_at'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <?php if ($canReceivePayment): ?>
                <div class="card h-100">
                    <div class="card-header bg-transparent"><h5 class="mb-0">Catat Pembayaran</h5></div>
                    <div class="card-body">
                        <form method="POST" action="<?= base_url('bendahara/invoices/' . $invoice['id'] . '/payment') ?>">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label" for="amount">Nominal</label>
                                <input class="form-control" id="amount" name="amount" type="number" min="1" step="1" max="<?= esc($invoice['balance_amount']) ?>" value="<?= esc((int) $invoice['balance_amount']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="payment_method_id">Metode</label>
                                <select class="form-select" id="payment_method_id" name="payment_method_id">
                                    <option value="">Manual / Lainnya</option>
                                    <?php foreach (($paymentMethods ?? []) as $method): ?>
                                        <option value="<?= esc($method['id']) ?>"><?= esc($method['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="notes">Catatan</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                            <button class="btn btn-success w-100" type="submit">Verifikasi Pembayaran</button>
                        </form>
                    </div>
                </div>
            <?php elseif ($invoice['status'] === 'paid'): ?>
                <div class="alert alert-success h-100 mb-0">Invoice ini sudah lunas.</div>
            <?php else: ?>
                <div class="alert alert-secondary h-100 mb-0">Invoice ini tidak dapat menerima pembayaran.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header bg-transparent"><h5 class="mb-0">Rincian Tagihan</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Item</th><th class="text-end">Nominal</th></tr></thead>
                        <tbody>
                            <?php foreach (($items ?? []) as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($item['name']) ?></strong>
                                        <div class="small text-muted"><?= esc($item['item_code']) ?></div>
                                    </td>
                                    <td class="text-end">Rp <?= number_format((float) $item['total_amount'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-transparent"><h5 class="mb-0">Riwayat Pembayaran</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Tanggal</th><th>Metode</th><th>Status</th><th class="text-end">Nominal</th></tr></thead>
                        <tbody>
                            <?php foreach (($payments ?? []) as $payment): ?>
                                <tr>
                                    <td><?= esc($payment['verified_at'] ?? $payment['created_at']) ?></td>
                                    <td><?= esc($payment['method_name'] ?? 'Manual') ?></td>
                                    <td><span class="badge bg-label-success border"><?= esc($payment['status']) ?></span></td>
                                    <td class="text-end">Rp <?= number_format((float) $payment['amount'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($payments)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada pembayaran.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <?php if ($canCancel): ?>
                <div class="card mb-3">
                    <div class="card-header bg-transparent"><h5 class="mb-0 text-danger">Batalkan Invoice</h5></div>
                    <div class="card-body">
                        <form method="POST" action="<?= base_url('bendahara/invoices/' . $invoice['id'] . '/cancel') ?>">
                            <?= csrf_field() ?>
                            <label class="form-label" for="reason">Alasan</label>
                            <textarea class="form-control mb-3" id="reason" name="reason" rows="3" required></textarea>
                            <button class="btn btn-outline-danger w-100" type="submit">Batalkan Invoice</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-transparent"><h5 class="mb-0">Log Pembayaran</h5></div>
                <div class="list-group list-group-flush">
                    <?php foreach (($logs ?? []) as $log): ?>
                        <div class="list-group-item">
                            <div class="fw-semibold"><?= esc($log['action']) ?></div>
                            <div class="small text-muted"><?= esc($log['created_at']) ?> - <?= esc($log['old_status'] ?? '-') ?> to <?= esc($log['new_status'] ?? '-') ?></div>
                            <?php if (!empty($log['notes'])): ?><div class="small"><?= esc($log['notes']) ?></div><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                        <div class="list-group-item text-muted">Belum ada log.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
