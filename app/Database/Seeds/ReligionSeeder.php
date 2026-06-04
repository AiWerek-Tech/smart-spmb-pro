<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ReligionSeeder extends Seeder
{
    public function run(): void
    {
        $db = \Config\Database::connect();

        $religions = [
            'Islam' => [
                'Sunni',
                'Syiah',
                'Lainnya',
            ],
            'Kristen' => [
                'Protestan',
                'Lainnya',
            ],
            'Katolik' => [
                'Katolik Roma',
                'Lainnya',
            ],
            'Hindu' => [
                'Kaharingan',
                'Lainnya',
            ],
            'Buddha' => [
                'Theravada',
                'Mahayana',
                'Tantrayana',
                'Lainnya',
            ],
            'Konghucu' => [
                'Konghucu',
                'Lainnya',
            ],
        ];

        foreach ($religions as $relName => $subgroups) {
            // Check if religion exists
            $religion = $db->table('religions')->where('name', $relName)->get()->getRow();
            if (!$religion) {
                $db->table('religions')->insert([
                    'name'       => $relName,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $religionId = $db->insertID();
            } else {
                $religionId = $religion->id;
            }

            // Check if subgroups exist
            foreach ($subgroups as $subName) {
                $sub = $db->table('religion_subgroups')
                    ->where('religion_id', $religionId)
                    ->where('name', $subName)
                    ->get()
                    ->getRow();

                if (!$sub) {
                    $db->table('religion_subgroups')->insert([
                        'religion_id' => $religionId,
                        'name'        => $subName,
                        'created_at'  => date('Y-m-d H:i:s'),
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }
}
