<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table      = 'faqs';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'question',
        'answer',
        'sort_order',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'question' => 'required',
        'answer'   => 'required',
    ];

    /**
     * Ambil semua FAQ yang aktif, diurutkan berdasarkan sort_order.
     */
    public function getActiveFaqs(): array
    {
        return $this->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil semua FAQ (untuk admin), diurutkan berdasarkan sort_order.
     */
    public function getAllOrdered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->findAll();
    }

    /**
     * Toggle status aktif FAQ.
     */
    public function toggleActive(int $id): bool
    {
        $faq = $this->find($id);
        if ($faq === null) {
            return false;
        }

        return $this->update($id, ['is_active' => $faq['is_active'] ? 0 : 1]);
    }

    /**
     * Perbarui urutan FAQ.
     */
    public function updateSortOrder(int $id, int $sortOrder): bool
    {
        return $this->update($id, ['sort_order' => $sortOrder]);
    }

    /**
     * Cari FAQ berdasarkan kata kunci pada pertanyaan atau jawaban.
     */
    public function search(string $keyword): array
    {
        return $this->where('is_active', 1)
                    ->groupStart()
                    ->like('question', $keyword)
                    ->orLike('answer', $keyword)
                    ->groupEnd()
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }
}
