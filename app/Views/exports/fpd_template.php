<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir F-PD Dapodik - <?= esc($student['full_name']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-logo {
            width: 70px;
            text-align: center;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header-title h4 {
            margin: 0;
            font-size: 11px;
            font-weight: normal;
        }
        .header-qr {
            width: 80px;
            text-align: right;
        }
        .section-title {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 5px;
            margin: 15px 0 8px 0;
            border-left: 3px solid #4472C4;
            font-size: 12px;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .label {
            width: 30%;
            color: #555;
            font-weight: bold;
        }
        .value {
            width: 70%;
            border-bottom: 1px dotted #ccc;
        }
        .value-box {
            width: 70%;
        }
        .border-box {
            border: 1px solid #ccc;
            padding: 2px 5px;
            display: inline-block;
            background-color: #fafafa;
        }
        .nested-table {
            width: 100%;
            border-collapse: collapse;
        }
        .nested-table th {
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            padding: 4px;
            font-weight: bold;
            text-align: left;
        }
        .nested-table td {
            border: 1px solid #ddd;
            padding: 4px;
        }
        .footer-table {
            width: 100%;
            margin-top: 30px;
        }
        .footer-table td {
            width: 50%;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <!-- Placeholder for generic graduation cap icon or local emblem -->
                <span style="font-size: 32px; color: #4472C4;">🎓</span>
            </td>
            <td class="header-title">
                <h2>FORMULIR PESERTA DIDIK (F-PD)</h2>
                <h4>SISTEM PENERIMAAN MURID BARU (SPMB) ONLINE</h4>
                <h3 style="margin: 5px 0 0 0; color: #4472C4;"><?= esc($registration['registration_number']) ?></h3>
            </td>
            <td class="header-qr">
                <?php if (!empty($qrCode)): ?>
                    <img src="<?= $qrCode ?>" style="width: 75px; height: 75px;">
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- 1. Registrasi -->
    <div class="section-title">I. REGISTRASI PESERTA DIDIK</div>
    <table class="data-table">
        <tr>
            <td class="label">Jalur Pendaftaran</td>
            <td class="value"><span class="border-box"><?= esc($registration['jalur_name'] ?? 'Umum') ?></span></td>
        </tr>
        <tr>
            <td class="label">Tahun Ajaran</td>
            <td class="value"><?= esc($registration['academic_year']) ?></td>
        </tr>
        <tr>
            <td class="label">Tanggal Pendaftaran</td>
            <td class="value"><?= date('d F Y H:i', strtotime($registration['submitted_at'])) ?> WIB</td>
        </tr>
    </table>

    <!-- 2. Biodata Siswa -->
    <div class="section-title">II. BIODATA CALON PESERTA DIDIK</div>
    <table class="data-table">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="value" style="font-weight: bold; text-transform: uppercase;"><?= esc($student['full_name']) ?></td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="value"><?= ($student['gender'] === 'L') ? 'Laki-laki (L)' : 'Perempuan (P)' ?></td>
        </tr>
        <tr>
            <td class="label">NIK (No. Induk Kependudukan)</td>
            <td class="value"><?= esc($student['nik']) ?></td>
        </tr>
        <tr>
            <td class="label">NISN (No. Induk Siswa Nasional)</td>
            <td class="value"><?= esc($student['nisn']) ?: '-' ?></td>
        </tr>
        <tr>
            <td class="label">Tempat / Tanggal Lahir</td>
            <td class="value"><?= esc($student['birth_place']) ?>, <?= date('d F Y', strtotime($student['birth_date'])) ?></td>
        </tr>
        <tr>
            <td class="label">No. Registrasi Akta Lahir</td>
            <td class="value"><?= esc($student['birth_cert_number']) ?: '-' ?></td>
        </tr>
        <tr>
            <td class="label">Agama</td>
            <td class="value"><?= esc($student['religion']) ?></td>
        </tr>
        <tr>
            <td class="label">Kewarganegaraan</td>
            <td class="value"><?= esc($student['citizenship']) ?></td>
        </tr>
        <tr>
            <td class="label">Status dalam Keluarga</td>
            <td class="value"><?= esc($student['family_status']) ?></td>
        </tr>
        <tr>
            <td class="label">Kebutuhan Khusus</td>
            <td class="value"><?= esc($student['special_needs']) ?></td>
        </tr>
    </table>

    <!-- 3. Alamat & Kontak -->
    <div class="section-title">III. DATA ALAMAT & KONTAK</div>
    <table class="data-table">
        <tr>
            <td class="label">Alamat Jalan</td>
            <td class="value"><?= esc($address['street_address'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">RT / RW</td>
            <td class="value">RT <?= esc($address['rt'] ?? '0') ?> / RW <?= esc($address['rw'] ?? '0') ?></td>
        </tr>
        <tr>
            <td class="label">Dusun / Kelurahan / Kecamatan</td>
            <td class="value">Dsn. <?= esc($address['hamlet'] ?? '-') ?>, Kel/Desa. <?= esc($address['village'] ?? '-') ?>, Kec. <?= esc($address['subdistrict'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Kabupaten / Provinsi / Kode Pos</td>
            <td class="value"><?= esc($address['district'] ?? '-') ?>, <?= esc($address['province'] ?? '-') ?>, <?= esc($address['postal_code'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Tempat Tinggal / Jarak</td>
            <td class="value"><?= esc($address['residence_type'] ?? '-') ?> / Jarak: <?= esc($address['distance_km'] ?? '0') ?> KM (Moda: <?= esc($address['transport_mode'] ?? '-') ?>)</td>
        </tr>
        <tr>
            <td class="label">Nomor HP / WhatsApp</td>
            <td class="value"><?= esc($student['phone_number'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Alamat Email</td>
            <td class="value"><?= esc($student['email'] ?? '-') ?></td>
        </tr>
    </table>

    <!-- Page Break for cleaner look if content extends -->
    <div style="page-break-after: always;"></div>

    <!-- 4. Data Keluarga -->
    <div class="section-title">IV. DATA KELUARGA KANDUNG</div>
    <h3 style="margin: 5px 0; color: #555; font-size: 11px;">A. DATA AYAH KANDUNG</h3>
    <table class="data-table">
        <tr>
            <td class="label">Nama Lengkap Ayah</td>
            <td class="value"><?= esc($father['full_name'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">NIK Ayah</td>
            <td class="value"><?= esc($father['nik'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Pendidikan / Pekerjaan</td>
            <td class="value"><?= esc($father['education'] ?? '-') ?> / <?= esc($father['occupation'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Penghasilan Bulanan</td>
            <td class="value"><?= esc($father['income'] ?? '-') ?></td>
        </tr>
    </table>

    <h3 style="margin: 15px 0 5px 0; color: #555; font-size: 11px;">B. DATA IBU KANDUNG</h3>
    <table class="data-table">
        <tr>
            <td class="label">Nama Lengkap Ibu</td>
            <td class="value"><?= esc($mother['full_name'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">NIK Ibu</td>
            <td class="value"><?= esc($mother['nik'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Pendidikan / Pekerjaan</td>
            <td class="value"><?= esc($mother['education'] ?? '-') ?> / <?= esc($mother['occupation'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Penghasilan Bulanan</td>
            <td class="value"><?= esc($mother['income'] ?? '-') ?></td>
        </tr>
    </table>

    <!-- 5. Data Periodik -->
    <div class="section-title">V. DATA PERIODIK & KESEHATAN</div>
    <?php
    $db = \Config\Database::connect();
    $periodic = $db->table('student_periodic')->where('student_id', $student['id'])->get()->getRowArray();
    ?>
    <table class="data-table">
        <tr>
            <td class="label">Tinggi / Berat Badan</td>
            <td class="value"><?= esc($periodic['height_cm'] ?? '-') ?> cm / <?= esc($periodic['weight_kg'] ?? '-') ?> kg</td>
        </tr>
        <tr>
            <td class="label">No. Kartu KIP / KKS / PKH</td>
            <td class="value">
                KIP: <?= !empty($periodic['has_kip']) ? esc($periodic['kip_number']) : 'Tidak Ada' ?> | 
                KKS: <?= !empty($periodic['has_kks']) ? esc($periodic['kks_number']) : 'Tidak Ada' ?> | 
                PKH: <?= !empty($periodic['pkh_number']) ? esc($periodic['pkh_number']) : 'Tidak Ada' ?>
            </td>
        </tr>
        <tr>
            <td class="label">Kondisi Khusus / Riwayat Kesehatan</td>
            <td class="value"><?= esc($periodic['special_condition'] ?? 'Tidak Ada Kondisi Khusus') ?></td>
        </tr>
    </table>

    <!-- 6. Riwayat Prestasi -->
    <div class="section-title">VI. RIWAYAT PRESTASI SISWA</div>
    <?php
    $achievements = $db->table('student_achievements')->where('student_id', $student['id'])->get()->getResultArray();
    ?>
    <table class="nested-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Prestasi / Lomba</th>
                <th>Jenis</th>
                <th>Tingkat</th>
                <th>Peringkat</th>
                <th>Tahun</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($achievements)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Tidak ada riwayat prestasi yang diunggah.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($achievements as $idx => $ach): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td><?= esc($ach['name']) ?></td>
                        <td><?= esc($ach['type']) ?></td>
                        <td><?= esc($ach['level']) ?></td>
                        <td><?= esc($ach['rank']) ?></td>
                        <td><?= esc($ach['year']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="footer-table">
        <tr>
            <td>
                <br>
                Calon Peserta Didik,
                <br><br><br><br><br>
                ( <strong><?= esc($student['full_name']) ?></strong> )
            </td>
            <td>
                .................., ........................ 2026
                <br>
                Orang Tua / Wali Calon Siswa,
                <br><br><br><br><br>
                ( .................................................. )
            </td>
        </tr>
    </table>

</body>
</html>
