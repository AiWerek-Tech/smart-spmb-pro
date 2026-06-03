<?php

namespace App\Filters;

use App\Services\AuditLogService;
use App\Services\PermissionService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return redirect()->to(base_url('auth/login'));
        }

        $permissionKeys = $this->parsePermissions($arguments);
        if ($permissionKeys === []) {
            return $this->denyAccess('Permission route belum dikonfigurasi.');
        }

        if (! service('rbacEngine')->hasAnyPermission($userId, $permissionKeys) && ! $this->legacyRoleAllows($permissionKeys)) {
            (new AuditLogService())->record('keamanan', 'UNAUTHORIZED_ACCESS_ATTEMPT', [
                'entity_type' => 'route',
                'new_data'    => [
                    'uri'                  => (string) current_url(),
                    'required_permissions' => $permissionKeys,
                ],
            ]);

            return $this->denyAccess('Anda tidak memiliki izin untuk mengakses halaman ini.', $permissionKeys);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    /**
     * @param array|null $arguments
     * @return string[]
     */
    private function parsePermissions(?array $arguments): array
    {
        $permissionKeys = [];
        foreach ($arguments ?? [] as $argument) {
            foreach (explode(',', (string) $argument) as $permissionKey) {
                $permissionKey = trim($permissionKey);
                if ($permissionKey !== '') {
                    $permissionKeys[] = $permissionKey;
                }
            }
        }

        return array_values(array_unique($permissionKeys));
    }

    private function denyAccess(string $message, array $requiredPermissions = []): ResponseInterface|RedirectResponse
    {
        $response = service('response');
        $response->setStatusCode(403);
        $response->setBody(view('errors/403', [
            'message'             => $message,
            'requiredPermissions' => $requiredPermissions,
        ]));

        return $response;
    }

    private function legacyRoleAllows(array $permissionKeys): bool
    {
        $roleSlug = (string) session()->get('user_role');
        if ($roleSlug === '') {
            return false;
        }

        $permissionService = new PermissionService();
        foreach ($permissionKeys as $permissionKey) {
            if ($permissionService->has($roleSlug, $permissionKey)) {
                return true;
            }
        }

        return false;
    }
}
