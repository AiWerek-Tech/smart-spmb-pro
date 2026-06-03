<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class AddImageTagToAnnouncements extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('announcements')) {
            return;
        }

        // Tambah kolom image dan tag ke tabel announcements
        $fields = [];

        if (! $this->db->fieldExists('image', 'announcements')) {
            $fields['image'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'content',
            ];
        }

        if (! $this->db->fieldExists('tag', 'announcements')) {
            $fields['tag'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'INFO',
                'after'      => 'image',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('announcements', $fields);
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('announcements')) {
            return;
        }

        foreach (['image', 'tag'] as $column) {
            if ($this->db->fieldExists($column, 'announcements')) {
                try {
                    $this->forge->dropColumn('announcements', $column);
                } catch (DatabaseException) {
                }
            }
        }
    }
}
