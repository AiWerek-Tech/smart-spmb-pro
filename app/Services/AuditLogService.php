<?php

namespace App\Services;

use App\Models\AuditLogModel;

class AuditLogService
{
    public function __construct(private ?AuditLogModel $auditLogModel = null)
    {
        $this->auditLogModel ??= new AuditLogModel();
    }

    public function record(string $module, string $action, array $options = []): bool
    {
        try {
            $userId = (int) ($options['user_id'] ?? session()->get('user_id') ?? 0);
            $userName = (string) ($options['user_name'] ?? session()->get('user_name') ?? 'System');
            $roleSnapshot = $options['role_snapshot'] ?? session()->get('user_roles') ?? [];
            $request = service('request');
            $userAgent = method_exists($request, 'getUserAgent') ? $request->getUserAgent()->getAgentString() : null;

            return $this->auditLogModel->log([
                'user_id'       => $userId > 0 ? $userId : null,
                'user_name'     => $userName !== '' ? $userName : 'System',
                'role_snapshot' => $roleSnapshot,
                'module'        => $module,
                'action'        => $action,
                'entity_type'   => $options['entity_type'] ?? null,
                'entity_id'     => $options['entity_id'] ?? null,
                'old_data'      => $options['old_data'] ?? null,
                'new_data'      => $options['new_data'] ?? null,
                'ip_address'    => method_exists($request, 'getIPAddress') ? $request->getIPAddress() : null,
                'user_agent'    => $userAgent,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Audit log failed: ' . $e->getMessage());

            return false;
        }
    }
}
