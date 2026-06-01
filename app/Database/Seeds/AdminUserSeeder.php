<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk akun admin default.
 * PENTING: Ganti password setelah instalasi pertama!
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $existing = $this->db->table('users')
            ->where('email', 'admin@smartspmbpro.sch.id')
            ->countAllResults();

        if ($existing === 0) {
            $this->db->table('users')->insert([
                'name'       => 'Administrator',
                'email'      => 'admin@smartspmbpro.sch.id',
                'password'   => password_hash('Admin@12345', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
