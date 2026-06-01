<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Lulus Seleksi - <?= esc($student['full_name']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .header-logo {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }
        .header-logo img {
            max-width: 70px;
            max-height: 70px;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-text h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-text p {
            margin: 0;
            font-size: 10px;
            color: #555;
        }
        .title-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .title-section h3 {
            margin: 0 0 5px 0;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title-section p {
            margin: 0;
            font-size: 11px;
            font-family: Courier, monospace;
        }
        .content-section {
            margin-bottom: 25px;
            text-align: justify;
        }
        .data-table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 5px;
            vertical-align: top;
        }
        .data-label {
            width: 35%;
            font-weight: bold;
        }
        .data-colon {
            width: 3%;
        }
        .data-value {
            width: 62%;
        }
        .status-box {
            text-align: center;
            margin: 20px auto;
            width: 60%;
            border: 2px solid #28a745;
            background-color: #f4faf6;
            color: #28a745;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .signature-left {
            width: 65%;
            vertical-align: bottom;
        }
        .signature-right {
            width: 35%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 70px;
        }
        .qr-img {
            width: 75px;
            height: 75px;
            border: 1px solid #ddd;
            padding: 2px;
        }
    </style>
</head>
<body>

    <!-- Header / Kop Surat -->
    <table class="header-table">
        <tr>
            <?php 
            $logoPath = $schoolLogo ?? '';
            $absoluteLogoPath = '';
            if (!empty($logoPath)) {
                $absoluteLogoPath = WRITEPATH . $logoPath;
                if (!file_exists($absoluteLogoPath)) {
                    $absoluteLogoPath = '';
                }
            }
            ?>
            <?php if (!empty($absoluteLogoPath)): ?>
                <td class="header-logo">
                    <img src="<?= $absoluteLogoPath ?>" alt="Logo">
                </td>
            <?php endif; ?>
            <td class="header-text" style="<?= empty($absoluteLogoPath) ? 'width: 100%;' : '' ?>">
                <h2><?= esc($schoolName ?? 'PANITIA PENERIMAAN MURID BARU') ?></h2>
                <p><?= esc($schoolAddress ?? 'Alamat Sekolah Mandiri, Indonesia') ?></p>
                <p>Telp: <?= esc($schoolPhone ?? '-') ?> | Email: <?= esc($schoolEmail ?? '-') ?></p>
            </td>
        </tr>
    </table>

    <!-- Judul Surat -->
    <div class="title-section">
        <h3>Surat Keterangan Lulus Seleksi</h3>
        <p>Nomor: SKL/SPMB/<?= date('Y', strtotime($registration['created_at'])) ?>/<?= sprintf('%04d', $registration['id']) ?></p>
    </div>

    <!-- Isi Surat -->
    <div class="content-section">
        <p>Yang bertanda tangan di bawah ini, Kepala Sekolah selaku Ketua Panitia Penerimaan Murid Baru (SPMB) menyatakan bahwa calon peserta didik berikut:</p>
        
        <table class="data-table">
            <tr>
                <td class="data-label">Nama Lengkap</td>
                <td class="data-colon">:</td>
                <td class="data-value" style="text-transform: uppercase; font-weight: bold;"><?= esc($student['full_name']) ?></td>
            </tr>
            <tr>
                <td class="data-label">Nomor Pendaftaran</td>
                <td class="data-colon">:</td>
                <td class="data-value" style="font-family: monospace; font-weight: bold;"><?= esc($registration['registration_number']) ?></td>
            </tr>
            <tr>
                <td class="data-label">NIK / NISN</td>
                <td class="data-colon">:</td>
                <td class="data-value"><?= esc($student['nik']) ?> / <?= esc($student['nisn'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="data-label">Jalur Pendaftaran</td>
                <td class="data-colon">:</td>
                <td class="data-value"><?= esc($registration['jalur_name'] ?? 'UMUM') ?></td>
            </tr>
            <tr>
                <td class="data-label">Tahun Ajaran</td>
                <td class="data-colon">:</td>
                <td class="data-value"><?= esc($registration['academic_year']) ?></td>
            </tr>
        </table>

        <div class="status-box">
            Dinyatakan: LULUS SELEKSI
        </div>

        <p>Calon peserta didik tersebut di atas dinyatakan memenuhi kriteria seleksi administrasi dan seleksi jalur pendaftaran untuk bergabung di instansi sekolah kami. Diharapkan untuk segera melakukan proses pendaftaran ulang sesuai dengan jadwal dan petunjuk pendaftaran ulang resmi.</p>
    </div>

    <!-- Tanda Tangan & QR Code -->
    <table class="signature-table">
        <tr>
            <td class="signature-left">
                <?php if (!empty($qrCode)): ?>
                    <img src="<?= $qrCode ?>" class="qr-img"><br>
                    <span style="font-size: 8px; color: #666; font-family: monospace;">Validasi Digital Dokumen Resmi</span>
                <?php endif; ?>
            </td>
            <td class="signature-right">
                <p>Jakarta, <?= date('d F Y') ?></p>
                <p style="font-weight: bold;">Kepala Sekolah,</p>
                <div class="signature-space"></div>
                <p style="font-weight: bold; text-decoration: underline;">Dr. H. Ahmad Wijaya, M.Pd.</p>
                <p style="font-size: 10px; color: #555;">NIP. 19750812 200212 1 003</p>
            </td>
        </tr>
    </table>

</body>
</html>
