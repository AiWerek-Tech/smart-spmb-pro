<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Slip Pembayaran') ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 32px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #111827; padding-bottom: 16px; margin-bottom: 24px; }
        h1 { margin: 0; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 10px; text-align: left; }
        th:last-child, td:last-child { text-align: right; }
        .summary { margin-top: 24px; width: 320px; margin-left: auto; }
        .summary div { display: flex; justify-content: space-between; padding: 6px 0; }
        .paid { color: #047857; font-weight: bold; }
        .muted { color: #6b7280; }
        @media print { body { margin: 18mm; } .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Cetak</button>
    <div class="header">
        <div>
            <h1>Slip Pembayaran SPMB</h1>
            <div class="muted"><?= esc($invoice['invoice_number']) ?></div>
        </div>
        <div>
            <strong><?= esc($invoice['status']) ?></strong><br>
            <span class="muted"><?= esc($invoice['issued_at'] ?? '') ?></span>
        </div>
    </div>

    <p>
        <strong><?= esc($invoice['full_name']) ?></strong><br>
        No. Pendaftaran: <?= esc($invoice['registration_number']) ?><br>
        Jalur: <?= esc($invoice['jalur_name'] ?? '-') ?><br>
        Tahun Pelajaran: <?= esc($invoice['academic_year']) ?>
    </p>

    <table>
        <thead><tr><th>Item</th><th>Nominal</th></tr></thead>
        <tbody>
            <?php foreach (($items ?? []) as $item): ?>
                <tr>
                    <td><?= esc($item['name']) ?></td>
                    <td>Rp <?= number_format((float) $item['total_amount'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="summary">
        <div><span>Total</span><strong>Rp <?= number_format((float) $invoice['total_amount'], 0, ',', '.') ?></strong></div>
        <div><span>Dibayar</span><strong class="paid">Rp <?= number_format((float) $invoice['paid_amount'], 0, ',', '.') ?></strong></div>
        <div><span>Sisa</span><strong>Rp <?= number_format((float) $invoice['balance_amount'], 0, ',', '.') ?></strong></div>
    </div>

    <?php if (!empty($payments)): ?>
        <h2>Riwayat Pembayaran</h2>
        <table>
            <thead><tr><th>Tanggal</th><th>Status</th><th>Nominal</th></tr></thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= esc($payment['verified_at'] ?? $payment['created_at']) ?></td>
                        <td><?= esc($payment['status']) ?></td>
                        <td>Rp <?= number_format((float) $payment['amount'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
