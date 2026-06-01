<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DetailedDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign keys for a clean wipe of demo-related tables
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        
        $this->db->table('activity_logs')->truncate();
        $this->db->table('student_achievements')->truncate();
        $this->db->table('student_documents')->truncate();
        $this->db->table('student_periodic')->truncate();
        $this->db->table('student_contact')->truncate();
        $this->db->table('student_address')->truncate();
        $this->db->table('student_family')->truncate();
        $this->db->table('registrations')->truncate();
        $this->db->table('students')->truncate();
        $this->db->query('DELETE FROM users WHERE email != "admin@smartspmbpro.sch.id";');
        
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 1. Seed Operators
        $operators = [
            [
                'name'       => 'Operator Utama',
                'email'      => 'operator@smartspmbpro.sch.id',
                'password'   => password_hash('Operator@12345', PASSWORD_BCRYPT),
                'role'       => 'operator',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Operator Pembantu',
                'email'      => 'operator2@smartspmbpro.sch.id',
                'password'   => password_hash('Operator@12345', PASSWORD_BCRYPT),
                'role'       => 'operator',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('users')->insertBatch($operators);
        $operator1Id = $this->db->table('users')->where('email', 'operator@smartspmbpro.sch.id')->get()->getRow()->id;

        // Get Admin User
        $admin = $this->db->table('users')->where('email', 'admin@smartspmbpro.sch.id')->get()->getRow();
        $adminId = $admin ? $admin->id : 1;

        // 2. Define Student Dummy Profiles (8 Candidates)
        $candidates = [
            [
                'email' => 'siswa1@gmail.com',
                'name' => 'Budi Santoso',
                'gender' => 'L',
                'birth_place' => 'Surabaya',
                'birth_date' => '2012-05-14',
                'religion' => 'Islam',
                'family_status' => 'Anak Kandung',
                'nik' => '3578011405120001',
                'nisn' => '0123456781',
                'birth_cert' => 'REG/3578/140512/001',
                'jalur_id' => 1, // Domisili
                'status' => 'accepted',
                'is_dapodik_ready' => 1,
                'dapodik_percentage' => 100.00,
                'score_distance' => 0.45,
                'score_achievement' => 0,
                'score_total' => 95.50,
            ],
            [
                'email' => 'siswa2@gmail.com',
                'name' => 'Siti Aminah',
                'gender' => 'P',
                'birth_place' => 'Gresik',
                'birth_date' => '2012-09-21',
                'religion' => 'Islam',
                'family_status' => 'Anak Kandung',
                'nik' => '3578022109120002',
                'nisn' => '0123456782',
                'birth_cert' => 'REG/3578/210912/002',
                'jalur_id' => 3, // Afirmasi
                'status' => 'accepted',
                'is_dapodik_ready' => 1,
                'dapodik_percentage' => 100.00,
                'score_distance' => 2.10,
                'score_achievement' => 0,
                'score_total' => 88.00,
            ],
            [
                'email' => 'siswa3@gmail.com',
                'name' => 'Ahmad Hidayat',
                'gender' => 'L',
                'birth_place' => 'Sidoarjo',
                'birth_date' => '2012-03-08',
                'religion' => 'Islam',
                'family_status' => 'Anak Kandung',
                'nik' => '3578030803120003',
                'nisn' => '0123456783',
                'birth_cert' => 'REG/3578/080312/003',
                'jalur_id' => 2, // Prestasi
                'status' => 'accepted',
                'is_dapodik_ready' => 1,
                'dapodik_percentage' => 100.00,
                'score_distance' => 5.40,
                'score_achievement' => 45,
                'score_total' => 98.75,
            ],
            [
                'email' => 'siswa4@gmail.com',
                'name' => 'Rina Wijaya',
                'gender' => 'P',
                'birth_place' => 'Malang',
                'birth_date' => '2012-11-30',
                'religion' => 'Kristen',
                'family_status' => 'Anak Kandung',
                'nik' => '3578043011120004',
                'nisn' => '0123456784',
                'birth_cert' => 'REG/3578/301112/004',
                'jalur_id' => 5, // Umum
                'status' => 'rejected',
                'is_dapodik_ready' => 1,
                'dapodik_percentage' => 100.00,
                'score_distance' => 12.00,
                'score_achievement' => 0,
                'score_total' => 45.20,
            ],
            [
                'email' => 'siswa5@gmail.com',
                'name' => 'Dedi Kurniawan',
                'gender' => 'L',
                'birth_place' => 'Jakarta',
                'birth_date' => '2012-01-25',
                'religion' => 'Islam',
                'family_status' => 'Anak Kandung',
                'nik' => '3578052501120005',
                'nisn' => '0123456785',
                'birth_cert' => 'REG/3578/250112/005',
                'jalur_id' => 4, // Mutasi
                'status' => 'verified',
                'is_dapodik_ready' => 1,
                'dapodik_percentage' => 100.00,
                'score_distance' => 3.50,
                'score_achievement' => 0,
                'score_total' => 78.40,
            ],
            [
                'email' => 'siswa6@gmail.com',
                'name' => 'Dewi Lestari',
                'gender' => 'P',
                'birth_place' => 'Surabaya',
                'birth_date' => '2012-07-19',
                'religion' => 'Hindu',
                'family_status' => 'Anak Kandung',
                'nik' => '3578061907120006',
                'nisn' => '0123456786',
                'birth_cert' => 'REG/3578/190712/006',
                'jalur_id' => 1, // Domisili
                'status' => 'submitted',
                'is_dapodik_ready' => 1,
                'dapodik_percentage' => 100.00,
                'score_distance' => 0.85,
                'score_achievement' => 0,
                'score_total' => 92.10,
            ],
            [
                'email' => 'siswa7@gmail.com',
                'name' => 'Ferry Irawan',
                'gender' => 'L',
                'birth_place' => 'Surabaya',
                'birth_date' => '2012-10-02',
                'religion' => 'Islam',
                'family_status' => 'Anak Kandung',
                'nik' => '3578070210120007',
                'nisn' => '0123456787',
                'birth_cert' => 'REG/3578/021012/007',
                'jalur_id' => 5, // Umum
                'status' => 'draft',
                'is_dapodik_ready' => 0,
                'dapodik_percentage' => 45.00,
                'score_distance' => 4.20,
                'score_achievement' => 0,
                'score_total' => 65.00,
            ],
            [
                'email' => 'siswa8@gmail.com',
                'name' => 'Anisa Rahma',
                'gender' => 'P',
                'birth_place' => 'Surabaya',
                'birth_date' => '2012-04-12',
                'religion' => 'Islam',
                'family_status' => 'Anak Kandung',
                'nik' => '3578081204120008',
                'nisn' => '0123456788',
                'birth_cert' => 'REG/3578/120412/008',
                'jalur_id' => 2, // Prestasi
                'status' => 'submitted',
                'is_dapodik_ready' => 1,
                'dapodik_percentage' => 100.00,
                'score_distance' => 6.20,
                'score_achievement' => 20,
                'score_total' => 84.50,
            ],
        ];

        $regCounter = 1;

        foreach ($candidates as $cand) {
            // A. Create User
            $this->db->table('users')->insert([
                'name'       => $cand['name'],
                'email'      => $cand['email'],
                'password'   => password_hash('Siswa@12345', PASSWORD_BCRYPT),
                'role'       => 'pendaftar',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $userId = $this->db->insertID();

            // B. Create Student Profile
            $this->db->table('students')->insert([
                'user_id'            => $userId,
                'full_name'          => $cand['name'],
                'gender'             => $cand['gender'],
                'birth_place'        => $cand['birth_place'],
                'birth_date'         => $cand['birth_date'],
                'religion'           => $cand['religion'],
                'citizenship'        => 'WNI',
                'family_status'      => $cand['family_status'],
                'nik'                => $cand['nik'],
                'nisn'               => $cand['nisn'],
                'birth_cert_number'  => $cand['birth_cert'],
                'special_needs'      => 'Tidak Ada',
                'dapodik_percentage' => $cand['dapodik_percentage'],
                'is_dapodik_ready'   => $cand['is_dapodik_ready'],
                'score_distance'     => $cand['score_distance'],
                'score_achievement'  => $cand['score_achievement'],
                'score_total'        => $cand['score_total'],
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
            $studentId = $this->db->insertID();

            // C. Create Address (student_address)
            $this->db->table('student_address')->insert([
                'student_id'     => $studentId,
                'address'        => 'Jl. Contoh Indah No. ' . rand(1, 150) . ', Perumahan Lestari',
                'rt'             => '02',
                'rw'             => '05',
                'hamlet'         => 'Dusun Harapan',
                'village'        => 'Mekar Jaya',
                'district'       => 'Kec. Contoh Sari',
                'city'           => 'Surabaya',
                'province'       => 'Jawa Timur',
                'postal_code'    => '60123',
                'residence_type' => 'Tinggal dengan Orang Tua',
                'distance_km'    => $cand['score_distance'],
                'transport_mode' => 'Sepeda Motor / Antar Jemput',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            // D. Create Contact (student_contact)
            $this->db->table('student_contact')->insert([
                'student_id' => $studentId,
                'phone'      => '62812' . rand(10000000, 99999999),
                'email'      => $cand['email'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // E. Create Family Records (Father & Mother)
            $this->db->table('student_family')->insert([
                'student_id'     => $studentId,
                'family_type'    => 'ayah',
                'full_name'      => 'Slamet ' . explode(' ', $cand['name'])[1],
                'nik'            => '357801010170' . rand(1000, 9999),
                'birth_place'    => 'Surabaya',
                'birth_date'     => '1975-04-12',
                'education'      => 'SMA / Sederajat',
                'occupation'     => 'Karyawan Swasta',
                'monthly_income' => 'Rp 5.000.000 - Rp 10.000.000',
                'phone'          => '62813' . rand(10000000, 99999999),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            $this->db->table('student_family')->insert([
                'student_id'     => $studentId,
                'family_type'    => 'ibu',
                'full_name'      => 'Kartini ' . explode(' ', $cand['name'])[1],
                'nik'            => '357801010180' . rand(1000, 9999),
                'birth_place'    => 'Surabaya',
                'birth_date'     => '1980-08-25',
                'education'      => 'D3 / S1',
                'occupation'     => 'Ibu Rumah Tangga',
                'monthly_income' => 'Tidak Berpenghasilan',
                'phone'          => '62819' . rand(10000000, 99999999),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            // F. Create Periodic data (student_periodic)
            $this->db->table('student_periodic')->insert([
                'student_id'        => $studentId,
                'height_cm'         => rand(140, 165),
                'weight_kg'         => rand(35, 55),
                'has_kip'           => ($cand['jalur_id'] === 3) ? 1 : 0,
                'kip_number'        => ($cand['jalur_id'] === 3) ? 'KIP-' . rand(100000, 999999) : null,
                'has_kks'           => ($cand['jalur_id'] === 3) ? 1 : 0,
                'kks_number'        => ($cand['jalur_id'] === 3) ? 'KKS-' . rand(100000, 999999) : null,
                'pkh_number'        => ($cand['jalur_id'] === 3) ? 'PKH-' . rand(100000, 999999) : null,
                'special_condition' => 'Tidak Ada Kondisi Khusus',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            // G. Create Achievements (if any points)
            if ($cand['score_achievement'] > 0) {
                $this->db->table('student_achievements')->insert([
                    'student_id'       => $studentId,
                    'achievement_type' => 'akademik',
                    'competition_name' => 'Olimpiade Sains Nasional Matematika',
                    'level'            => 'provinsi',
                    'rank'             => 'juara 1',
                    'year'             => '2025',
                ]);
            }

            // H. Create Registration
            $regNumber = sprintf("SPMB-2026-%04d", $regCounter++);
            
            $submittedAt = null;
            if ($cand['status'] !== 'draft') {
                $submittedAt = date('Y-m-d H:i:s', strtotime('-' . rand(1, 10) . ' hours'));
            }

            // Get gelombang ID matching the registration's jalur
            $gelombang = $this->db->table('gelombang')
                ->where('jalur_id', $cand['jalur_id'])
                ->where('name', 'Gelombang 1')
                ->get()
                ->getRow();
            $gelombangId = $gelombang ? $gelombang->id : null;

            $this->db->table('registrations')->insert([
                'user_id'             => $userId,
                'student_id'          => $studentId,
                'jalur_id'            => $cand['jalur_id'],
                'gelombang_id'        => $gelombangId,
                'registration_number' => $regNumber,
                'academic_year'       => '2026/2027',
                'status'              => $cand['status'],
                'submitted_at'        => $submittedAt,
                'created_at'          => date('Y-m-d H:i:s', strtotime('-1 days')),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);
            $regId = $this->db->insertID();

            // I. Create Physical Dummy Files and database rows in `student_documents`
            // (Don't create for Draft candidate unless they uploaded some)
            if ($cand['status'] !== 'draft' || $cand['dapodik_percentage'] > 0) {
                $docTypes = ['kk', 'akta', 'foto', 'raport'];
                
                // Ensure target directory exists
                $subFolder = 'documents/' . $userId . '/';
                $targetDir = WRITEPATH . 'uploads/' . $subFolder;
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                foreach ($docTypes as $type) {
                    $fileName = $type . '_document_mock.png';
                    $filePath = 'uploads/' . $subFolder . $fileName;
                    $physicalFile = WRITEPATH . $filePath;

                    // Generate a valid PNG image physically
                    if (!file_exists($physicalFile)) {
                        $im = imagecreatetruecolor(400, 300);
                        // Indigo background
                        $bg = imagecolorallocate($im, 99, 102, 241);
                        imagefill($im, 0, 0, $bg);
                        
                        // White text
                        $fg = imagecolorallocate($im, 255, 255, 255);
                        $darkBg = imagecolorallocate($im, 79, 70, 229);
                        
                        // Title bar
                        imagefilledrectangle($im, 0, 0, 400, 60, $darkBg);
                        imagestring($im, 5, 20, 20, "SMART SPMB PRO — DUMMY ASSET", $fg);
                        
                        // Body text
                        imagestring($im, 4, 30, 100, "Pemilik: " . $cand['name'], $fg);
                        imagestring($im, 4, 30, 130, "Mime: image/png", $fg);
                        imagestring($im, 4, 30, 160, "Tipe Dokumen: " . strtoupper($type), $fg);
                        imagestring($im, 4, 30, 190, "Status Verifikasi: PENDING REVIEW", $fg);
                        imagestring($im, 4, 30, 240, "[PRODUK SIAP JUAL - DEMO OK]", $fg);
                        
                        imagepng($im, $physicalFile);
                        imagedestroy($im);
                    }

                    // Insert Document database row
                    // Status is 'approved' for Siswa 1, 2, 3, 'rejected' for Siswa 4, and 'pending' for others.
                    $docStatus = 'pending';
                    $rejectionReason = null;
                    $verifiedBy = null;
                    $verifiedAt = null;

                    if (in_array($cand['status'], ['accepted', 'rejected', 'verified'], true)) {
                        if ($cand['status'] === 'rejected') {
                            $docStatus = 'rejected';
                            $rejectionReason = 'Dokumen Kartu Keluarga buram dan tidak terbaca jelas.';
                            $verifiedBy = $operator1Id;
                            $verifiedAt = date('Y-m-d H:i:s');
                        } else {
                            $docStatus = 'approved';
                            $verifiedBy = $operator1Id;
                            $verifiedAt = date('Y-m-d H:i:s');
                        }
                    }

                    $this->db->table('student_documents')->insert([
                        'student_id'       => $studentId,
                        'document_type'    => $type,
                        'file_name'        => $fileName,
                        'file_path'        => $filePath,
                        'file_size'        => file_exists($physicalFile) ? filesize($physicalFile) : 1024,
                        'mime_type'        => 'image/png',
                        'status'           => $docStatus,
                        'rejection_reason' => $rejectionReason,
                        'verified_by'      => $verifiedBy,
                        'verified_at'      => $verifiedAt,
                        'created_at'       => date('Y-m-d H:i:s', strtotime('-12 hours')),
                        'updated_at'       => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // 3. Seed activity_logs (22 realistic rows)
        $logs = [
            ['user_id' => $adminId, 'action' => 'login', 'entity_type' => null, 'entity_id' => null, 'desc' => 'Admin berhasil melakukan login ke sistem.'],
            ['user_id' => $adminId, 'action' => 'backup', 'entity_type' => 'system', 'entity_id' => null, 'desc' => 'Admin membuat cadangan data database sistem.'],
            ['user_id' => $adminId, 'action' => 'edit_data', 'entity_type' => 'settings', 'entity_id' => 1, 'desc' => 'Admin memperbarui nama sekolah pada pengaturan.'],
            ['user_id' => $operator1Id, 'action' => 'login', 'entity_type' => null, 'entity_id' => null, 'desc' => 'Operator Utama login ke sistem.'],
            ['user_id' => 4, 'action' => 'login', 'entity_type' => null, 'entity_id' => null, 'desc' => 'Calon Siswa Budi Santoso login ke panel.'],
            ['user_id' => 4, 'action' => 'edit_data', 'entity_type' => 'students', 'entity_id' => 1, 'desc' => 'Budi Santoso menyelesaikan Langkah 1 Identitas.'],
            ['user_id' => 4, 'action' => 'edit_data', 'entity_type' => 'students', 'entity_id' => 1, 'desc' => 'Budi Santoso menyelesaikan Langkah 2 s.d 7 Wizard.'],
            ['user_id' => 4, 'action' => 'edit_data', 'entity_type' => 'documents', 'entity_id' => 1, 'desc' => 'Budi Santoso berhasil mengunggah berkas Kartu Keluarga.'],
            ['user_id' => 4, 'action' => 'edit_data', 'entity_type' => 'documents', 'entity_id' => 2, 'desc' => 'Budi Santoso berhasil mengunggah berkas Akta Kelahiran.'],
            ['user_id' => 4, 'action' => 'edit_data', 'entity_type' => 'documents', 'entity_id' => 3, 'desc' => 'Budi Santoso berhasil mengunggah berkas Pas Foto.'],
            ['user_id' => 4, 'action' => 'edit_data', 'entity_type' => 'registrations', 'entity_id' => 1, 'desc' => 'Budi Santoso melakukan finalisasi berkas pendaftaran (Submit).'],
            ['user_id' => $operator1Id, 'action' => 'verify_document', 'entity_type' => 'documents', 'entity_id' => 1, 'desc' => 'Operator menyetujui berkas Kartu Keluarga Budi Santoso.'],
            ['user_id' => $operator1Id, 'action' => 'verify_document', 'entity_type' => 'documents', 'entity_id' => 2, 'desc' => 'Operator menyetujui berkas Akta Kelahiran Budi Santoso.'],
            ['user_id' => $operator1Id, 'action' => 'verify_document', 'entity_type' => 'documents', 'entity_id' => 3, 'desc' => 'Operator menyetujui berkas Pas Foto Budi Santoso.'],
            ['user_id' => $operator1Id, 'action' => 'edit_data', 'entity_type' => 'registrations', 'entity_id' => 1, 'desc' => 'Sistem otomatis mengubah status pendaftaran Budi Santoso ke VERIFIED.'],
            ['user_id' => $adminId, 'action' => 'edit_data', 'entity_type' => 'registrations', 'entity_id' => 1, 'desc' => 'Panitia mengubah status pendaftaran Budi Santoso ke LULUS (Accepted).'],
            ['user_id' => 5, 'action' => 'login', 'entity_type' => null, 'entity_id' => null, 'desc' => 'Siti Aminah login ke panel.'],
            ['user_id' => 5, 'action' => 'edit_data', 'entity_type' => 'registrations', 'entity_id' => 2, 'desc' => 'Siti Aminah melakukan finalisasi berkas pendaftaran.'],
            ['user_id' => $operator1Id, 'action' => 'verify_document', 'entity_type' => 'documents', 'entity_id' => 13, 'desc' => 'Operator menolak berkas Kartu Keluarga Rina Wijaya.'],
            ['user_id' => $operator1Id, 'action' => 'export', 'entity_type' => 'registrations', 'entity_id' => null, 'desc' => 'Operator mengekspor daftar pendaftar ke format Excel.'],
        ];

        foreach ($logs as $log) {
            $this->db->table('activity_logs')->insert([
                'user_id'     => $log['user_id'],
                'action'      => $log['action'],
                'entity_type' => $log['entity_type'],
                'entity_id'   => $log['entity_id'],
                'old_value'   => null,
                'new_value'   => json_encode(['description' => $log['desc']]),
                'ip_address'  => '127.0.0.1',
                'user_agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/123.0.0.0',
                'created_at'  => date('Y-m-d H:i:s', strtotime('-' . rand(2, 60) . ' minutes')),
            ]);
        }
    }
}
