<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table      = 'announcements';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'title',
        'content',
        'published_at',
        'status',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title'      => 'required|max_length[200]',
        'content'    => 'required',
        'status'     => 'required|in_list[draft,published]',
        'created_by' => 'required|integer',
    ];

    /**
     * Ambil semua pengumuman yang berstatus published, diurutkan terbaru.
     */
    public function getPublished(): array
    {
        return $this->where('status', 'published')
                    ->orderBy('published_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil semua pengumuman (untuk admin), diurutkan terbaru.
     */
    public function getAllOrdered(): array
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Publish pengumuman (ubah status ke published dan set published_at).
     */
    public function publish(int $id): bool
    {
        return $this->update($id, [
            'status'       => 'published',
            'published_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Unpublish pengumuman (ubah status ke draft).
     */
    public function unpublish(int $id): bool
    {
        return $this->update($id, [
            'status'       => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Cari pengumuman berdasarkan kata kunci pada judul atau konten.
     */
    public function search(string $keyword): array
    {
        return $this->where('status', 'published')
                    ->groupStart()
                    ->like('title', $keyword)
                    ->orLike('content', $keyword)
                    ->groupEnd()
                    ->orderBy('published_at', 'DESC')
                    ->findAll();
    }
}
