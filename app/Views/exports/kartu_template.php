<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta SPMB - <?= esc($student['full_name']) ?></title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .card-container {
            width: 298px; /* A6 width approx */
            height: 419px; /* A6 height approx */
            padding: 15px;
            box-sizing: border-box;
            border: 4px double #4472C4;
            position: relative;
        }
        .card-header {
            text-align: center;
            border-bottom: 2px solid #4472C4;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .card-header h2 {
            margin: 0 0 3px 0;
            font-size: 12px;
            font-weight: bold;
            color: #4472C4;
            text-transform: uppercase;
        }
        .card-header h3 {
            margin: 0;
            font-size: 10px;
            font-weight: normal;
            color: #666;
        }
        .card-title {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            background-color: #f0f4ff;
            color: #4472C4;
            padding: 4px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .number-box {
            text-align: center;
            margin-bottom: 15px;
        }
        .number-box span {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            border: 1px dashed #4472C4;
            padding: 5px 12px;
            background-color: #fafafa;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-label {
            width: 30%;
            color: #666;
            font-weight: bold;
            font-size: 9px;
        }
        .info-value {
            width: 70%;
            font-weight: bold;
            font-size: 10px;
        }
        .photo-qr-container {
            width: 100%;
            position: absolute;
            bottom: 45px;
            left: 15px;
            right: 15px;
        }
        .photo-cell {
            width: 45%;
            text-align: left;
        }
        .qr-cell {
            width: 45%;
            text-align: right;
        }
        .photo-box {
            width: 80px;
            height: 105px;
            border: 1px solid #ccc;
            text-align: center;
            line-height: 105px;
            background-color: #fafafa;
            color: #999;
            font-size: 8px;
            overflow: hidden;
        }
        .photo-img {
            width: 80px;
            height: 105px;
            object-fit: cover;
        }
        .qr-img {
            width: 85px;
            height: 85px;
        }
        .card-footer {
            position: absolute;
            bottom: 10px;
            left: 15px;
            right: 15px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            padding-top: 5px;
            font-size: 8px;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>

    <?php
    // Resolve absolute photo path
    $absolutePhotoPath = '';
    if (!empty($photoPath)) {
        $absolutePhotoPath = WRITEPATH . $photoPath;
        if (!file_exists($absolutePhotoPath)) {
            $absolutePhotoPath = '';
        }
    }
    ?>

    <div class="card-container">
        <!-- Header -->
        <div class="card-header">
            <h2>KARTU PESERTA SPMB ONLINE</h2>
            <h3>TAHUN AJARAN <?= esc($registration['academic_year']) ?> / <?= esc($registration['academic_year'] + 1) ?></h3>
        </div>

        <div class="card-title">KARTU UJIAN & SELEKSI</div>

        <!-- Nomor Pendaftaran -->
        <div class="number-box">
            <span><?= esc($registration['registration_number']) ?></span>
        </div>

        <!-- Biodata -->
        <table class="info-table">
            <tr>
                <td class="info-label">NAMA</td>
                <td class="info-value" style="text-transform: uppercase;"><?= esc($student['full_name']) ?></td>
            </tr>
            <tr>
                <td class="info-label">NIK</td>
                <td class="info-value"><?= esc($student['nik']) ?></td>
            </tr>
            <tr>
                <td class="info-label">JALUR</td>
                <td class="info-value"><span style="color: #4472C4;"><?= esc($registration['jalur_name'] ?? 'UMUM') ?></span></td>
            </tr>
        </table>

        <!-- Photo & QR Code -->
        <table class="photo-qr-container">
            <tr>
                <td class="photo-cell">
                    <div class="photo-box">
                        <?php if (!empty($absolutePhotoPath)): ?>
                            <img src="<?= $absolutePhotoPath ?>" class="photo-img">
                        <?php else: ?>
                            FOTO 3X4
                        <?php endif; ?>
                    </div>
                </td>
                <td class="qr-cell">
                    <?php if (!empty($qrCode)): ?>
                        <img src="<?= $qrCode ?>" class="qr-img">
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="card-footer">
            Harap bawa kartu ini saat pelaksanaan verifikasi berkas & ujian fisik.
        </div>
    </div>

</body>
</html>
