<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageTagToAnnouncements extends Migration
{
    public function up(): void
    {
        // Tambah kolom image dan tag ke tabel announcements
        $fields = [
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'content',
            ],
            'tag' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'INFO',
                'after'      => 'image',
            ],
        ];

        $this->forge->addColumn('announcements', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('announcements', ['image', 'tag']);
    }
}
