<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table      = 'activity_logs';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    // activity_logs hanya memiliki created_at (tidak ada updated_at)
    protected $useTimestamps = false;

    protected $validationRules = [
        'action' => 'required|max_length[100]',
    ];

    /**
     * Catat aktivitas baru.
     */
    public function log(
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        mixed $oldValue = null,
        mixed $newValue = null
    ): bool {
        $request = \Config\Services::request();

        return $this->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_value'   => $oldValue !== null ? json_encode($oldValue) : null,
            'new_value'   => $newValue !== null ? json_encode($newValue) : null,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => $request->getUserAgent()->getAgentString(),
            'created_at'  => date('Y-m-d H:i:s'),
        ]) !== false;
    }

    /**
     * Ambil log aktivitas berdasarkan user_id.
     */
    public function findByUserId(int $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Ambil log aktivitas berdasarkan entity.
     */
    public function findByEntity(string $entityType, int $entityId): array
    {
        return $this->where('entity_type', $entityType)
                    ->where('entity_id', $entityId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil log aktivitas terbaru dengan informasi pengguna.
     */
    public function getRecentLogs(int $limit = 100): array
    {
        return $this->select('activity_logs.*, users.name AS user_name, users.role AS user_role')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Ambil log berdasarkan aksi tertentu.
     */
    public function findByAction(string $action, int $limit = 50): array
    {
        return $this->where('action', $action)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
