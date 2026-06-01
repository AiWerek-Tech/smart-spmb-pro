<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceHomepageContent extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('gallery')) {
            $fields = $this->db->getFieldNames('gallery');

            if (!in_array('description', $fields, true)) {
                $this->forge->addColumn('gallery', [
                    'description' => [
                        'type' => 'TEXT',
                        'null' => true,
                        'after' => 'title',
                    ],
                ]);
            }

            if (!in_array('media_type', $fields, true)) {
                $this->forge->addColumn('gallery', [
                    'media_type' => [
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'null' => false,
                        'default' => 'photo',
                        'after' => 'category',
                    ],
                ]);
            }

            if (!in_array('video_url', $fields, true)) {
                $this->forge->addColumn('gallery', [
                    'video_url' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true,
                        'after' => 'media_type',
                    ],
                ]);
            }

            if (!in_array('video_provider', $fields, true)) {
                $this->forge->addColumn('gallery', [
                    'video_provider' => [
                        'type' => 'VARCHAR',
                        'constraint' => 50,
                        'null' => true,
                        'after' => 'video_url',
                    ],
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!$this->db->tableExists('gallery')) {
            return;
        }

        $table = $this->db->protectIdentifiers('gallery');
        foreach (['video_provider', 'video_url', 'media_type', 'description'] as $field) {
            $exists = $this->db->query('SHOW COLUMNS FROM ' . $table . ' LIKE ' . $this->db->escape($field))->getNumRows() > 0;
            if ($exists) {
                $this->db->query('ALTER TABLE ' . $table . ' DROP COLUMN ' . $this->db->protectIdentifiers($field));
            }
        }
    }
}
