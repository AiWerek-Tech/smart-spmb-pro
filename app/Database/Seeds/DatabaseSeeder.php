<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Master seeder — menjalankan semua seeder secara berurutan.
 * Jalankan dengan: php spark db:seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('JalurSeeder');
        $this->call('SettingsSeeder');
        $this->call('AdminUserSeeder');
    }
}
